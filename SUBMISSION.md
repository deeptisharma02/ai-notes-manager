# Submission — AI Notes Management System

**Candidate:** Deepti Sharma
**Role:** PHP Backend Developer (Laravel / CodeIgniter) with AI-Assisted Development Skills
**Repository:** https://github.com/deeptisharma02/ai-notes-manager
**Stack:** Laravel 13 · PHP 8.4 · MySQL 8 · Redis 7 · OpenAI · Docker

---

## How to run

```bash
docker compose up --build -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

- App UI: http://localhost:8000
- API docs (Swagger): http://localhost:8000/api/docs

> Semantic search and summaries work out of the box via fallbacks. Adding
> `OPENAI_API_KEY` to `.env` (then `docker compose restart app`) enables full
> OpenAI-powered semantic search and summaries.

---

## Requirements checklist

### Core features
| Requirement | Status | Where |
|-------------|--------|-------|
| Create note | Done | `POST /api/notes` |
| Get single note | Done | `GET /api/notes/{id}` |
| Get notes list | Done | `GET /api/notes` |
| Update note | Done | `PUT /api/notes/{id}` |
| Delete note | Done | `DELETE /api/notes/{id}` |
| Validation | Done | `StoreNoteRequest`, `UpdateNoteRequest` |
| Proper HTTP status codes | Done | 200 / 201 / 204 / 404 / 422 |
| Clean JSON responses | Done | `{ data, meta }` envelope |
| Pagination | Done | `GET /api/notes?page=1&limit=10` |
| Semantic search (AI) | Done | `GET /api/notes/search?q=` (embeddings + cosine) |
| AI summary endpoint | Done | `POST /api/notes/{id}/summary` |
| AI-generated frontend UI | Done | `resources/views/notes.blade.php` |

### Security
| Requirement | Status | How |
|-------------|--------|-----|
| SQL injection prevention | Done | Eloquent ORM / parameter binding only |
| API validation | Done | Laravel Form Requests, `422` errors |
| Rate limiting | Done | `throttle:60,1` (60 req/min), backed by Redis |
| Secure API handling | Done | Stateless API routes, output escaping in UI |

### Bonus points
| Bonus | Status |
|-------|--------|
| Docker setup | Done (app + MySQL + Redis) |
| Unit / feature tests | Done (all passing) |
| Swagger / OpenAPI docs | Done (`/api/docs`, OpenAPI 3.0) |
| Redis usage | Done (cache + rate limiter + sessions) |
| Clean architecture | Done (controller / form request / service / model layers) |

---

## Architecture

```
Browser (notes.blade.php)
        │ JSON / fetch()
routes/api.php  ── throttle 60/min ──►  NoteController
                                          │        │
                              Note (Eloquent→MySQL)  OpenAiService ──► OpenAI
                                          │
                                  Redis (cache, rate limiter, sessions)
```

- HTTP concerns: `NoteController`
- Validation: `StoreNoteRequest` / `UpdateNoteRequest`
- All AI / third-party logic isolated in `App\Services\OpenAiService` (dependency-injected, mockable)
- Persistence: `Note` Eloquent model (`embedding` cast to array)

---

## AI integration

**Semantic search:** note content is embedded with `text-embedding-3-small` on
create/update and stored in the `embedding` column. On search, the query is
embedded and ranked against all notes by cosine similarity. Falls back to SQL
`LIKE` keyword search when no key/embeddings exist (`source` field signals which ran).

**Summaries — prompt used:**
- System: `You are an assistant that summarizes notes clearly and concisely.`
- User: `Summarize the following note content in 2-3 sentences, focusing on key points and meaning:\n\n{content}`
- Params: `temperature: 0.2`, `max_tokens: 200`

---

## AI usage (assisted development) & validation

**Where AI was used to build this:** generating the frontend UI, scaffolding the
service layer, the OpenAPI spec, and tests.

**How the AI-generated code was validated:**
- Wrote and ran feature tests for CRUD, search, validation, and summaries (`php artisan test` — all passing).
- Manually exercised every endpoint end-to-end via `curl` and the UI, verifying status codes and payloads.
- Verified graceful fallbacks (no API key → local summary + keyword search) so the app never hard-fails.

---

## Database schema — `notes`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | auto-increment |
| title | string | indexed |
| content | text | |
| summary | text, nullable | AI-generated |
| embedding | json, nullable | OpenAI vector for semantic search |
| created_at / updated_at | timestamp | created_at indexed |

---

Full details, examples, and screenshots are in the [README](README.md).
