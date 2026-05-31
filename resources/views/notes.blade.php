<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Notes Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0b1020;
            --card: rgba(255, 255, 255, 0.06);
            --card-border: rgba(255, 255, 255, 0.12);
            --text: #eef2ff;
            --muted: #9aa6c4;
            --brand: #6366f1;
            --brand-2: #22d3ee;
            --accent: #f472b6;
            --green: #34d399;
            --red: #fb7185;
            --shadow: 0 24px 60px rgba(2, 6, 23, 0.55);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            background: var(--bg);
            overflow-x: hidden;
            position: relative;
        }

        /* Animated aurora background */
        .aurora { position: fixed; inset: 0; z-index: -2; overflow: hidden; }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.55;
            animation: float 18s ease-in-out infinite;
        }
        .blob.b1 { width: 520px; height: 520px; background: #6366f1; top: -120px; left: -80px; }
        .blob.b2 { width: 460px; height: 460px; background: #22d3ee; top: 20%; right: -120px; animation-delay: -4s; }
        .blob.b3 { width: 480px; height: 480px; background: #f472b6; bottom: -160px; left: 30%; animation-delay: -9s; }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(40px, -50px) scale(1.1); }
            66% { transform: translate(-30px, 30px) scale(0.95); }
        }
        /* subtle grid overlay */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: -1;
            background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(ellipse at 50% 0%, black, transparent 75%);
        }

        main { max-width: 1040px; margin: 0 auto; padding: 2.5rem 1.25rem 4rem; }

        header { text-align: center; margin-bottom: 2.25rem; animation: dropIn .7s cubic-bezier(.2,.8,.2,1) both; }
        .badge-ai {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .4rem .85rem; border-radius: 999px;
            background: rgba(99,102,241,.16); border: 1px solid rgba(99,102,241,.35);
            color: #c7d2fe; font-size: .8rem; font-weight: 600; letter-spacing: .02em;
            margin-bottom: 1rem;
        }
        .dot-pulse { width: 8px; height: 8px; border-radius: 50%; background: var(--brand-2); box-shadow: 0 0 0 0 rgba(34,211,238,.7); animation: pulse 1.8s infinite; }
        @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(34,211,238,.7);} 70%{box-shadow:0 0 0 10px rgba(34,211,238,0);} 100%{box-shadow:0 0 0 0 rgba(34,211,238,0);} }

        h1 {
            font-family: 'Sora', sans-serif;
            margin: 0; font-size: clamp(2.2rem, 5vw, 3.4rem); font-weight: 800; line-height: 1.05;
            background: linear-gradient(100deg, #fff 10%, #a5b4fc 45%, #67e8f9 80%);
            -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }
        header p { color: var(--muted); margin: .9rem 0 0; font-size: 1.05rem; }

        .stats { display: flex; gap: .75rem; justify-content: center; margin-top: 1.4rem; flex-wrap: wrap; }
        .stat {
            display: flex; align-items: center; gap: .6rem;
            padding: .6rem 1rem; border-radius: 14px;
            background: var(--card); border: 1px solid var(--card-border); backdrop-filter: blur(10px);
        }
        .stat b { font-family: 'Sora', sans-serif; font-size: 1.05rem; }
        .stat span { color: var(--muted); font-size: .82rem; }

        .card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 22px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
            margin-bottom: 1.25rem;
        }

        .composer { animation: dropIn .7s .1s cubic-bezier(.2,.8,.2,1) both; }
        .section-title { display: flex; align-items: center; gap: .6rem; font-family:'Sora',sans-serif; font-weight: 700; font-size: 1.15rem; margin: 0 0 1.1rem; }
        .section-title svg { color: var(--brand-2); }

        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; }
        @media (max-width: 720px) { .grid2 { grid-template-columns: 1fr; } }

        label { display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem; color: var(--muted); letter-spacing: .03em; text-transform: uppercase; }

        input, textarea {
            width: 100%; font: inherit; color: var(--text);
            background: rgba(255,255,255,.04);
            border: 1px solid var(--card-border); border-radius: 14px;
            padding: .85rem 1rem; transition: border-color .2s, box-shadow .2s, background .2s;
        }
        input::placeholder, textarea::placeholder { color: #64708f; }
        input:focus, textarea:focus {
            outline: none; border-color: var(--brand);
            background: rgba(99,102,241,.08);
            box-shadow: 0 0 0 4px rgba(99,102,241,.18);
        }
        textarea { resize: vertical; min-height: 130px; line-height: 1.55; }

        .search-row { display: flex; gap: .6rem; }
        .search-row input { flex: 1; }

        button {
            font: inherit; font-weight: 600; cursor: pointer; border: none;
            border-radius: 14px; padding: .85rem 1.3rem; color: #fff;
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
            display: inline-flex; align-items: center; gap: .5rem;
        }
        button:active { transform: scale(.96); }
        button:disabled { opacity: .6; cursor: not-allowed; }
        .btn-primary { background: linear-gradient(120deg, var(--brand), #818cf8); box-shadow: 0 10px 26px rgba(99,102,241,.4); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 34px rgba(99,102,241,.55); }
        .btn-cyan { background: linear-gradient(120deg, #0891b2, var(--brand-2)); box-shadow: 0 10px 26px rgba(34,211,238,.3); }
        .btn-cyan:hover { transform: translateY(-2px); }
        .btn-ghost { background: rgba(255,255,255,.06); border: 1px solid var(--card-border); }
        .btn-ghost:hover { background: rgba(255,255,255,.12); }
        .actions-row { display: flex; justify-content: flex-end; gap: .75rem; margin-top: 1.2rem; }

        .list-head { display: flex; align-items: center; justify-content: space-between; margin: 2rem .3rem 1rem; }
        .list-head h2 { font-family:'Sora',sans-serif; margin: 0; font-size: 1.3rem; }
        .src-badge { font-size: .72rem; font-weight: 700; padding: .3rem .7rem; border-radius: 999px; letter-spacing: .04em; text-transform: uppercase; }
        .src-semantic { background: rgba(52,211,153,.15); color: #6ee7b7; border: 1px solid rgba(52,211,153,.4); }
        .src-keyword { background: rgba(250,204,21,.14); color: #fde047; border: 1px solid rgba(250,204,21,.35); }

        /* Note cards */
        .note {
            position: relative; overflow: hidden;
            animation: cardIn .5s cubic-bezier(.2,.8,.2,1) both;
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }
        .note:hover { transform: translateY(-4px); border-color: rgba(99,102,241,.5); box-shadow: 0 28px 64px rgba(2,6,23,.7); }
        .note::after {
            content:''; position:absolute; left:0; top:0; bottom:0; width:4px;
            background: linear-gradient(180deg, var(--brand), var(--brand-2)); opacity:.8;
        }
        .note-top { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; align-items: flex-start; }
        .note h3 { font-family:'Sora',sans-serif; margin: 0; font-size: 1.2rem; }
        .note-meta { color: var(--muted); font-size: .8rem; margin-top: .35rem; display:flex; gap:.8rem; flex-wrap:wrap; }
        .note-body { white-space: pre-wrap; margin: 1rem 0 0; color: #d7def0; line-height: 1.6; }
        .note-actions { display: flex; gap: .5rem; flex-wrap: wrap; }
        .icon-btn { padding: .55rem .8rem; font-size: .85rem; }
        .summary-box {
            margin-top: 1.1rem; padding: 1rem 1.1rem; border-radius: 14px;
            background: linear-gradient(120deg, rgba(99,102,241,.12), rgba(34,211,238,.1));
            border: 1px solid rgba(99,102,241,.28);
            animation: cardIn .4s ease both;
        }
        .summary-box .s-head { display:flex; align-items:center; gap:.5rem; font-weight:700; font-size:.82rem; text-transform:uppercase; letter-spacing:.04em; color:#c7d2fe; }
        .summary-box p { margin: .5rem 0 0; color: #e6ebff; line-height: 1.55; }
        .chip { font-size:.66rem; padding:.18rem .5rem; border-radius:999px; background:rgba(255,255,255,.1); color:var(--muted); margin-left:auto; }

        /* Empty + loading */
        .empty { text-align: center; padding: 3rem 1rem; color: var(--muted); }
        .empty .emoji { font-size: 3.2rem; display:block; margin-bottom: .8rem; animation: bob 2.5s ease-in-out infinite; }
        @keyframes bob { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-10px);} }
        .skeleton { height: 132px; border-radius: 22px; background: linear-gradient(90deg, rgba(255,255,255,.04), rgba(255,255,255,.1), rgba(255,255,255,.04)); background-size: 200% 100%; animation: shimmer 1.3s infinite; margin-bottom: 1.25rem; }
        @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @keyframes dropIn { from { opacity: 0; transform: translateY(-18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes cardIn { from { opacity: 0; transform: translateY(22px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

        /* Toast */
        .toast {
            position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(120%);
            background: rgba(17,24,39,.92); color: #fff; padding: .95rem 1.3rem; border-radius: 14px;
            border: 1px solid var(--card-border); box-shadow: var(--shadow); backdrop-filter: blur(12px);
            display: flex; align-items: center; gap: .65rem; z-index: 50;
            transition: transform .4s cubic-bezier(.2,.9,.2,1.1); max-width: 90vw;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast.ok { border-color: rgba(52,211,153,.5); }
        .toast.err { border-color: rgba(251,113,133,.5); }
    </style>
</head>
<body>
<div class="aurora">
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
</div>

<main>
    <header>
        <span class="badge-ai"><span class="dot-pulse"></span> Powered by AI · Semantic Search · Summaries</span>
        <h1>AI Notes Manager</h1>
        <p>Capture ideas, search by meaning, and let AI summarize your notes.</p>
        <div class="stats">
            <div class="stat">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a5b4fc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                <div><b id="stat-count">0</b><br><span>Notes</span></div>
            </div>
            <div class="stat">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#67e8f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <div><b id="stat-mode">Ready</b><br><span>Search mode</span></div>
            </div>
            <div class="stat">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f9a8d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3m0 12v3M3 12h3m12 0h3M5.6 5.6l2.1 2.1m8.6 8.6 2.1 2.1m0-12.8-2.1 2.1M7.7 16.3l-2.1 2.1"/></svg>
                <div><b id="stat-ai">Auto</b><br><span>Summary engine</span></div>
            </div>
        </div>
    </header>

    <section class="card composer">
        <h2 class="section-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            <span id="composer-title">Create a note</span>
        </h2>
        <div class="grid2">
            <div>
                <label for="title">Title</label>
                <input id="title" placeholder="Meeting agenda" />
            </div>
            <div>
                <label for="query">Search notes</label>
                <div class="search-row">
                    <input id="query" placeholder="Search by concept or meaning" />
                    <button class="btn-cyan" id="search-button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                        Search
                    </button>
                </div>
            </div>
        </div>
        <div style="margin-top:1.1rem;">
            <label for="content">Content</label>
            <textarea id="content" rows="6" placeholder="Write the note here..."></textarea>
        </div>
        <div class="actions-row">
            <button class="btn-ghost" id="clear-button" type="button">Clear</button>
            <button class="btn-primary" id="save-button" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                <span id="save-label">Save note</span>
            </button>
        </div>
    </section>

    <div class="list-head">
        <h2>Your notes</h2>
        <span id="source-badge"></span>
    </div>
    <section id="notes-list"></section>
</main>

<div id="toast" class="toast"></div>

<script>
const apiBase = '/api/notes';
let currentEditId = null;

function icon(name) {
    const map = {
        edit: '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        trash: '<path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
        spark: '<path d="m12 3 1.9 5.8L20 10l-4.8 3.4L17 20l-5-3.6L7 20l1.8-6.6L4 10l6.1-1.2Z"/>',
    };
    return `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${map[name]}</svg>`;
}

function showToast(message, type = '') {
    const toast = document.getElementById('toast');
    toast.className = 'toast show ' + type;
    toast.textContent = message;
    clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(() => toast.className = 'toast ' + type, 3000);
}

function escapeText(value) {
    return String(value)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function formatDate(iso) {
    if (!iso) return '';
    try { return new Date(iso).toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' }); }
    catch (e) { return iso; }
}

async function fetchNotes(query = '') {
    const url = query ? apiBase + '/search?q=' + encodeURIComponent(query) : apiBase + '?limit=20';
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    return { notes: data.data || [], source: data.source || null };
}

function setSourceBadge(source) {
    const badge = document.getElementById('source-badge');
    const mode = document.getElementById('stat-mode');
    if (!source) { badge.innerHTML = ''; mode.textContent = 'Ready'; return; }
    if (source === 'semantic') {
        badge.innerHTML = '<span class="src-badge src-semantic">✦ Semantic match</span>';
        mode.textContent = 'Semantic';
    } else {
        badge.innerHTML = '<span class="src-badge src-keyword">⌕ Keyword match</span>';
        mode.textContent = 'Keyword';
    }
}

function showSkeletons(n = 3) {
    document.getElementById('notes-list').innerHTML = Array.from({ length: n }).map(() => '<div class="skeleton"></div>').join('');
}

function renderNotes(notes) {
    const list = document.getElementById('notes-list');
    document.getElementById('stat-count').textContent = notes.length;
    list.innerHTML = '';

    if (!notes.length) {
        list.innerHTML = `<div class="card empty"><span class="emoji">🗒️</span><h3 style="margin:0 0 .4rem;">No notes yet</h3><p style="margin:0;">Create your first note above — then try semantic search and AI summaries.</p></div>`;
        return;
    }

    notes.forEach((note, i) => {
        const card = document.createElement('div');
        card.className = 'card note';
        card.style.animationDelay = (i * 0.06) + 's';
        const score = (typeof note.similarity_score === 'number')
            ? `<span>· match ${(note.similarity_score * 100).toFixed(0)}%</span>` : '';
        card.innerHTML = `
            <div class="note-top">
                <div>
                    <h3>${escapeText(note.title)}</h3>
                    <div class="note-meta">
                        <span>#${note.id}</span>
                        <span>· ${formatDate(note.created_at)}</span>
                        ${score}
                    </div>
                </div>
                <div class="note-actions">
                    <button class="btn-ghost icon-btn" onclick="editNote(${note.id})">${icon('edit')} Edit</button>
                    <button class="btn-cyan icon-btn" onclick="generateSummary(${note.id}, this)">${icon('spark')} Summarize</button>
                    <button class="icon-btn" style="background:linear-gradient(120deg,#e11d48,#fb7185);" onclick="deleteNote(${note.id})">${icon('trash')} Delete</button>
                </div>
            </div>
            <p class="note-body">${escapeText(note.content)}</p>
            ${note.summary ? `<div class="summary-box"><div class="s-head">${icon('spark')} AI Summary <span class="chip">auto</span></div><p>${escapeText(note.summary)}</p></div>` : ''}
        `;
        list.appendChild(card);
    });
}

async function reloadNotes(query = '') {
    showSkeletons();
    try {
        const { notes, source } = await fetchNotes(query);
        setSourceBadge(query ? source : null);
        renderNotes(notes);
    } catch (e) {
        showToast('Could not load notes.', 'err');
        renderNotes([]);
    }
}

async function saveNote() {
    const btn = document.getElementById('save-button');
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();
    if (!title || !content) { showToast('Title and content are required.', 'err'); return; }

    btn.disabled = true;
    const method = currentEditId ? 'PUT' : 'POST';
    const url = currentEditId ? `${apiBase}/${currentEditId}` : apiBase;
    try {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ title, content })
        });
        const json = await res.json();
        if (!res.ok) { showToast(json.message || 'Unable to save note.', 'err'); return; }
        showToast(currentEditId ? 'Note updated ✓' : 'Note created ✓', 'ok');
        resetForm();
        reloadNotes();
    } catch (e) {
        showToast('Network error while saving.', 'err');
    } finally {
        btn.disabled = false;
    }
}

function resetForm() {
    currentEditId = null;
    document.getElementById('title').value = '';
    document.getElementById('content').value = '';
    document.getElementById('composer-title').textContent = 'Create a note';
    document.getElementById('save-label').textContent = 'Save note';
}

async function editNote(id) {
    try {
        const res = await fetch(`${apiBase}/${id}`, { headers: { 'Accept': 'application/json' } });
        const { data } = await res.json();
        if (!res.ok) { showToast('Unable to load note.', 'err'); return; }
        currentEditId = data.id;
        document.getElementById('title').value = data.title;
        document.getElementById('content').value = data.content;
        document.getElementById('composer-title').textContent = 'Edit note #' + data.id;
        document.getElementById('save-label').textContent = 'Update note';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.getElementById('title').focus();
    } catch (e) { showToast('Network error.', 'err'); }
}

async function deleteNote(id) {
    if (!confirm('Delete this note?')) return;
    try {
        const res = await fetch(`${apiBase}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json' } });
        if (!res.ok) { showToast('Unable to delete note.', 'err'); return; }
        showToast('Note deleted ✓', 'ok');
        reloadNotes();
    } catch (e) { showToast('Network error.', 'err'); }
}

async function generateSummary(id, btn) {
    let original;
    if (btn) { original = btn.innerHTML; btn.disabled = true; btn.innerHTML = `${icon('spark').replace('<svg', '<svg class="spin"')} Summarizing...`; }
    try {
        const res = await fetch(`${apiBase}/${id}/summary`, { method: 'POST', headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        if (!res.ok) { showToast(json.message || 'Summary failed.', 'err'); return; }
        const src = json.data && json.data.source ? json.data.source : '';
        document.getElementById('stat-ai').textContent = src === 'openai' ? 'OpenAI' : 'Local';
        showToast('Summary generated ✓ (' + (src || 'auto') + ')', 'ok');
        reloadNotes();
    } catch (e) {
        showToast('Network error.', 'err');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = original; }
    }
}

document.getElementById('save-button').addEventListener('click', saveNote);
document.getElementById('clear-button').addEventListener('click', resetForm);
document.getElementById('search-button').addEventListener('click', () => reloadNotes(document.getElementById('query').value));
document.getElementById('query').addEventListener('keypress', e => {
    if (e.key === 'Enter') { e.preventDefault(); reloadNotes(e.target.value); }
});

reloadNotes();
</script>
</body>
</html>
