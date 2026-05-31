<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Notes Manager</title>
    <style>
        body { font-family: Inter, system-ui, sans-serif; margin: 0; padding: 0; background: #f4f5f7; color: #111827; }
        main { max-width: 1000px; margin: 2rem auto; padding: 1rem; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
        h1 { margin: 0; font-size: 2rem; }
        button, input, textarea, select { font: inherit; }
        .card { background: white; border-radius: 1rem; box-shadow: 0 16px 40px rgba(15,23,42,.08); padding: 1.25rem; margin-bottom: 1rem; }
        .grid { display: grid; gap: 1rem; }
        .grid-cols-2 { grid-template-columns: 1fr 1fr; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        input, textarea { width: 100%; border: 1px solid #d1d5db; border-radius: .75rem; padding: .75rem; }
        button { border: none; border-radius: .75rem; padding: .85rem 1rem; cursor: pointer; background: #2563eb; color: white; transition: background .2s ease; }
        button:hover { background: #1d4ed8; }
        .note-actions button { margin-right: .5rem; }
        .inline { display: inline-flex; align-items: center; gap: .5rem; }
        .toast { position: fixed; bottom: 1rem; right: 1rem; background: #111827; color: white; padding: 1rem 1.25rem; border-radius: .75rem; box-shadow: 0 18px 40px rgba(0,0,0,.16); }
        .search-row { display: flex; gap: .75rem; align-items: center; margin-bottom: 1rem; }
        .search-row input { flex: 1; }
    </style>
</head>
<body>
<main>
    <header>
        <div>
            <h1>AI Notes Manager</h1>
            <p>Save notes, search semantically, and generate summaries.</p>
        </div>
    </header>

    <section class="card" id="note-form-card">
        <div class="grid grid-cols-2">
            <div>
                <label for="title">Title</label>
                <input id="title" placeholder="Meeting agenda" />
            </div>
            <div>
                <label for="query">Search notes</label>
                <div class="search-row">
                    <input id="query" placeholder="Search by concept or meaning" />
                    <button id="search-button">Search</button>
                </div>
            </div>
        </div>
        <div style="margin-top:1rem;">
            <label for="content">Content</label>
            <textarea id="content" rows="6" placeholder="Write the note here..."></textarea>
        </div>
        <div style="display:flex; justify-content:flex-end; margin-top:1rem; gap:.75rem;">
            <button id="clear-button" type="button">Clear</button>
            <button id="save-button" type="button">Save note</button>
        </div>
    </section>

    <section id="notes-list"></section>
</main>

<div id="toast" class="toast" style="display:none;"></div>

<script>
const apiBase = '/api/notes';
let currentEditId = null;

function showToast(message){
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.display = 'block';
    clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(() => toast.style.display = 'none', 3000);
}

async function fetchNotes(query = '') {
    const url = query ? apiBase + '/search?q=' + encodeURIComponent(query) : apiBase + '?limit=20';
    const res = await fetch(url);
    const data = await res.json();
    return data.data || [];
}

function escapeText(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function renderNotes(notes) {
    const list = document.getElementById('notes-list');
    list.innerHTML = '';
    if (!notes.length) {
        list.innerHTML = '<div class="card"><p>No notes found yet.</p></div>';
        return;
    }

    notes.forEach(note => {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
            <div style="display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                <div>
                    <h2 style="margin:0;">${escapeText(note.title)}</h2>
                    <p style="margin:.5rem 0 0; color:#6b7280; font-size:.95rem;">ID: ${note.id}</p>
                </div>
                <div class="note-actions">
                    <button onclick="editNote(${note.id})">Edit</button>
                    <button onclick="deleteNote(${note.id})" style="background:#ef4444;">Delete</button>
                    <button onclick="generateSummary(${note.id})" style="background:#10b981;">Summarize</button>
                </div>
            </div>
            <p style="white-space:pre-wrap; margin:1rem 0 0;">${escapeText(note.content)}</p>
            ${note.summary ? `<div style="margin-top:1rem; padding:1rem; background:#f3f4f6; border-radius:.75rem;"><strong>Summary:</strong><p style="margin:.5rem 0 0;">${escapeText(note.summary)}</p></div>` : ''}
        `;
        list.appendChild(card);
    });
}

async function reloadNotes(query='') {
    const notes = await fetchNotes(query);
    renderNotes(notes);
}

async function saveNote() {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();
    if (!title || !content) {
        showToast('Title and content are required.');
        return;
    }

    const method = currentEditId ? 'PUT' : 'POST';
    const url = currentEditId ? `${apiBase}/${currentEditId}` : apiBase;
    const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title, content })
    });

    if (!res.ok) {
        const json = await res.json();
        showToast(json.message || 'Unable to save note.');
        return;
    }

    showToast(currentEditId ? 'Note updated.' : 'Note created.');
    resetForm();
    reloadNotes();
}

function resetForm() {
    currentEditId = null;
    document.getElementById('title').value = '';
    document.getElementById('content').value = '';
}

async function editNote(id) {
    const res = await fetch(`${apiBase}/${id}`);
    const { data } = await res.json();
    if (!res.ok) {
        showToast('Unable to load note.');
        return;
    }
    currentEditId = data.id;
    document.getElementById('title').value = data.title;
    document.getElementById('content').value = data.content;
}

async function deleteNote(id) {
    if (!confirm('Delete this note?')) return;
    const res = await fetch(`${apiBase}/${id}`, { method: 'DELETE' });
    if (!res.ok) {
        showToast('Unable to delete note.');
        return;
    }
    showToast('Note deleted.');
    reloadNotes();
}

async function generateSummary(id) {
    const res = await fetch(`${apiBase}/${id}/summary`, { method: 'POST' });
    const json = await res.json();
    if (!res.ok) {
        showToast(json.message || 'Summary failed.');
        return;
    }
    showToast('Summary generated.');
    reloadNotes();
}

document.getElementById('save-button').addEventListener('click', saveNote);
document.getElementById('clear-button').addEventListener('click', resetForm);
document.getElementById('search-button').addEventListener('click', () => reloadNotes(document.getElementById('query').value));

document.getElementById('query').addEventListener('keypress', event => {
    if (event.key === 'Enter') {
        event.preventDefault();
        reloadNotes(event.target.value);
    }
});

reloadNotes();
</script>
</body>
</html>
