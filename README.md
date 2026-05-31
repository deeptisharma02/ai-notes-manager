# AI Notes Manager

A Laravel-backed AI notes management system with secure REST APIs, semantic vector search, and AI summary generation.

## Features

- Notes CRUD API
- Pagination support
- AI-powered semantic search using OpenAI embeddings
- AI summary generation endpoint
- Simple frontend UI for note creation, search, and summaries
- Docker setup for PHP and MySQL
- Unit tests for note API flows

## Local Setup

### Prerequisites

- Docker and Docker Compose
- OpenAI API key (for AI summaries and semantic search)

### Run locally

1. Copy environment example:

```bash
cp .env.example .env
```

2. Set `OPENAI_API_KEY` in `.env`.

3. Start services:

```bash
docker compose up --build
```

4. Run migrations inside the app container:

```bash
docker compose exec app php artisan migrate
```

5. Open `http://localhost:8000` to use the frontend UI.

## API Endpoints

- `GET /api/notes?page=1&limit=10`
- `GET /api/notes/{id}`
- `POST /api/notes`
- `PUT /api/notes/{id}`
- `DELETE /api/notes/{id}`
- `POST /api/notes/{id}/summary`
- `GET /api/notes/search?q=your query`

### Example: create note

```bash
curl -X POST http://localhost:8000/api/notes \
  -H "Content-Type: application/json" \
  -d '{"title":"Demo note","content":"This is a sample AI-driven note."}'
```

### Example: semantic search

```bash
curl "http://localhost:8000/api/notes/search?q=AI%20summary"
```

## Database Schema

- `notes`
  - `id`
  - `title`
  - `content`
  - `summary`
  - `embedding`
  - `created_at`
  - `updated_at`

## AI Integration

- `OpenAiService` handles both embeddings and summary generation.
- Note creation and update store embeddings for later semantic search.
- `POST /api/notes/{id}/summary` creates an AI-powered summary.
- Search uses cosine similarity over stored embeddings when OpenAI is available, otherwise keyword fallback.

## AI Usage

This project used AI-assisted development to design the frontend UI and to structure the AI service layer.

- Frontend UI: simple JavaScript-based interface to consume the notes API.
- AI summary & embeddings: OpenAI `chat.completions` and `embeddings` endpoints.

## Test Suite

Run tests with:

```bash
docker compose exec app php artisan test
```

## Security

- Request validation with Laravel form requests
- JSON responses with proper HTTP status codes
- Rate limiting applied to API routes
- SQL injection prevention via Eloquent and query binding

## Notes

If `OPENAI_API_KEY` is not configured, the summary endpoint will fall back to a local summary heuristic and search uses keyword fallbacks.
