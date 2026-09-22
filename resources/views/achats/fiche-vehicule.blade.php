@extends('layouts.app')

@section('title', 'Fiche Véhicule')
@section('page-title', 'Fiche Véhicule')

@section('content')
<div class="module fv">
    @if (session('success'))
        <div class="flash flash--ok">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash flash--err">{{ $errors->first() }}</div>
    @endif

    <div class="module__toolbar">
        <header class="fv-catalogue">
            <p class="fv-catalogue__eyebrow">STE LOUANGE AUTO</p>
            <h2 class="fv-catalogue__title">Catalogue</h2>
        </header>
        <div class="module__footer">
            <a href="{{ route('dashboard') }}" class="btn btn--ghost">Fermer</a>
        </div>
    </div>

    <div class="fv-grid" id="fv-grid">
        @forelse ($vehicules as $bon)
            @php
                $fiche = $bon->ficheVehicule;
                $photoBon = $bon->piece_jointe ? asset('storage/'.$bon->piece_jointe) : '';
                $p1 = $fiche?->photo_1 ? asset('storage/'.$fiche->photo_1) : '';
                $p2 = $fiche?->photo_2 ? asset('storage/'.$fiche->photo_2) : '';
                $p3 = $fiche?->photo_3 ? asset('storage/'.$fiche->photo_3) : '';
                $description = $fiche
                    ? trim(($fiche->marque ?? '').' '.($fiche->modele ?? ''))
                    : $bon->type_vehicule;
                if ($description === '') {
                    $description = $bon->type_vehicule;
                }
                $prixAchat = $fiche ? montant_fr($fiche->montant_achat) : montant_fr($bon->montant);
            @endphp
            <article
                class="fv-card"
                data-bon-id="{{ $bon->id }}"
                data-fiche-id="{{ $fiche?->id }}"
                data-date="{{ $fiche ? date_fr($fiche->date_achat) : date_fr($bon->date_bon) }}"
                data-proprietaire="{{ $fiche?->nom_proprietaire ?? '' }}"
                data-marque="{{ $fiche?->marque ?? '' }}"
                data-modele="{{ $fiche?->modele ?? $bon->type_vehicule }}"
                data-kilometrage="{{ $fiche?->kilometrage ?? '' }}"
                data-couleur="{{ $fiche?->couleur ?? '' }}"
                data-montant="{{ $prixAchat }}"
                data-titre="{{ $fiche?->titre ?? '' }}"
                data-desc-catalogue="{{ $fiche?->description ?? '' }}"
                data-montant-vente="{{ $fiche && $fiche->montant_vente !== null ? montant_fr($fiche->montant_vente) : '' }}"
                data-en-catalogue="{{ $fiche && $fiche->en_catalogue ? '1' : '0' }}"
                data-photo-bon="{{ $photoBon }}"
                data-photo1="{{ $p1 }}"
                data-photo2="{{ $p2 }}"
                data-photo3="{{ $p3 }}"
                data-label="{{ $description }}"
                data-description="{{ $description }}"
            >
                <button type="button" class="fv-frame" data-photo-click>
                    @if ($photoBon)
                        <img src="{{ $photoBon }}" alt="{{ $description }}" loading="lazy">
                    @else
                        <span class="fv-frame__empty">Aucune photo</span>
                    @endif
                    <span class="fv-frame__info">
                        <span class="fv-frame__desc">{{ $description }}</span>
                    </span>
                </button>
                <div class="fv-card__bar">
                    <div class="fv-card__actions">
                        <button type="button" class="act" title="Voir" data-action="voir">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                        <button type="button" class="act" title="Imprimer" data-action="imprimer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 9V3h12v6"/><path d="M6 17H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><path d="M6 13h12v8H6z"/></svg>
                        </button>
                        <button type="button" class="act" title="Télécharger" data-action="telecharger">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                        </button>
                    </div>
                    <div class="fv-card__prix">
                        <span class="fv-card__prix-label">Prix achat</span>
                        <strong class="fv-card__prix-value">{{ $prixAchat }}</strong>
                    </div>
                </div>
            </article>
        @empty
            <div class="fv-empty">Aucun véhicule.</div>
        @endforelse
    </div>

    <div class="module__pager">{{ $vehicules->links() }}</div>
</div>

{{-- Aperçu photo bon d'achat --}}
<div class="overlay overlay--photo" id="panel-fv-photo" hidden>
    <div class="fv-photo-view" role="dialog" aria-modal="true" aria-label="Photo véhicule">
        <button type="button" class="fv-photo-view__img-btn" id="fv-photo-reclic">
            <img id="fv-photo-view-img" alt="Véhicule acheté">
            <span class="fv-photo-view__caption">
                <span id="fv-photo-view-desc"></span>
                <span class="fv-photo-view__prix">
                    <span class="fv-photo-view__prix-label">Prix achat</span>
                    <strong id="fv-photo-view-prix"></strong>
                </span>
            </span>
        </button>
        <button type="button" class="btn btn--ghost" id="btn-fv-photo-close">Fermer</button>
    </div>
</div>

{{-- Panneau saisie --}}
<div class="overlay" id="panel-fv" hidden>
    <div class="panel panel--fv" role="dialog" aria-modal="true" aria-labelledby="panel-fv-title">
        <h2 class="panel__title" id="panel-fv-title">Fiche véhicule</h2>
        <div class="panel__msg" id="panel-fv-msg" hidden></div>
        <form method="POST" action="{{ route('achats.fiche-vehicule.store') }}" id="form-fv" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="fv-method" value="POST">
            <input type="hidden" name="bon_achat_id" id="fv-bon-id" value="">
            <div class="panel__grid">
                <label class="field">
                    <span>Date achat</span>
                    <input type="text" name="date_achat" id="fv-date" value="" placeholder="jj/mm/aaaa" maxlength="10" required autocomplete="off" inputmode="numeric" class="input-date">
                </label>
                <label class="field">
                    <span>Nom propriétaire</span>
                    <input type="text" name="nom_proprietaire" id="fv-proprietaire" value="" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Marque</span>
                    <input type="text" name="marque" id="fv-marque" value="" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Modèle</span>
                    <input type="text" name="modele" id="fv-modele" value="" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Kilométrage</span>
                    <input type="number" name="kilometrage" id="fv-km" value="" min="0" step="1" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Couleur</span>
                    <input type="text" name="couleur" id="fv-couleur" value="" autocomplete="off">
                </label>
                <label class="field">
                    <span>Montant Achat</span>
                    <input type="text" name="montant_achat" id="fv-montant" value="" placeholder="0.00" required autocomplete="off" inputmode="decimal">
                </label>
                <label class="field">
                    <span>Titre catalogue</span>
                    <input type="text" name="titre" id="fv-titre" value="" placeholder="Ex. BMW Série 3" autocomplete="off">
                </label>
                <label class="field field--full">
                    <span>Description catalogue</span>
                    <textarea name="description" id="fv-description" rows="3" placeholder="Points forts, état, options…" autocomplete="off"></textarea>
                </label>
                <label class="field">
                    <span>Montant vente</span>
                    <input type="text" name="montant_vente" id="fv-montant-vente" value="" placeholder="0.00" autocomplete="off" inputmode="decimal">
                </label>
                <label class="field field--check">
                    <span>Catalogue public</span>
                    <label class="check">
                        <input type="checkbox" name="en_catalogue" id="fv-catalogue" value="1">
                        Afficher dans le catalogue
                    </label>
                </label>
            </div>

            <div class="fv-photos">
                <span class="fv-photos__label">Importer — 3 angles</span>
                <div class="fv-photos__grid">
                    @foreach ([1 => 'Avant', 2 => 'Côté', 3 => 'Arrière'] as $n => $label)
                        <div class="fv-photo-slot">
                            <span class="fv-photo-slot__label">{{ $label }}</span>
                            <button type="button" class="fv-photo-frame" data-pick-photo="{{ $n }}" title="Importer photo {{ $label }}">
                                <img id="fv-preview-{{ $n }}" alt="Photo {{ $label }}" hidden>
                                <span class="fv-photo-frame__empty" id="fv-empty-{{ $n }}">Importer</span>
                            </button>
                            <input type="file" name="photo_{{ $n }}" id="fv-file-{{ $n }}" accept="image/*" hidden>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel__actions fv-panel-actions">
                <div class="fv-panel-acts" id="fv-panel-acts">
                    <button type="button" class="act" title="Voir" id="btn-fv-voir">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                    <button type="button" class="act" title="Imprimer" id="btn-fv-imprimer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 9V3h12v6"/><path d="M6 17H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><path d="M6 13h12v8H6z"/></svg>
                    </button>
                    <button type="button" class="act" title="Télécharger" id="btn-fv-telecharger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                    </button>
                </div>
                <div class="fv-panel-btns">
                    <button type="submit" class="btn btn--gold" id="btn-valider-fv">Valider</button>
                    <button type="button" class="btn btn--ghost" data-close="panel-fv">Fermer</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Aperçu impression --}}
<div class="overlay overlay--print" id="panel-fv-print" hidden>
    <div class="print-shell" role="dialog" aria-modal="true">
        <div class="print-toolbar no-print">
            <button type="button" class="btn btn--ghost" id="btn-fv-print-close">Fermer</button>
            <button type="button" class="btn btn--gold" id="btn-fv-print-confirm">Imprimer</button>
        </div>
        <div class="print-sheet" id="fv-print-sheet">
            <header class="print-brand">
                <img class="print-brand__logo" src="{{ asset('images/logo-ste-louange-auto.svg') }}" alt="Logo STE LOUANGE AUTO" width="72" height="72">
                <div class="print-brand__text">
                    <p class="print-brand__name">STE LOUANGE AUTO</p>
                    <p class="print-brand__tag">Excellence automobile</p>
                </div>
            </header>
            <h1 class="print-doc-title">Fiche véhicule</h1>
            <p class="print-doc-sub" id="fv-print-date"></p>
            <div class="print-grid" id="fv-print-fields"></div>
            <h2 class="print-section-title">Photo bon d’achat</h2>
            <div class="fv-print-bon" id="fv-print-bon"></div>
            <h2 class="print-section-title">Photos angles</h2>
            <div class="fv-print-photos" id="fv-print-photos"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .fv { gap: 1rem; }
    .module__toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.85rem 1.25rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid var(--line);
    }
    .fv-catalogue {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        min-width: 0;
    }
    .fv-catalogue__eyebrow {
        margin: 0;
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--gold-soft);
    }
    .fv-catalogue__title {
        margin: 0;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: clamp(2rem, 3.2vw, 2.65rem);
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        line-height: 1;
        color: var(--text-strong, var(--mist));
        position: relative;
        padding-bottom: 0.35rem;
    }
    .fv-catalogue__title::after {
        content: "";
        display: block;
        width: 3.25rem;
        height: 2px;
        margin-top: 0.45rem;
        background: linear-gradient(90deg, var(--gold), transparent);
    }
    .module__footer { display: flex; gap: 0.55rem; flex-wrap: wrap; }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        border: 1px solid transparent;
        font: inherit;
        font-size: 0.82rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }
    .btn--gold { background: var(--gold); color: #121212; border-color: var(--gold); }
    .btn--gold:hover { background: var(--gold-soft); }
    .btn--ghost { background: transparent; color: var(--mist); border-color: var(--line); }
    .btn--ghost:hover { border-color: var(--gold); color: var(--gold-soft); }

    .flash { padding: 0.75rem 1rem; border: 1px solid var(--line); font-size: 0.88rem; }
    .flash--ok { background: rgba(109, 191, 138, 0.12); border-color: rgba(109, 191, 138, 0.35); }
    .flash--err { background: rgba(212, 90, 90, 0.12); border-color: rgba(212, 90, 90, 0.35); color: #e8a0a0; }
    html[data-theme="light"] .flash--err { color: #8a2f2f; }

    .fv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(15rem, 1fr));
        gap: 1rem;
        flex: 1;
        align-content: start;
    }
    .fv-empty {
        grid-column: 1 / -1;
        padding: 2.5rem 1rem;
        text-align: center;
        border: 1px dashed var(--line);
        color: var(--muted);
    }
    .fv-card {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
        padding: 0.7rem;
        border: 1px solid var(--line);
        background: var(--panel);
    }
    .fv-card.is-active {
        border-color: var(--gold);
        box-shadow: 0 0 0 1px color-mix(in srgb, var(--gold) 40%, transparent);
    }
    .fv-frame {
        display: block;
        width: 100%;
        aspect-ratio: 4 / 3;
        padding: 0;
        border: 1px solid color-mix(in srgb, var(--gold) 35%, var(--line));
        background: color-mix(in srgb, var(--panel) 88%, #000 12%);
        cursor: pointer;
        overflow: hidden;
        position: relative;
    }
    .fv-frame:hover { border-color: var(--gold); }
    .fv-frame img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .fv-frame__empty {
        position: absolute; inset: 0; display: grid; place-items: center;
        color: var(--muted); font-size: 0.85rem;
    }
    .fv-frame__info {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        padding: 0.55rem 0.65rem;
        background: linear-gradient(transparent, rgba(8, 8, 10, 0.88));
        text-align: left;
        pointer-events: none;
    }
    .fv-frame__desc {
        font-size: 0.82rem;
        font-weight: 600;
        color: #f5f2ea;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fv-frame__desc::before { content: "Description · "; color: rgba(245, 242, 234, 0.65); font-weight: 400; }
    .fv-card__bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.65rem;
        min-height: 2.6rem;
    }
    .fv-card__actions {
        display: flex;
        gap: 0.35rem;
        justify-content: flex-start;
        flex-shrink: 0;
    }
    .fv-card__prix {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.05rem;
        min-width: 0;
        text-align: right;
    }
    .fv-card__prix-label {
        font-size: 0.62rem;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .fv-card__prix-value {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        line-height: 1;
        color: var(--gold-soft);
        font-variant-numeric: tabular-nums lining-nums;
        font-feature-settings: "tnum" 1, "lnum" 1;
        white-space: nowrap;
    }
    .act {
        width: 2rem; height: 2rem; display: grid; place-items: center;
        border: 1px solid var(--line); background: transparent; color: var(--text);
        cursor: pointer; padding: 0;
    }
    .act:hover { border-color: var(--gold); color: var(--gold-soft); }
    .act svg { width: 1.05rem; height: 1.05rem; }

    .overlay {
        position: fixed; inset: 0; z-index: 60;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem; background: rgba(6, 6, 8, 0.65); backdrop-filter: blur(6px);
    }
    .overlay[hidden] { display: none !important; }
    .overlay--photo { z-index: 70; }
    .fv-photo-view {
        width: min(100%, 40rem);
        display: flex; flex-direction: column; align-items: center; gap: 0.85rem;
    }
    .fv-photo-view__img-btn {
        display: block; width: 100%; padding: 0; border: 1px solid var(--gold);
        background: #0c0c0e; cursor: pointer; overflow: hidden; position: relative;
    }
    .fv-photo-view__img-btn img {
        width: 100%; max-height: 70vh; object-fit: contain; display: block;
        background: #111;
    }
    .fv-photo-view__caption {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        background: linear-gradient(transparent, rgba(8, 8, 10, 0.9));
        color: #f5f2ea;
        font-size: 0.9rem;
        text-align: left;
        pointer-events: none;
    }
    .fv-photo-view__prix {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.1rem;
    }
    .fv-photo-view__prix-label {
        font-size: 0.62rem;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(245, 242, 234, 0.65);
    }
    .fv-photo-view__prix strong {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        color: var(--gold-soft);
        font-variant-numeric: tabular-nums lining-nums;
        font-feature-settings: "tnum" 1, "lnum" 1;
        white-space: nowrap;
    }

    .panel {
        width: min(100%, 44rem); background: var(--panel); border: 1px solid var(--line);
        padding: 1.35rem 1.3rem 1.2rem; box-shadow: 0 24px 60px rgba(0,0,0,.4);
        max-height: 92vh; overflow: auto;
    }
    .panel--fv { width: min(100%, 48rem); }
    .panel__title {
        font-family: "Cormorant Garamond", serif; font-size: 1.45rem; font-weight: 600;
        letter-spacing: 0.08em; text-transform: uppercase; color: var(--gold-soft); margin-bottom: 1rem;
    }
    .panel__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.85rem; }
    .field { display: flex; flex-direction: column; gap: 0.35rem; }
    .field span {
        font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted);
    }
    .field input,
    .field textarea {
        background: transparent; border: 1px solid var(--line); color: var(--mist);
        padding: 0.55rem 0.65rem; font: inherit;
    }
    .field textarea { resize: vertical; min-height: 4.5rem; }
    html[data-theme="light"] .field input,
    html[data-theme="light"] .field textarea { background: #fff; color: #1c1b18; }
    .field input:focus,
    .field textarea:focus { border-color: var(--gold); outline: none; }
    .field--full { grid-column: 1 / -1; }
    .field--check .check {
        display: flex; align-items: center; gap: 0.55rem;
        min-height: 2.55rem; font-size: 0.88rem; color: var(--mist); cursor: pointer;
    }
    .field--check input[type="checkbox"] {
        width: 1.05rem; height: 1.05rem; accent-color: var(--gold);
    }
    .panel__msg {
        margin-bottom: 0.85rem; padding: 0.65rem 0.8rem; border: 1px solid var(--line); font-size: 0.88rem;
    }
    .panel__msg.is-ok { border-color: rgba(109, 191, 138, 0.4); }
    .panel__msg.is-err { border-color: rgba(212, 90, 90, 0.4); color: #e8a0a0; }

    .fv-photos { margin-top: 1.15rem; }
    .fv-photos__label {
        display: block; margin-bottom: 0.65rem; font-size: 0.72rem;
        letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted);
    }
    .fv-photos__grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; }
    .fv-photo-slot__label { display: block; margin-bottom: 0.35rem; font-size: 0.75rem; color: var(--gold-soft); }
    .fv-photo-frame {
        display: block; width: 100%; aspect-ratio: 4 / 3; padding: 0;
        border: 1px dashed color-mix(in srgb, var(--gold) 40%, var(--line));
        background: color-mix(in srgb, var(--panel) 90%, #000 10%);
        cursor: pointer; position: relative; overflow: hidden;
    }
    .fv-photo-frame:hover { border-style: solid; border-color: var(--gold); }
    .fv-photo-frame img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .fv-photo-frame__empty {
        position: absolute; inset: 0; display: grid; place-items: center;
        color: var(--muted); font-size: 0.82rem; letter-spacing: 0.06em; text-transform: uppercase;
    }
    .fv-panel-actions {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
        gap: 0.75rem; margin-top: 1.2rem;
    }
    .fv-panel-acts { display: flex; gap: 0.4rem; }
    .fv-panel-btns { display: flex; gap: 0.55rem; flex-wrap: wrap; margin-left: auto; }

    .overlay--print { align-items: flex-start; padding: 1.25rem; overflow: auto; z-index: 90; }
    .print-shell { width: min(100%, 52rem); margin: 0 auto 2rem; display: flex; flex-direction: column; gap: 0.85rem; }
    .print-toolbar {
        display: flex; gap: 0.55rem; justify-content: flex-end; position: sticky; top: 0; z-index: 2;
        padding: 0.55rem 0; background: rgba(6, 6, 8, 0.72); backdrop-filter: blur(6px);
    }
    .print-sheet {
        background: #fff; color: #111; padding: 1.6rem 1.7rem 2rem;
        box-shadow: 0 24px 60px rgba(0,0,0,.45); font-family: Georgia, "Times New Roman", serif;
    }
    .print-brand {
        display: flex; align-items: center; gap: 1rem; padding-bottom: 0.9rem;
        margin-bottom: 0.35rem; border-bottom: 2px solid #c9a227;
    }
    .print-brand__logo { width: 72px; height: 72px; display: block; }
    .print-brand__name {
        margin: 0; font-size: 1.55rem; letter-spacing: 0.12em; text-transform: uppercase;
        font-weight: 700; line-height: 1.15;
    }
    .print-brand__tag {
        margin: 0.15rem 0 0; font-size: 0.72rem; letter-spacing: 0.18em;
        text-transform: uppercase; color: #8a7020;
    }
    .print-doc-title {
        margin: 1rem 0 0.25rem; font-size: 1.1rem; letter-spacing: 0.1em; text-transform: uppercase;
    }
    .print-doc-sub { margin: 0 0 1.1rem; color: #666; font-size: 0.86rem; }
    .print-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.7rem 1.6rem; margin-bottom: 1rem; }
    .print-field span {
        display: block; font-size: 0.68rem; letter-spacing: 0.06em; text-transform: uppercase; color: #666;
    }
    .print-field strong { font-size: 0.98rem; }
    .print-section-title {
        margin: 1.2rem 0 0.55rem; font-size: 1rem; letter-spacing: 0.08em; text-transform: uppercase;
    }
    .fv-print-bon img {
        max-width: 280px; max-height: 200px; object-fit: cover; border: 1px solid #ccc; display: block;
    }
    .fv-print-photos { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; }
    .fv-print-photos figure { margin: 0; }
    .fv-print-photos img {
        width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border: 1px solid #ccc; display: block;
    }
    .fv-print-photos figcaption {
        margin-top: 0.3rem; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.06em; color: #666;
    }

    @media (max-width: 700px) {
        .panel__grid, .fv-photos__grid, .fv-print-photos { grid-template-columns: 1fr; }
    }

    @media print {
        body * { visibility: hidden !important; }
        #panel-fv-print, #panel-fv-print * { visibility: visible !important; }
        #panel-fv-print {
            position: static !important; display: block !important;
            background: #fff !important; padding: 0 !important; overflow: visible !important;
        }
        .print-shell { width: 100% !important; margin: 0 !important; }
        .no-print { display: none !important; }
        .print-sheet { box-shadow: none !important; padding: 0 !important; }
        .print-brand { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const panelPhoto = document.getElementById('panel-fv-photo');
    const panelFv = document.getElementById('panel-fv');
    const panelPrint = document.getElementById('panel-fv-print');
    const formFv = document.getElementById('form-fv');
    const methodInput = document.getElementById('fv-method');
    const titleFv = document.getElementById('panel-fv-title');
    const msgEl = document.getElementById('panel-fv-msg');
    const btnValider = document.getElementById('btn-valider-fv');
    const csrf = formFv.querySelector('input[name="_token"]').value;
    const angleLabels = { 1: 'Avant', 2: 'Côté', 3: 'Arrière' };
    const objectUrls = { 1: null, 2: null, 3: null };

    let currentCard = null;
    let readOnlyMode = false;

    function openPanel(el) { el.hidden = false; }
    function closePanel(el) { el.hidden = true; }

    function showMsg(text, isErr) {
        msgEl.hidden = false;
        msgEl.textContent = text;
        msgEl.classList.toggle('is-err', !!isErr);
        msgEl.classList.toggle('is-ok', !isErr);
    }
    function hideMsg() { msgEl.hidden = true; msgEl.textContent = ''; }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function maskDate(el) {
        var digits = String(el.value || '').replace(/\D/g, '').substring(0, 8);
        var out = digits;
        if (digits.length >= 3 && digits.length <= 4) {
            out = digits.substring(0, 2) + '/' + digits.substring(2);
        } else if (digits.length >= 5) {
            out = digits.substring(0, 2) + '/' + digits.substring(2, 4) + '/' + digits.substring(4);
        }
        if (el.value !== out) el.value = out;
    }
    (function bindDate() {
        var el = document.getElementById('fv-date');
        ['input', 'keyup', 'change', 'blur', 'paste'].forEach(function (evt) {
            el.addEventListener(evt, function () { setTimeout(function () { maskDate(el); }, 0); });
        });
    })();

    function setPhotoPreview(n, url) {
        var img = document.getElementById('fv-preview-' + n);
        var empty = document.getElementById('fv-empty-' + n);
        if (objectUrls[n]) {
            URL.revokeObjectURL(objectUrls[n]);
            objectUrls[n] = null;
        }
        if (url) {
            img.src = url;
            img.hidden = false;
            empty.hidden = true;
        } else {
            img.removeAttribute('src');
            img.hidden = true;
            empty.hidden = false;
        }
    }

    function dataFromCard(card) {
        return {
            bonId: card.dataset.bonId,
            ficheId: card.dataset.ficheId || '',
            date: card.dataset.date || '',
            proprietaire: card.dataset.proprietaire || '',
            marque: card.dataset.marque || '',
            modele: card.dataset.modele || '',
            kilometrage: card.dataset.kilometrage || '',
            couleur: card.dataset.couleur || '',
            montant: card.dataset.montant || '',
            titre: card.dataset.titre || '',
            descCatalogue: card.dataset.descCatalogue || '',
            montantVente: card.dataset.montantVente || '',
            enCatalogue: card.dataset.enCatalogue === '1',
            photoBon: card.dataset.photoBon || '',
            photo1: card.dataset.photo1 || '',
            photo2: card.dataset.photo2 || '',
            photo3: card.dataset.photo3 || '',
            label: card.dataset.label || '',
            description: card.dataset.description || card.dataset.label || ''
        };
    }

    function dataFromForm() {
        return {
            bonId: document.getElementById('fv-bon-id').value,
            ficheId: currentCard ? (currentCard.dataset.ficheId || '') : '',
            date: document.getElementById('fv-date').value,
            proprietaire: document.getElementById('fv-proprietaire').value,
            marque: document.getElementById('fv-marque').value,
            modele: document.getElementById('fv-modele').value,
            kilometrage: document.getElementById('fv-km').value,
            couleur: document.getElementById('fv-couleur').value,
            montant: document.getElementById('fv-montant').value,
            photoBon: currentCard ? (currentCard.dataset.photoBon || '') : '',
            photo1: document.getElementById('fv-preview-1').getAttribute('src') || '',
            photo2: document.getElementById('fv-preview-2').getAttribute('src') || '',
            photo3: document.getElementById('fv-preview-3').getAttribute('src') || '',
            label: currentCard ? (currentCard.dataset.label || '') : ''
        };
    }

    function setActiveCard(card) {
        document.querySelectorAll('.fv-card.is-active').forEach(function (c) {
            c.classList.remove('is-active');
        });
        if (card) card.classList.add('is-active');
        currentCard = card;
    }

    function openPhotoView(card) {
        setActiveCard(card);
        var data = dataFromCard(card);
        document.getElementById('fv-photo-view-img').src = data.photoBon;
        document.getElementById('fv-photo-view-desc').textContent = data.description || data.label
            ? ('Description · ' + (data.description || data.label))
            : '';
        document.getElementById('fv-photo-view-prix').textContent = data.montant || '';
        openPanel(panelPhoto);
    }

    function setFields(data, readOnly) {
        readOnlyMode = !!readOnly;
        document.getElementById('fv-bon-id').value = data.bonId || '';
        var dateEl = document.getElementById('fv-date');
        dateEl.value = data.date || '';
        if (dateEl.value) {
            var d = String(dateEl.value).replace(/\D/g, '').substring(0, 8);
            if (d.length === 8) {
                dateEl.value = d.substring(0, 2) + '/' + d.substring(2, 4) + '/' + d.substring(4);
            }
        }
        document.getElementById('fv-proprietaire').value = data.proprietaire || '';
        document.getElementById('fv-marque').value = data.marque || '';
        document.getElementById('fv-modele').value = data.modele || '';
        document.getElementById('fv-km').value = data.kilometrage || '';
        document.getElementById('fv-couleur').value = data.couleur || '';
        document.getElementById('fv-montant').value = data.montant || '';
        document.getElementById('fv-titre').value = data.titre || '';
        document.getElementById('fv-description').value = data.descCatalogue || '';
        document.getElementById('fv-montant-vente').value = data.montantVente || '';
        document.getElementById('fv-catalogue').checked = !!data.enCatalogue;
        setPhotoPreview(1, data.photo1 || '');
        setPhotoPreview(2, data.photo2 || '');
        setPhotoPreview(3, data.photo3 || '');
        [1, 2, 3].forEach(function (n) {
            document.getElementById('fv-file-' + n).value = '';
        });

        formFv.querySelectorAll('input, textarea').forEach(function (el) {
            if (el.id === 'fv-method' || el.name === '_token' || el.id === 'fv-bon-id') return;
            if (el.type === 'file') {
                el.disabled = !!readOnly;
                return;
            }
            if (el.type === 'checkbox') {
                el.disabled = !!readOnly;
                return;
            }
            el.readOnly = !!readOnly;
            el.disabled = !!readOnly;
        });
        document.querySelectorAll('[data-pick-photo]').forEach(function (btn) {
            btn.style.pointerEvents = readOnly ? 'none' : '';
        });
        btnValider.hidden = !!readOnly;
    }

    function openFichePanel(card, readOnly) {
        hideMsg();
        setActiveCard(card);
        closePanel(panelPhoto);
        var data = dataFromCard(card);
        var ficheId = data.ficheId;
        titleFv.textContent = readOnly ? 'Voir la fiche véhicule' : 'Fiche véhicule';
        if (ficheId) {
            formFv.action = @json(url('/achats/fiche-vehicule')) + '/' + ficheId;
            methodInput.value = 'PUT';
        } else {
            formFv.action = @json(route('achats.fiche-vehicule.store'));
            methodInput.value = 'POST';
        }
        setFields(data, readOnly);
        openPanel(panelFv);
    }

    function fillPrint(data) {
        document.getElementById('fv-print-date').textContent =
            'Document généré le ' + new Date().toLocaleDateString('fr-FR');
        document.getElementById('fv-print-fields').innerHTML =
            '<div class="print-field"><span>Date achat</span><strong>' + escapeHtml(data.date) + '</strong></div>' +
            '<div class="print-field"><span>Nom propriétaire</span><strong>' + escapeHtml(data.proprietaire || '—') + '</strong></div>' +
            '<div class="print-field"><span>Marque</span><strong>' + escapeHtml(data.marque || '—') + '</strong></div>' +
            '<div class="print-field"><span>Modèle</span><strong>' + escapeHtml(data.modele || '—') + '</strong></div>' +
            '<div class="print-field"><span>Kilométrage</span><strong>' + escapeHtml(data.kilometrage || '—') + '</strong></div>' +
            '<div class="print-field"><span>Couleur</span><strong>' + escapeHtml(data.couleur || '—') + '</strong></div>' +
            '<div class="print-field"><span>Montant achat</span><strong>' + escapeHtml(data.montant || '—') + '</strong></div>';

        document.getElementById('fv-print-bon').innerHTML = data.photoBon
            ? '<img src="' + escapeHtml(data.photoBon) + '" alt="Photo bon d’achat">'
            : '<span style="color:#888;">Aucune photo</span>';

        var photosHtml = '';
        [1, 2, 3].forEach(function (n) {
            var url = data['photo' + n] || '';
            photosHtml += '<figure>' +
                (url
                    ? '<img src="' + escapeHtml(url) + '" alt="' + angleLabels[n] + '">'
                    : '<div style="aspect-ratio:4/3;border:1px dashed #ccc;display:grid;place-items:center;color:#999;font-size:12px;">Sans photo</div>') +
                '<figcaption>' + angleLabels[n] + '</figcaption></figure>';
        });
        document.getElementById('fv-print-photos').innerHTML = photosHtml;
        openPanel(panelPrint);
    }

    function downloadFiche(data) {
        fillPrint(data);
        setTimeout(function () {
            var sheet = document.getElementById('fv-print-sheet');
            var html = '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><title>Fiche véhicule — STE LOUANGE AUTO</title>' +
                '<style>body{font-family:Georgia,serif;margin:24px;color:#111}' +
                '.print-brand{display:flex;align-items:center;gap:16px;padding-bottom:14px;border-bottom:2px solid #c9a227}' +
                '.print-brand__logo{width:72px;height:72px}' +
                '.print-brand__name{margin:0;font-size:26px;letter-spacing:.12em;text-transform:uppercase;font-weight:700}' +
                '.print-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px 24px;margin:16px 0}' +
                '.fv-print-photos{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}' +
                '.fv-print-photos img,.fv-print-bon img{max-width:100%;border:1px solid #ccc}</style></head><body>' +
                sheet.innerHTML + '</body></html>';
            var blob = new Blob([html], { type: 'text/html;charset=utf-8' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'fiche-vehicule-ste-louange-auto.html';
            a.click();
            URL.revokeObjectURL(a.href);
        }, 80);
    }

    document.getElementById('fv-grid').addEventListener('click', function (e) {
        var actBtn = e.target.closest('[data-action]');
        var photoBtn = e.target.closest('[data-photo-click]');
        var card = e.target.closest('.fv-card');
        if (!card) return;

        if (actBtn) {
            var action = actBtn.getAttribute('data-action');
            if (action === 'voir') openFichePanel(card, true);
            if (action === 'imprimer') fillPrint(dataFromCard(card));
            if (action === 'telecharger') downloadFiche(dataFromCard(card));
            return;
        }

        if (photoBtn) {
            if (currentCard === card && !panelPhoto.hidden) {
                openFichePanel(card, false);
            } else {
                openPhotoView(card);
            }
        }
    });

    document.getElementById('fv-photo-reclic').addEventListener('click', function () {
        if (currentCard) openFichePanel(currentCard, false);
    });
    document.getElementById('btn-fv-photo-close').addEventListener('click', function () {
        closePanel(panelPhoto);
    });

    document.querySelectorAll('[data-pick-photo]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (readOnlyMode) return;
            document.getElementById('fv-file-' + btn.getAttribute('data-pick-photo')).click();
        });
    });
    [1, 2, 3].forEach(function (n) {
        document.getElementById('fv-file-' + n).addEventListener('change', function () {
            var file = this.files[0];
            if (file && file.type.indexOf('image/') === 0) {
                if (objectUrls[n]) URL.revokeObjectURL(objectUrls[n]);
                objectUrls[n] = URL.createObjectURL(file);
                setPhotoPreview(n, objectUrls[n]);
            }
        });
    });

    document.getElementById('fv-montant').addEventListener('blur', function () {
        var raw = (this.value || '').replace(',', '.').replace(/\s/g, '');
        if (raw !== '' && !isNaN(raw)) this.value = Number(raw).toFixed(2);
    });
    document.getElementById('fv-montant-vente').addEventListener('blur', function () {
        var raw = (this.value || '').replace(',', '.').replace(/\s/g, '');
        if (raw !== '' && !isNaN(raw)) this.value = Number(raw).toFixed(2);
    });

    document.getElementById('btn-fv-voir').addEventListener('click', function () {
        setFields(dataFromForm(), true);
        titleFv.textContent = 'Voir la fiche véhicule';
    });
    document.getElementById('btn-fv-imprimer').addEventListener('click', function () {
        fillPrint(dataFromForm());
    });
    document.getElementById('btn-fv-telecharger').addEventListener('click', function () {
        downloadFiche(dataFromForm());
    });

    formFv.addEventListener('submit', function (e) {
        e.preventDefault();
        hideMsg();
        btnValider.disabled = true;
        btnValider.textContent = '…';

        var fd = new FormData(formFv);
        if (methodInput.value === 'PUT') fd.set('_method', 'PUT');

        fetch(formFv.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: fd
        })
        .then(function (res) {
            return res.json().then(function (data) {
                if (!res.ok) {
                    var msg = data.message || 'Erreur.';
                    if (data.errors) msg = Object.values(data.errors).flat().join(' ');
                    throw new Error(msg);
                }
                return data;
            });
        })
        .then(function (data) {
            showMsg(data.message || 'Enregistré.', false);
            if (data.fiche && currentCard) {
                currentCard.dataset.ficheId = data.fiche.id;
                currentCard.dataset.date = data.fiche.date_achat || '';
                currentCard.dataset.proprietaire = data.fiche.nom_proprietaire || '';
                currentCard.dataset.marque = data.fiche.marque || '';
                currentCard.dataset.modele = data.fiche.modele || '';
                currentCard.dataset.kilometrage = data.fiche.kilometrage || '';
                currentCard.dataset.couleur = data.fiche.couleur || '';
                currentCard.dataset.montant = data.fiche.montant_achat || '';
                currentCard.dataset.titre = data.fiche.titre || '';
                currentCard.dataset.descCatalogue = data.fiche.description || '';
                currentCard.dataset.montantVente = data.fiche.montant_vente || '';
                currentCard.dataset.enCatalogue = data.fiche.en_catalogue ? '1' : '0';
                currentCard.dataset.photo1 = data.fiche.photo_1 || '';
                currentCard.dataset.photo2 = data.fiche.photo_2 || '';
                currentCard.dataset.photo3 = data.fiche.photo_3 || '';
                formFv.action = @json(url('/achats/fiche-vehicule')) + '/' + data.fiche.id;
                methodInput.value = 'PUT';
            }
        })
        .catch(function (err) {
            showMsg(err.message || 'Erreur.', true);
        })
        .finally(function () {
            btnValider.disabled = false;
            btnValider.textContent = 'Valider';
        });
    });

    document.querySelectorAll('[data-close]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closePanel(document.getElementById(btn.getAttribute('data-close')));
        });
    });
    document.getElementById('btn-fv-print-close').addEventListener('click', function () {
        closePanel(panelPrint);
    });
    document.getElementById('btn-fv-print-confirm').addEventListener('click', function () {
        window.print();
    });

    [panelPhoto, panelFv, panelPrint].forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closePanel(overlay);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (!panelPrint.hidden) closePanel(panelPrint);
        else if (!panelFv.hidden) closePanel(panelFv);
        else if (!panelPhoto.hidden) closePanel(panelPhoto);
    });
})();
</script>
@endpush
