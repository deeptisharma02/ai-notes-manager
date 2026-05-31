# 🧠 AI Notes Manager

A production-style **AI-powered Notes Management System** built with **Laravel 13**.
It exposes a clean REST API for note CRUD, adds **AI semantic search** (OpenAI
embeddings + cosine similarity) and **AI summaries**, and ships with a polished
single-page frontend, Docker setup, Redis, OpenAPI docs and tests.

> Live locally at **http://localhost:8000** · API docs at **http://localhost:8000/api/docs**

---

## ✨ Features

- **Notes CRUD API** — create, read, update, delete, list
- **Pagination** — `GET /api/notes?page=1&limit=10`
- **AI Semantic Search** — OpenAI embeddings + cosine similarity, with automatic keyword fallback
- **AI Summaries** — `POST /api/notes/{id}/summary`, with a local extractive fallback
- **Validation** — Form Request classes, clean `422` error responses
- **Security** — rate limiting (60 req/min), input validation, SQL-injection-safe (Eloquent/bindings)
- **Professional frontend UI** — clean light theme, semantic/keyword badges, AI summary blocks
- **Bonus** — 🐳 Docker · ⚡ Redis (cache + sessions) · 📘 Swagger/OpenAPI · ✅ Feature tests · 🚀 OPcache

---

## 🧱 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13 (PHP 8.4) |
| Database | MySQL 8 |
| Cache / Sessions | Redis 7 (via `predis`) |
| AI | OpenAI Chat Completions + Embeddings |
| Frontend | Blade + vanilla JS (single page) |
| Docs | OpenAPI 3.0 + Swagger UI |
| Infra | Docker + Docker Compose |

---

## 🏗️ Architecture

```
                 ┌────────────────────────────┐
   Browser  ───► │  Frontend (notes.blade.php) │  vanilla JS, fetch()
                 └──────────────┬─────────────┘
                                │  JSON over HTTP
                 ┌──────────────▼─────────────┐
                 │   routes/api.php (REST)     │  throttle: 60/min
                 └──────────────┬─────────────┘
                                │
                 ┌──────────────▼─────────────┐
                 │      NoteController          │  validation, HTTP codes
                 │  (StoreNote/UpdateNote Req)  │
                 └───────┬───────────────┬──────┘
                         │               │
            ┌────────────▼───┐    ┌──────▼────────────┐
            │  Note (Eloquent)│    │  OpenAiService     │
            │   → MySQL        │    │  embeddings + chat │ ──► OpenAI API
            └─────────────────┘    └────────────────────┘
                         │
                 ┌───────▼────────┐
                 │  Redis (cache,  │  rate limiter + sessions
                 │   sessions)     │
                 └─────────────────┘
```

**Layering / clean architecture:** HTTP concerns live in the controller, validation in
Form Requests, all AI/third-party logic is isolated in `App\Services\OpenAiService`, and
persistence is handled by the `Note` Eloquent model. The service is dependency-injected,
so it is trivially swappable/mockable.

---

## 🚀 Setup (Docker — recommended)

### Prerequisites
- Docker + Docker Compose
- (Optional) An OpenAI API key for real semantic search & summaries

### Steps

```bash
# 1. Copy env
cp .env.example .env

# 2. (Optional) add your OpenAI key in .env
#    OPENAI_API_KEY=sk-...

# 3. Build & start (app + MySQL + Redis)
docker compose up --build -d

# 4. App key + database
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force

# 5. Open
#    UI    → http://localhost:8000
#    Docs  → http://localhost:8000/api/docs
```

> **Note on AI:** without `OPENAI_API_KEY`, summaries use a local extractive fallback
> and search uses keyword matching. Add a key and run `docker compose restart app`
> to enable true OpenAI semantic search + summaries.

> **Note on OPcache:** for speed, OPcache runs with `validate_timestamps=0`. After
> editing PHP code, run `docker compose restart app` to load the changes.

---

## 📚 API Documentation

Interactive Swagger UI: **http://localhost:8000/api/docs** (spec at `/openapi.yaml`).

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notes?page=1&limit=10` | List notes (paginated) |
| POST | `/api/notes` | Create a note |
| GET | `/api/notes/{id}` | Get a single note |
| PUT | `/api/notes/{id}` | Update a note |
| DELETE | `/api/notes/{id}` | Delete a note (204) |
| GET | `/api/notes/search?q=...` | Semantic / keyword search |
| POST | `/api/notes/{id}/summary` | Generate an AI summary |

### Examples

```bash
# Create
curl -X POST http://localhost:8000/api/notes \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"title":"Project kickoff","content":"We discussed the architecture and timeline."}'

# List (paginated)
curl "http://localhost:8000/api/notes?page=1&limit=10" -H "Accept: application/json"

# Semantic search
curl "http://localhost:8000/api/notes/search?q=meeting%20timeline" -H "Accept: application/json"

# AI summary
curl -X POST http://localhost:8000/api/notes/1/summary -H "Accept: application/json"
```

Success responses are wrapped in `{ "data": ... }`; lists add a `meta` pagination block.
Validation failures return `422` with a `{ "message", "errors" }` body.

---

## 🗄️ Database Schema

**`notes`**

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint (PK) | auto-increment |
| `title` | string | indexed |
| `content` | text | |
| `summary` | text, nullable | AI-generated |
| `embedding` | json, nullable | OpenAI vector for semantic search |
| `created_at` | timestamp | indexed |
| `updated_at` | timestamp | |

---

## 🔍 Semantic Search — how it works

1. On create/update, the note content is sent to OpenAI's embeddings endpoint
   (`text-embedding-3-small`) and the resulting vector is stored in `embedding`.
2. On search, the query is embedded and compared to every note's vector using
   **cosine similarity**; results are ranked by `similarity_score`.
3. If no API key is set (or no embeddings exist), the API transparently falls
   back to SQL `LIKE` keyword search. The response `source` field (`semantic` |
   `keyword`) tells the client which path ran.

---

## 🤖 AI Integration, Prompts & Validation

All AI logic lives in [`app/Services/OpenAiService.php`](app/Services/OpenAiService.php).

**Summary prompt used:**
- *System:* `You are an assistant that summarizes notes clearly and concisely.`
- *User:* `Summarize the following note content in 2-3 sentences, focusing on key points and meaning:\n\n{content}`
- Params: `temperature: 0.2`, `max_tokens: 200` (deterministic, concise).

**Embeddings:** model `text-embedding-3-small`, input = note content.

**Where AI was used to build this project (AI-assisted development):**
- Generating the frontend UI (Blade + JS) and styling.
- Scaffolding the service layer, OpenAPI spec, and tests.

**How AI-generated code was validated:**
- Wrote and ran **feature tests** covering CRUD, search, validation and summaries (`php artisan test`).
- Manually exercised **every endpoint** end-to-end via `curl` and the UI, checking status codes and payloads.
- Verified graceful **fallbacks** (no API key → local summary + keyword search) so the app never hard-fails on AI errors.

---

## 🔐 Security

- **Validation** — `StoreNoteRequest` / `UpdateNoteRequest` reject bad input with `422`.
- **Rate limiting** — `throttle:60,1` (60 requests/min) on all API routes, backed by Redis.
- **SQL injection** — all DB access via Eloquent / parameter binding; no raw concatenated SQL.
- **Output escaping** — frontend escapes all user content before rendering.
- **Stateless API** — API routes live in `routes/api.php` (no session/CSRF coupling).

---

## ⚡ Performance

- **Redis** for cache, rate-limiter and sessions.
- **OPcache** enabled (`docker/php/opcache.ini`) with realpath caching — cut request
  latency from ~3–15s to ~0.1–0.3s on Docker bind mounts.

---

## ✅ Tests

```bash
docker compose exec app php artisan test
```

Feature tests cover create + list, update + delete, search, and summary generation.

---

## 📸 Screenshots

See [`screenshots/`](screenshots/) — home, create, AI summary, search, and Swagger UI.

---

## 📂 Key Files

| Path | Responsibility |
|------|----------------|
| `routes/api.php` | API route definitions + rate limiting |
| `app/Http/Controllers/NoteController.php` | CRUD, search, summary logic |
| `app/Http/Requests/*` | Validation rules |
| `app/Services/OpenAiService.php` | OpenAI embeddings + summaries (+ fallbacks) |
| `app/Models/Note.php` | Eloquent model (embedding cast to array) |
| `resources/views/notes.blade.php` | Frontend UI |
| `resources/views/docs.blade.php` | Swagger UI |
| `public/openapi.yaml` | OpenAPI 3.0 spec |
| `docker-compose.yml`, `Dockerfile` | Container setup (app + MySQL + Redis) |
