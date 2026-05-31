<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Notes Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;450;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f7f8fa;
            --surface: #ffffff;
            --border: #e8eaed;
            --border-strong: #d7dade;
            --text: #1a1d21;
            --text-soft: #5b6470;
            --muted: #8a929e;
            --brand: #4f46e5;
            --brand-soft: #eef2ff;
            --brand-text: #4338ca;
            --green: #0f9d58;
            --green-soft: #e8f5ee;
            --amber-soft: #fef6e7;
            --amber-text: #b25e09;
            --red: #d93025;
            --red-soft: #fce8e6;
            --ring: rgba(79, 70, 229, 0.14);
            --shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.05);
            --shadow-md: 0 4px 16px rgba(16, 24, 40, 0.08);
            --shadow-lg: 0 12px 32px rgba(16, 24, 40, 0.10);
            --radius: 12px;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }

        /* Top bar */
        .topbar {
            position: sticky; top: 0; z-index: 30;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: saturate(180%) blur(12px);
            border-bottom: 1px solid var(--border);
        }
        .topbar-inner {
            max-width: 1080px; margin: 0 auto; padding: 0 1.5rem;
            height: 60px; display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .brand { display: flex; align-items: center; gap: .7rem; font-weight: 650; font-size: 1.02rem; letter-spacing: -0.01em; }
        .logo {
            width: 34px; height: 34px; border-radius: 9px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            display: grid; place-items: center; color: #fff; box-shadow: var(--shadow-sm);
        }
        .ai-pill {
            display: inline-flex; align-items: center; gap: .45rem;
            font-size: .76rem; font-weight: 550; color: var(--text-soft);
            padding: .35rem .7rem; border: 1px solid var(--border); border-radius: 999px; background: var(--surface);
        }
        .ai-pill .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--green); }

        .wrap { max-width: 1080px; margin: 0 auto; padding: 2.25rem 1.5rem 4rem; }

        /* Hero */
        .hero { margin-bottom: 1.75rem; }
        .hero h1 { font-size: 1.85rem; font-weight: 700; letter-spacing: -0.025em; margin: 0; }
        .hero p { color: var(--text-soft); margin: .5rem 0 0; font-size: 1rem; max-width: 56ch; }

        /* Stat strip */
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin: 1.5rem 0; }
        @media (max-width: 640px) { .stats { grid-template-columns: 1fr; } }
        .stat {
            background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
            padding: 1rem 1.1rem; display: flex; align-items: center; gap: .85rem; box-shadow: var(--shadow-sm);
        }
        .stat .ico { width: 38px; height: 38px; border-radius: 9px; display: grid; place-items: center; flex: none; }
        .stat .ico.i1 { background: var(--brand-soft); color: var(--brand); }
        .stat .ico.i2 { background: var(--green-soft); color: var(--green); }
        .stat .ico.i3 { background: var(--amber-soft); color: var(--amber-text); }
        .stat b { font-size: 1.15rem; font-weight: 650; letter-spacing: -0.01em; display: block; line-height: 1.2; }
        .stat span { color: var(--muted); font-size: .8rem; }

        /* Cards */
        .card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 16px; box-shadow: var(--shadow-sm);
        }
        .card-pad { padding: 1.35rem 1.4rem; }
        .card-head { display: flex; align-items: center; gap: .6rem; padding: 1.1rem 1.4rem; border-bottom: 1px solid var(--border); }
        .card-head h2 { font-size: 1rem; font-weight: 650; margin: 0; letter-spacing: -0.01em; }
        .card-head .ico { color: var(--brand); display: grid; place-items: center; }

        .field { margin-bottom: 1.1rem; }
        .field:last-child { margin-bottom: 0; }
        label { display: block; font-size: .82rem; font-weight: 550; color: var(--text-soft); margin-bottom: .4rem; }

        input, textarea {
            width: 100%; font: inherit; color: var(--text);
            background: var(--surface);
            border: 1px solid var(--border-strong); border-radius: 10px;
            padding: .65rem .8rem; transition: border-color .15s, box-shadow .15s;
        }
        input::placeholder, textarea::placeholder { color: #aab1bc; }
        input:focus, textarea:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 3.5px var(--ring); }
        textarea { resize: vertical; min-height: 120px; line-height: 1.6; }

        /* Buttons */
        button { font: inherit; cursor: pointer; border: 1px solid transparent; border-radius: 10px; font-weight: 550;
            display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
            padding: .6rem 1rem; transition: background .15s, border-color .15s, box-shadow .15s, transform .08s; }
        button:active { transform: translateY(1px); }
        button:disabled { opacity: .55; cursor: not-allowed; }
        .btn-primary { background: var(--brand); color: #fff; box-shadow: var(--shadow-sm); }
        .btn-primary:hover { background: #4338ca; }
        .btn-default { background: var(--surface); color: var(--text); border-color: var(--border-strong); }
        .btn-default:hover { background: #f3f4f6; }
        .btn-sm { padding: .45rem .75rem; font-size: .85rem; }
        .btn-danger-ghost { background: var(--surface); color: var(--red); border-color: var(--border-strong); }
        .btn-danger-ghost:hover { background: var(--red-soft); border-color: #f3c2bd; }

        .composer-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.4rem; }
        @media (max-width: 720px) { .composer-grid { grid-template-columns: 1fr; gap: 1.1rem; } }
        .search-row { display: flex; gap: .55rem; }
        .search-row input { flex: 1; }
        .actions { display: flex; justify-content: flex-end; gap: .65rem; margin-top: 1.2rem; }
        .form-divider { border: none; border-top: 1px solid var(--border); margin: 1.3rem 0; }

        /* List header */
        .list-head { display: flex; align-items: center; justify-content: space-between; margin: 2.25rem 0 1rem; }
        .list-head h2 { font-size: 1.05rem; font-weight: 650; margin: 0; letter-spacing: -0.01em; }
        .list-head .count { color: var(--muted); font-weight: 450; font-size: .9rem; margin-left: .4rem; }
        .badge { font-size: .72rem; font-weight: 600; padding: .28rem .6rem; border-radius: 999px; display: inline-flex; align-items: center; gap: .35rem; }
        .badge.semantic { background: var(--green-soft); color: var(--green); }
        .badge.keyword { background: var(--amber-soft); color: var(--amber-text); }

        /* Notes grid */
        .notes-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.1rem; }
        @media (max-width: 720px) { .notes-grid { grid-template-columns: 1fr; } }
        .note {
            background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 1.2rem 1.25rem;
            box-shadow: var(--shadow-sm); transition: box-shadow .18s, border-color .18s, transform .18s;
            display: flex; flex-direction: column; animation: rise .4s ease both;
        }
        .note:hover { box-shadow: var(--shadow-md); border-color: var(--border-strong); transform: translateY(-2px); }
        .note-top { display: flex; justify-content: space-between; align-items: flex-start; gap: .75rem; }
        .note h3 { font-size: 1.05rem; font-weight: 650; margin: 0; letter-spacing: -0.01em; line-height: 1.35; }
        .note-meta { display: flex; gap: .6rem; flex-wrap: wrap; color: var(--muted); font-size: .78rem; margin-top: .35rem; }
        .note-meta .sim { color: var(--green); font-weight: 550; }
        .note-body { color: var(--text-soft); line-height: 1.6; margin: .85rem 0 0; font-size: .94rem; white-space: pre-wrap;
            display: -webkit-box; -webkit-line-clamp: 6; -webkit-box-orient: vertical; overflow: hidden; }
        .menu { position: relative; flex: none; }
        .menu-btn { background: transparent; border: none; padding: .3rem; border-radius: 8px; color: var(--muted); }
        .menu-btn:hover { background: #f1f2f4; color: var(--text); }
        .note-actions { display: flex; gap: .5rem; margin-top: 1.1rem; padding-top: 1rem; border-top: 1px solid var(--border); }
        .summary {
            margin-top: 1rem; padding: .85rem 1rem; border-radius: 10px;
            background: var(--brand-soft); border: 1px solid #e0e5ff;
        }
        .summary-head { display: flex; align-items: center; gap: .4rem; font-size: .76rem; font-weight: 600; color: var(--brand-text); text-transform: uppercase; letter-spacing: .03em; }
        .summary-head .src { margin-left: auto; text-transform: none; letter-spacing: 0; font-weight: 500; color: var(--muted); background: rgba(255,255,255,.7); padding: .1rem .45rem; border-radius: 999px; }
        .summary p { margin: .45rem 0 0; color: var(--text); font-size: .9rem; line-height: 1.55; }

        /* Empty + skeleton */
        .empty { grid-column: 1 / -1; text-align: center; padding: 3.5rem 1rem; border: 1px dashed var(--border-strong); border-radius: 14px; background: var(--surface); }
        .empty .ico { width: 52px; height: 52px; border-radius: 13px; background: var(--brand-soft); color: var(--brand); display: grid; place-items: center; margin: 0 auto 1rem; }
        .empty h3 { margin: 0 0 .35rem; font-size: 1.05rem; font-weight: 650; }
        .empty p { margin: 0; color: var(--muted); }
        .skeleton { grid-column: span 1; height: 168px; border-radius: 14px; border: 1px solid var(--border);
            background: linear-gradient(90deg, #f0f1f3 25%, #f7f8fa 37%, #f0f1f3 63%); background-size: 400% 100%; animation: shimmer 1.4s infinite; }
        @keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        @keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .spin { animation: spin 0.9s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Toast */
        .toast {
            position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(150%);
            background: var(--text); color: #fff; padding: .75rem 1.1rem; border-radius: 11px; font-size: .9rem; font-weight: 500;
            display: flex; align-items: center; gap: .55rem; box-shadow: var(--shadow-lg); z-index: 50; max-width: 90vw;
            transition: transform .35s cubic-bezier(.2,.9,.3,1.1);
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast .tdot { width: 8px; height: 8px; border-radius: 50%; background: #fff; flex: none; }
        .toast.ok .tdot { background: #4ade80; }
        .toast.err .tdot { background: #f87171; }

        footer { text-align: center; color: var(--muted); font-size: .82rem; margin-top: 3rem; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <span class="logo">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h4"/></svg>
                </span>
                AI Notes Manager
            </div>
            <span class="ai-pill"><span class="dot"></span> AI services online</span>
        </div>
    </div>

    <div class="wrap">
        <div class="hero">
            <h1>Your notes, organized intelligently</h1>
            <p>Create notes, search them by meaning with semantic vector search, and generate concise AI summaries on demand.</p>
        </div>

        <div class="stats">
            <div class="stat">
                <span class="ico i1"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></span>
                <div><b id="stat-count">0</b><span>Total notes</span></div>
            </div>
            <div class="stat">
                <span class="ico i2"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></span>
                <div><b id="stat-mode">Standard</b><span>Search mode</span></div>
            </div>
            <div class="stat">
                <span class="ico i3"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 1.9 5.8L20 10l-4.8 3.4L17 20l-5-3.6L7 20l1.8-6.6L4 10l6.1-1.2Z"/></svg></span>
                <div><b id="stat-ai">Auto</b><span>Summary engine</span></div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <span class="ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></span>
                <h2 id="composer-title">Create a note</h2>
            </div>
            <div class="card-pad">
                <div class="composer-grid">
                    <div class="field">
                        <label for="title">Title</label>
                        <input id="title" placeholder="Project meeting agenda" />
                    </div>
                    <div class="field">
                        <label for="query">Search notes</label>
                        <div class="search-row">
                            <input id="query" placeholder="Search by concept or meaning…" />
                            <button class="btn-default" id="search-button">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                                Search
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="form-divider" />
                <div class="field">
                    <label for="content">Content</label>
                    <textarea id="content" rows="6" placeholder="Write your note here…"></textarea>
                </div>
                <div class="actions">
                    <button class="btn-default" id="clear-button" type="button">Clear</button>
                    <button class="btn-primary" id="save-button" type="button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5v14"/></svg>
                        <span id="save-label">Save note</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="list-head">
            <h2>Your notes <span class="count" id="list-count"></span></h2>
            <span id="source-badge"></span>
        </div>
        <div class="notes-grid" id="notes-list"></div>

        <footer>AI Notes Manager · Laravel REST API · Semantic search &amp; AI summaries</footer>
    </div>

    <div id="toast" class="toast"><span class="tdot"></span><span id="toast-msg"></span></div>

<script>
const apiBase = '/api/notes';
let currentEditId = null;

function svg(path, w = 16, sw = 2) {
    return `<svg width="${w}" height="${w}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="${sw}" stroke-linecap="round" stroke-linejoin="round">${path}</svg>`;
}
const ICONS = {
    edit: '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
    trash: '<path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
    spark: '<path d="m12 3 1.9 5.8L20 10l-4.8 3.4L17 20l-5-3.6L7 20l1.8-6.6L4 10l6.1-1.2Z"/>',
    doc: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
};

function showToast(message, type = '') {
    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = message;
    toast.className = 'toast show ' + type;
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
    try { return new Date(iso).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }); }
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
    if (!source) { badge.innerHTML = ''; mode.textContent = 'Standard'; return; }
    if (source === 'semantic') {
        badge.innerHTML = '<span class="badge semantic">' + svg(ICONS.spark, 13) + ' Semantic results</span>';
        mode.textContent = 'Semantic';
    } else {
        badge.innerHTML = '<span class="badge keyword">Keyword results</span>';
        mode.textContent = 'Keyword';
    }
}

function showSkeletons(n = 4) {
    document.getElementById('notes-list').innerHTML = Array.from({ length: n }).map(() => '<div class="skeleton"></div>').join('');
}

function renderNotes(notes) {
    const list = document.getElementById('notes-list');
    document.getElementById('stat-count').textContent = notes.length;
    document.getElementById('list-count').textContent = notes.length ? '(' + notes.length + ')' : '';
    list.innerHTML = '';

    if (!notes.length) {
        list.innerHTML = `<div class="empty"><div class="ico">${svg(ICONS.doc, 24)}</div><h3>No notes yet</h3><p>Create your first note above — then try semantic search and AI summaries.</p></div>`;
        return;
    }

    notes.forEach((note, i) => {
        const card = document.createElement('div');
        card.className = 'note';
        card.style.animationDelay = (i * 0.05) + 's';
        const sim = (typeof note.similarity_score === 'number')
            ? `<span class="sim">${(note.similarity_score * 100).toFixed(0)}% match</span>` : '';
        card.innerHTML = `
            <div class="note-top">
                <div>
                    <h3>${escapeText(note.title)}</h3>
                    <div class="note-meta"><span>#${note.id}</span><span>${formatDate(note.created_at)}</span>${sim}</div>
                </div>
            </div>
            <p class="note-body">${escapeText(note.content)}</p>
            ${note.summary ? `<div class="summary"><div class="summary-head">${svg(ICONS.spark, 13)} AI Summary</div><p>${escapeText(note.summary)}</p></div>` : ''}
            <div class="note-actions">
                <button class="btn-default btn-sm" onclick="editNote(${note.id})">${svg(ICONS.edit, 14)} Edit</button>
                <button class="btn-default btn-sm" onclick="generateSummary(${note.id}, this)">${svg(ICONS.spark, 14)} Summarize</button>
                <button class="btn-danger-ghost btn-sm" onclick="deleteNote(${note.id})">${svg(ICONS.trash, 14)} Delete</button>
            </div>
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
        showToast(currentEditId ? 'Note updated' : 'Note created', 'ok');
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
    if (!confirm('Delete this note? This cannot be undone.')) return;
    try {
        const res = await fetch(`${apiBase}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json' } });
        if (!res.ok) { showToast('Unable to delete note.', 'err'); return; }
        showToast('Note deleted', 'ok');
        reloadNotes();
    } catch (e) { showToast('Network error.', 'err'); }
}

async function generateSummary(id, btn) {
    let original;
    if (btn) { original = btn.innerHTML; btn.disabled = true; btn.innerHTML = svg(ICONS.spark, 14).replace('<svg', '<svg class="spin"') + ' Summarizing…'; }
    try {
        const res = await fetch(`${apiBase}/${id}/summary`, { method: 'POST', headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        if (!res.ok) { showToast(json.message || 'Summary failed.', 'err'); return; }
        const src = json.data && json.data.source ? json.data.source : '';
        document.getElementById('stat-ai').textContent = src === 'openai' ? 'OpenAI' : 'Local';
        showToast('Summary generated', 'ok');
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
