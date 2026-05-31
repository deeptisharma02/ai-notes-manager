<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAiService
{
    private string $apiKey;
    private string $model;
    private string $embeddingModel;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openai.key', '');
        $this->model = (string) config('services.openai.model', 'gpt-4o-mini');
        $this->embeddingModel = (string) config('services.openai.embedding_model', 'text-embedding-3-small');
    }

    public function hasApiKey(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateSummary(string $content): string
    {
        if (! $this->hasApiKey()) {
            return $this->generateFallbackSummary($content);
        }

        $prompt = "Summarize the following note content in 2-3 sentences, focusing on key points and meaning: \n\n" . $content;

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an assistant that summarizes notes clearly and concisely.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.2,
                'max_tokens' => 200,
            ]);

        if (! $response->successful()) {
            return $this->generateFallbackSummary($content);
        }

        return trim($response->json('choices.0.message.content', $this->generateFallbackSummary($content)));
    }

    public function embedText(string $text): ?array
    {
        if (! $this->hasApiKey()) {
            return null;
        }

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/embeddings', [
                'model' => $this->embeddingModel,
                'input' => $text,
            ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('data.0.embedding');
    }

    private function generateFallbackSummary(string $content): string
    {
        $content = trim(preg_replace('/\s+/', ' ', $content));
        if ($content === '') {
            return '';
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $content, 3, PREG_SPLIT_NO_EMPTY);
        if (count($sentences) >= 2) {
            return implode(' ', array_slice($sentences, 0, 2));
        }

        return str($content)->limit(200)->toString();
    }
}
