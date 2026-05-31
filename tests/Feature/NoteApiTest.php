<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_and_list_notes(): void
    {
        $response = $this->postJson('/api/notes', [
            'title' => 'Build demo',
            'content' => 'Implement a note API with AI search and summary.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Build demo');

        $this->getJson('/api/notes')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_update_and_delete_note(): void
    {
        $note = Note::factory()->create();

        $this->putJson("/api/notes/{$note->id}", [
            'title' => 'Updated title',
        ])->assertStatus(200)
          ->assertJsonPath('data.title', 'Updated title');

        $this->deleteJson("/api/notes/{$note->id}")
            ->assertStatus(204);

        $this->getJson("/api/notes/{$note->id}")
            ->assertStatus(404);
    }

    public function test_can_search_notes_with_query(): void
    {
        Note::factory()->create(['title' => 'Laravel Notes', 'content' => 'AI search and summary demo.']);
        Note::factory()->create(['title' => 'Shopping list', 'content' => 'Buy milk and eggs.']);

        $this->getJson('/api/notes/search?q=AI')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_generate_summary_for_note(): void
    {
        $note = Note::factory()->create(['content' => 'This is a note for summary testing. It should produce a short summary automatically.']);

        $this->postJson("/api/notes/{$note->id}/summary")
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'summary', 'source']]);
    }
}
