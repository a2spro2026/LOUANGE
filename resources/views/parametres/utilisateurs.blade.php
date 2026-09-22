@extends('layouts.app')

@section('title', 'Utilisateur')
@section('page-title', 'Utilisateur')

@section('content')
<div class="module usr">
    @if (session('success'))
        <div class="flash flash--ok">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash flash--err">{{ $errors->first() }}</div>
    @endif

    <div class="module__toolbar">
        <div class="module__footer">
            <button type="button" class="btn btn--gold" id="btn-ajouter-usr">Ajouter</button>
            <a href="{{ route('dashboard') }}" class="btn btn--ghost">Fermer</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom Complet</th>
                    <th>Contact</th>
                    <th>Statut</th>
                    <th>Login</th>
                    <th>Mot de passe</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr
                        class="{{ $user->actif ? '' : 'is-suspended' }}"
                        data-id="{{ $user->id }}"
                        data-name="{{ $user->name }}"
                        data-contact="{{ $user->contact }}"
                        data-statut="{{ $user->statut }}"
                        data-login="{{ $user->login }}"
                        data-actif="{{ $user->actif ? '1' : '0' }}"
                    >
                        <td class="num">{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->contact ?: '—' }}</td>
                        <td>
                            <span class="usr-badge usr-badge--{{ $user->statut }}">{{ $user->statutLabel() }}</span>
                            @unless ($user->actif)
                                <span class="usr-badge usr-badge--off">Suspendu</span>
                            @endunless
                        </td>
                        <td>{{ $user->login }}</td>
                        <td class="usr-pass">••••••••</td>
                        <td>
                            <div class="row-actions">
                                <button type="button" class="act" title="Modifier" data-action="edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('parametres.utilisateurs.toggle', $user) }}" class="inline-form">
                                    @csrf
                                    @if ($user->actif)
                                        <button type="submit" class="act act--warn" title="Suspendre" @disabled($user->id === auth()->id())>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M8 12h8"/></svg>
                                        </button>
                                    @else
                                        <button type="submit" class="act act--ok" title="Activer">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M8 12l2.5 2.5L16 9"/></svg>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">Aucun utilisateur.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="module__pager">{{ $users->links() }}</div>
</div>

<div class="overlay" id="panel-usr" hidden>
    <div class="panel panel--sm" role="dialog" aria-modal="true" aria-labelledby="panel-usr-title">
        <h2 class="panel__title" id="panel-usr-title">Ajouter un utilisateur</h2>
        <form method="POST" action="{{ route('parametres.utilisateurs.store') }}" id="form-usr" autocomplete="off">
            @csrf
            <input type="hidden" name="_method" id="usr-method" value="POST">
            <div class="panel__grid panel__grid--1">
                <label class="field">
                    <span>Nom Complet</span>
                    <input type="text" name="name" id="usr-name" value="" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Contact</span>
                    <input type="text" name="contact" id="usr-contact" value="" autocomplete="off">
                </label>
                <label class="field">
                    <span>Statut</span>
                    <select name="statut" id="usr-statut" required autocomplete="off">
                        <option value="" disabled selected>— Sélectionner —</option>
                        @foreach ($statuts as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="field">
                    <span>Login</span>
                    <input type="text" name="login" id="usr-login" value="" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Mot de passe</span>
                    <input type="password" name="password" id="usr-password" value="" autocomplete="off">
                    <small class="field__hint" id="usr-pass-hint" hidden>Laisser vide pour conserver le mot de passe actuel.</small>
                </label>
            </div>
            <div class="panel__actions">
                <button type="submit" class="btn btn--gold">Valider</button>
                <button type="button" class="btn btn--ghost" data-close="panel-usr">Fermer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .usr { gap: 0.85rem; }
    .module__toolbar {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    .module__footer { display: flex; gap: 0.55rem; flex-wrap: wrap; }

    .btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.55rem 1rem; border: 1px solid transparent;
        font: inherit; font-size: 0.82rem; letter-spacing: 0.06em;
        text-transform: uppercase; text-decoration: none; cursor: pointer;
    }
    .btn--gold { background: var(--gold); color: #121212; border-color: var(--gold); }
    .btn--gold:hover { background: var(--gold-soft); }
    .btn--ghost { background: transparent; color: var(--mist); border-color: var(--line); }
    .btn--ghost:hover { border-color: var(--gold); color: var(--gold-soft); }

    .flash { padding: 0.75rem 1rem; border: 1px solid var(--line); font-size: 0.88rem; }
    .flash--ok { background: rgba(109, 191, 138, 0.12); border-color: rgba(109, 191, 138, 0.35); }
    .flash--err { background: rgba(212, 90, 90, 0.12); border-color: rgba(212, 90, 90, 0.35); color: #e8a0a0; }

    .table-wrap {
        border: 1px solid var(--line);
        background: var(--panel);
        overflow-x: hidden;
        overflow-y: auto;
        flex: 1;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    .data-table th,
    .data-table td {
        padding: 0.75rem 0.85rem;
        border-bottom: 1px solid var(--line);
        text-align: left;
        vertical-align: middle;
    }
    .data-table th {
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
        font-weight: 600;
        position: sticky;
        top: 0;
        background: var(--panel);
        z-index: 1;
    }
    .data-table .num {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }
    .data-table .empty { text-align: center; color: var(--muted); padding: 2rem; }
    .data-table tr.is-suspended td { opacity: 0.55; }
    .usr-pass {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        letter-spacing: 0.12em;
        color: var(--muted);
    }
    .usr-badge {
        display: inline-block;
        padding: 0.15rem 0.45rem;
        border: 1px solid var(--line);
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-right: 0.25rem;
    }
    .usr-badge--gerant { color: var(--gold-soft); border-color: color-mix(in srgb, var(--gold) 40%, var(--line)); }
    .usr-badge--assistant { color: #6aa3d4; border-color: rgba(106, 163, 212, 0.4); }
    .usr-badge--commercial { color: #6dbf8a; border-color: rgba(109, 191, 138, 0.4); }
    .usr-badge--atelier { color: #d4845a; border-color: rgba(212, 132, 90, 0.4); }
    .usr-badge--admin { color: #c9a227; border-color: rgba(201, 162, 39, 0.45); }
    .usr-badge--off { color: #e8a0a0; border-color: rgba(212, 90, 90, 0.4); }

    .row-actions { display: flex; gap: 0.35rem; align-items: center; }
    .inline-form { display: inline; margin: 0; }
    .act {
        width: 2rem; height: 2rem; display: grid; place-items: center;
        border: 1px solid var(--line); background: transparent; color: var(--text);
        cursor: pointer; padding: 0;
    }
    .act:hover { border-color: var(--gold); color: var(--gold-soft); }
    .act:disabled { opacity: 0.35; cursor: not-allowed; }
    .act--ok:hover { border-color: #6dbf8a; color: #6dbf8a; }
    .act--warn:hover { border-color: #d4845a; color: #d4845a; }
    .act svg { width: 1.05rem; height: 1.05rem; }

    .overlay {
        position: fixed; inset: 0; z-index: 60;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem; background: rgba(6, 6, 8, 0.65); backdrop-filter: blur(6px);
    }
    .overlay[hidden] { display: none !important; }
    .panel {
        width: min(100%, 28rem);
        background: var(--panel);
        border: 1px solid var(--line);
        padding: 1.35rem 1.3rem 1.2rem;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
        max-height: 92vh;
        overflow: auto;
    }
    .panel__title {
        font-family: "Cormorant Garamond", serif;
        font-size: 1.35rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--gold-soft);
        margin-bottom: 1rem;
    }
    .panel__grid--1 { display: grid; gap: 0.85rem; }
    .field { display: flex; flex-direction: column; gap: 0.35rem; }
    .field span {
        font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted);
    }
    .field input, .field select {
        background: transparent; border: 1px solid var(--line); color: var(--text);
        padding: 0.55rem 0.65rem; font: inherit;
    }
    html[data-theme="light"] .field input,
    html[data-theme="light"] .field select { background: #fff; }
    .field input:focus, .field select:focus { border-color: var(--gold); outline: none; }
    .field__hint { font-size: 0.75rem; color: var(--muted); }
    .panel__actions {
        display: flex; gap: 0.55rem; justify-content: flex-end; margin-top: 1.2rem;
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const panel = document.getElementById('panel-usr');
    const form = document.getElementById('form-usr');
    const methodInput = document.getElementById('usr-method');
    const title = document.getElementById('panel-usr-title');
    const passHint = document.getElementById('usr-pass-hint');
    const passInput = document.getElementById('usr-password');
    const storeUrl = @json(route('parametres.utilisateurs.store'));
    const updateBase = @json(url('/parametres/utilisateurs'));

    function openPanel() { panel.hidden = false; }
    function closePanel() { panel.hidden = true; }

    function clearForm() {
        form.reset();
        document.getElementById('usr-statut').selectedIndex = 0;
        document.getElementById('usr-name').value = '';
        document.getElementById('usr-contact').value = '';
        document.getElementById('usr-login').value = '';
        passInput.value = '';
        passInput.required = true;
        passHint.hidden = true;
        methodInput.value = 'POST';
        form.action = storeUrl;
        title.textContent = 'Ajouter un utilisateur';
    }

    function openCreate() {
        clearForm();
        openPanel();
    }

    function openEdit(row) {
        clearForm();
        title.textContent = 'Modifier un utilisateur';
        form.action = updateBase + '/' + row.dataset.id;
        methodInput.value = 'PUT';
        document.getElementById('usr-name').value = row.dataset.name || '';
        document.getElementById('usr-contact').value = row.dataset.contact || '';
        document.getElementById('usr-login').value = row.dataset.login || '';
        var statut = row.dataset.statut || '';
        var select = document.getElementById('usr-statut');
        if (statut === 'admin') {
            var opt = document.createElement('option');
            opt.value = 'admin';
            opt.textContent = 'Admin';
            select.appendChild(opt);
        }
        select.value = statut;
        passInput.required = false;
        passHint.hidden = false;
        openPanel();
    }

    document.getElementById('btn-ajouter-usr').addEventListener('click', openCreate);

    document.querySelectorAll('.data-table tbody tr[data-id]').forEach(function (row) {
        row.querySelectorAll('[data-action="edit"]').forEach(function (btn) {
            btn.addEventListener('click', function () { openEdit(row); });
        });
    });

    document.querySelectorAll('[data-close]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closePanel();
            clearForm();
        });
    });

    panel.addEventListener('click', function (e) {
        if (e.target === panel) {
            closePanel();
            clearForm();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.hidden) {
            closePanel();
            clearForm();
        }
    });

    @if ($errors->any())
    openCreate();
    @endif
})();
</script>
@endpush
