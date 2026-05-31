<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use App\Services\OpenAiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    public function __construct(private OpenAiService $ai)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $limit = $limit > 0 && $limit <= 100 ? $limit : 10;

        $notes = Note::latest()->paginate($limit);

        return response()->json([
            'data' => $notes->items(),
            'meta' => [
                'current_page' => $notes->currentPage(),
                'per_page' => $notes->perPage(),
                'total' => $notes->total(),
                'last_page' => $notes->lastPage(),
            ],
        ]);
    }

    public function store(StoreNoteRequest $request): JsonResponse
    {
        $note = Note::create($request->validated());

        $embedding = $this->ai->embedText($note->content);
        if ($embedding !== null) {
            $note->embedding = $embedding;
            $note->save();
        }

        return response()->json(['data' => $note], 201);
    }

    public function show(Note $note): JsonResponse
    {
        return response()->json(['data' => $note]);
    }

    public function update(UpdateNoteRequest $request, Note $note): JsonResponse
    {
        $note->fill($request->validated());

        if ($request->filled('content')) {
            $note->embedding = $this->ai->embedText($note->content);
        }

        $note->save();

        return response()->json(['data' => $note]);
    }

    public function destroy(Note $note): JsonResponse
    {
        $note->delete();

        return response()->json([], 204);
    }

    public function summary(Note $note): JsonResponse
    {
        $summary = $this->ai->generateSummary($note->content);
        $note->summary = $summary;
        $note->save();

        return response()->json(['data' => [
            'id' => $note->id,
            'summary' => $note->summary,
            'source' => $this->ai->hasApiKey() ? 'openai' : 'fallback',
        ]]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim($request->query('q', ''));
        if ($query === '') {
            return response()->json(['message' => 'Search query is required.'], 422);
        }

        $limit = (int) $request->query('limit', 10);
        $limit = $limit > 0 && $limit <= 100 ? $limit : 10;

        if ($this->ai->hasApiKey() && Note::whereNotNull('embedding')->exists()) {
            $queryEmbedding = $this->ai->embedText($query);
            if ($queryEmbedding !== null) {
                $notes = Note::all()->map(function (Note $note) use ($queryEmbedding) {
                    $note->similarity_score = $this->cosineSimilarity($queryEmbedding, $note->embedding ?? []);
                    return $note;
                })->sortByDesc('similarity_score')->values()->take($limit);

                return response()->json(['data' => $notes, 'source' => 'semantic']);
            }
        }

        $notes = Note::query()
            ->where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json(['data' => $notes, 'source' => 'keyword']);
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        if (empty($a) || empty($b)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $length = min(count($a), count($b));
        for ($i = 0; $i < $length; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        $denom = sqrt($normA) * sqrt($normB);

        return $denom > 0 ? $dot / $denom : 0.0;
    }
}
