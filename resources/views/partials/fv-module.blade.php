@php
    $closeUrl = $closeUrl ?? null;
    $addButton = $addButton ?? true;
    $marques = \App\Models\FicheVehicule::MARQUES;
    $carburants = \App\Models\FicheVehicule::CARBURANTS;
    $transmissions = \App\Models\FicheVehicule::TRANSMISSIONS;
@endphp
<div class="module fv">
    <div class="module__toolbar">
        <header class="fv-catalogue">
            <h2 class="fv-catalogue__title">Catalogue</h2>
        </header>
        @if ($addButton || $closeUrl)
            <div class="module__footer">
                @if ($addButton)
                    <button type="button" class="btn btn--gold" id="btn-fv-add">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        Ajouter
                    </button>
                @endif
                @if ($closeUrl)
                    <a href="{{ $closeUrl }}" class="btn btn--ghost">Fermer</a>
                @endif
            </div>
        @endif
    </div>

    <div class="fv-grid" id="fv-grid" data-fv-grid>
        @forelse ($fiches as $fiche)
            @include('partials.fv-card', ['fiche' => $fiche, 'interne' => true])
        @empty
            <div class="fv-empty">Aucun véhicule. Cliquez sur « Ajouter » pour créer une fiche.</div>
        @endforelse
    </div>

    @if ($fiches instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div class="module__pager">{{ $fiches->links() }}</div>
    @endif
</div>

@include('partials.fv-catalogue', ['payloads' => $payloads, 'editable' => true])

{{-- Panneau ajout / modification --}}
<div class="overlay" id="panel-fv-form" hidden>
    <div class="panel panel--fv fx" role="dialog" aria-modal="true" aria-labelledby="panel-fv-title">
        <form method="POST" action="{{ route('achats.fiche-vehicule.store') }}" id="form-fv" class="fx__form" autocomplete="off" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="_method" id="fv-method" value="POST">

            <header class="fx__head">
                <span class="fx__head-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 17h14M3 13l2-5.5A2 2 0 0 1 6.9 6h10.2a2 2 0 0 1 1.9 1.5L21 13v4a1 1 0 0 1-1 1h-1.5M3 13v4a1 1 0 0 0 1 1h1.5M3 13h18"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
                </span>
                <div class="fx__head-text">
                    <p class="fx__eyebrow">Fiche véhicule</p>
                    <h2 class="fx__title" id="panel-fv-title">Ajouter un véhicule</h2>
                </div>
                <button type="button" class="fx__close" data-close="panel-fv-form" title="Fermer" aria-label="Fermer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </header>

            <div class="fx__body">
            <div class="panel__msg" id="panel-fv-msg" hidden></div>

            <section class="fx__section">
                <div class="fx__section-head">
                    <span class="fx__num">1</span>
                    <div>
                        <h3>Photos</h3>
                        <p>1 photo principale + 6 photos secondaires</p>
                    </div>
                    <button type="button" class="btn btn--ghost btn--sm" id="btn-fv-multi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="15" height="15" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.6"/><path d="M21 16l-5-5-8 8"/></svg>
                        Sélectionner des photos
                    </button>
                </div>
                <input type="file" id="fv-file-multi" accept="image/*" multiple hidden>
                <div class="fv-photos__layout">
                    @foreach (\App\Models\FicheVehicule::PHOTOS as $i => $field)
                        @php $n = $i + 1; @endphp
                        <div class="fv-slot {{ $n === 1 ? 'fv-slot--main' : '' }}">
                            <button type="button" class="fv-photo-frame" data-pick="{{ $n }}" title="{{ $n === 1 ? 'Photo principale' : 'Photo secondaire '.($n - 1) }}">
                                <img id="fv-preview-{{ $n }}" alt="" hidden>
                                <span class="fv-photo-frame__empty" id="fv-empty-{{ $n }}">
                                    {{ $n === 1 ? '+ Photo principale' : '+ Photo '.($n - 1) }}
                                </span>
                            </button>
                            <button type="button" class="fv-slot__remove" data-remove="{{ $n }}" id="fv-remove-btn-{{ $n }}" title="Retirer la photo" aria-label="Retirer la photo" hidden>&times;</button>
                            <input type="hidden" name="remove_{{ $field }}" id="fv-remove-{{ $n }}" value="0">
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="fx__section">
                <div class="fx__section-head">
                    <span class="fx__num">2</span>
                    <div>
                        <h3>Identification</h3>
                        <p>Marque, modèle et années</p>
                    </div>
                </div>
                <div class="fx__grid">
                    <div class="field">
                        <label class="field__label" for="fv-marque-select">Marque <b>*</b></label>
                        <select id="fv-marque-select" data-label="Marque">
                            <option value="" disabled selected>— Sélectionner une marque —</option>
                            @foreach ($marques as $marque)
                                <option value="{{ $marque }}">{{ $marque }}</option>
                            @endforeach
                            <option value="__autre">Autre marque…</option>
                        </select>
                        <input type="text" name="marque" id="fv-marque" value="" maxlength="255" placeholder="Préciser la marque" autocomplete="off" hidden>
                    </div>
                    <div class="field">
                        <label class="field__label" for="fv-modele">Modèle <b>*</b></label>
                        <input type="text" name="modele" id="fv-modele" value="" maxlength="255" placeholder="Ex. Clio 5, Série 3, Tucson" required autocomplete="off" data-label="Modèle">
                    </div>
                    <div class="field">
                        <label class="field__label" for="fv-annee">Année <b>*</b></label>
                        <input type="number" name="annee" id="fv-annee" value="" min="1950" max="{{ now()->year + 1 }}" step="1" placeholder="{{ now()->year }}" required autocomplete="off" inputmode="numeric" data-label="Année">
                    </div>
                    <div class="field">
                        <label class="field__label" for="fv-annee-douane">Année douane</label>
                        <input type="number" name="annee_douane" id="fv-annee-douane" value="" min="1950" max="{{ now()->year + 1 }}" step="1" placeholder="—" autocomplete="off" inputmode="numeric">
                    </div>
                </div>
            </section>

            <section class="fx__section">
                <div class="fx__section-head">
                    <span class="fx__num">3</span>
                    <div>
                        <h3>Caractéristiques</h3>
                        <p>Kilométrage, motorisation et boîte</p>
                    </div>
                </div>
                <div class="fx__grid">
                    <div class="field">
                        <label class="field__label" for="fv-km">Kilométrage <b>*</b></label>
                        <div class="fv-input">
                            <input type="number" name="kilometrage" id="fv-km" value="" min="0" step="1" placeholder="0" required autocomplete="off" inputmode="numeric" data-label="Kilométrage">
                            <em>km</em>
                        </div>
                    </div>
                    <div class="field">
                        <label class="field__label" for="fv-cv">Puissance fiscale</label>
                        <div class="fv-input">
                            <input type="number" name="puissance_fiscale" id="fv-cv" value="" min="1" max="99" step="1" placeholder="—" autocomplete="off" inputmode="numeric">
                            <em>CV</em>
                        </div>
                    </div>
                    <div class="field field--full">
                        <span class="field__label">Carburant <b>*</b></span>
                        <div class="fv-choice" role="radiogroup" aria-label="Carburant">
                            @foreach ($carburants as $value => $label)
                                <label class="fv-choice__opt">
                                    <input type="radio" name="carburant" value="{{ $value }}">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="field field--full">
                        <span class="field__label">Transmission <b>*</b></span>
                        <div class="fv-choice fv-choice--2" role="radiogroup" aria-label="Transmission">
                            @foreach ($transmissions as $value => $label)
                                <label class="fv-choice__opt">
                                    <input type="radio" name="transmission" value="{{ $value }}">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="fx__section">
                <div class="fx__section-head">
                    <span class="fx__num">4</span>
                    <div>
                        <h3>Prix &amp; coûts</h3>
                        <p>Coût de revient et prix de vente</p>
                    </div>
                </div>
                <div class="fx__grid">
                    <div class="field">
                        <label class="field__label" for="fv-achat">Prix d’achat</label>
                        <div class="fv-input">
                            <input type="text" name="montant_achat" id="fv-achat" value="" placeholder="0.00" autocomplete="off" inputmode="decimal" data-money>
                            <em>MAD</em>
                        </div>
                    </div>
                    <div class="field">
                        <label class="field__label" for="fv-depenses">Montant des dépenses</label>
                        <div class="fv-input">
                            <input type="text" name="montant_depenses" id="fv-depenses" value="" placeholder="0.00" autocomplete="off" inputmode="decimal" data-money>
                            <em>MAD</em>
                        </div>
                    </div>
                    <div class="field field--full">
                        <div class="fv-cost-total">
                            <span>Coût total <small>(achat + dépenses)</small></span>
                            <strong id="fv-cout-total">0 MAD</strong>
                        </div>
                    </div>
                    <div class="field field--full">
                        <label class="field__label" for="fv-prix">Prix de vente <b>*</b></label>
                        <div class="fv-input fv-input--prix">
                            <input type="text" name="montant_vente" id="fv-prix" value="" placeholder="0.00" required autocomplete="off" inputmode="decimal" data-label="Prix" data-money>
                            <em>MAD</em>
                        </div>
                    </div>
                </div>
            </section>
            </div>

            <footer class="fx__foot">
                <span class="fx__hint"><b>*</b> Champs obligatoires</span>
                <button type="button" class="btn btn--ghost" data-close="panel-fv-form">Annuler</button>
                <button type="submit" class="btn btn--gold" id="btn-valider-fv">Enregistrer</button>
            </footer>
        </form>
    </div>
</div>

<style>
    .fv { display: flex; flex-direction: column; gap: 1rem; }
    .module__toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.85rem 1.25rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid var(--line);
    }
    .fv-catalogue { display: flex; flex-direction: column; gap: 0.2rem; min-width: 0; }
    .fv-catalogue__title {
        margin: 0;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: clamp(2rem, 3.2vw, 2.65rem);
        font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; line-height: 1;
        color: var(--text-strong, var(--mist));
        padding-bottom: 0.35rem;
    }
    .fv-catalogue__title::after {
        content: ""; display: block; width: 3.25rem; height: 2px; margin-top: 0.45rem;
        background: linear-gradient(90deg, var(--gold), transparent);
    }
    .module__footer { display: flex; gap: 0.55rem; flex-wrap: wrap; }

    .btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
        padding: 0.55rem 1.1rem; border: 1px solid transparent;
        font: inherit; font-size: 0.82rem; letter-spacing: 0.06em; text-transform: uppercase;
        text-decoration: none; cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }
    .btn--gold { background: var(--gold); color: #121212; border-color: var(--gold); }
    .btn--gold:hover { background: var(--gold-soft); }
    .btn--gold:disabled { opacity: 0.6; cursor: wait; }
    .btn--ghost { background: transparent; color: var(--mist); border-color: var(--line); }
    .btn--ghost:hover { border-color: var(--gold); color: var(--gold-soft); }

    .fv-grid { flex: 1; }

    /* Overlays */
    .overlay {
        position: fixed; inset: 0; z-index: 60;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem; background: rgba(6, 6, 8, 0.7); backdrop-filter: blur(6px);
    }
    .overlay[hidden] { display: none !important; }
    .panel {
        width: min(100%, 46rem); background: var(--panel); border: 1px solid var(--line);
        padding: 1.6rem 1.8rem 1.5rem; box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
        max-height: 92vh; overflow: auto; scrollbar-width: none;
    }
    .panel::-webkit-scrollbar { display: none; }
    /* Formulaire */
    .panel.fx {
        width: min(100%, 54rem); padding: 0; overflow: hidden;
        display: flex; flex-direction: column;
    }
    .fx__form { display: flex; flex-direction: column; min-height: 0; max-height: 92vh; }

    .fx__head {
        display: flex; align-items: center; gap: 0.95rem;
        padding: 1.35rem 2.2rem 1.2rem;
        border-bottom: 1px solid var(--line);
        background: linear-gradient(120deg, color-mix(in srgb, var(--gold) 14%, transparent) 0%, transparent 60%);
    }
    .fx__head-icon {
        flex-shrink: 0; width: 2.9rem; height: 2.9rem; display: grid; place-items: center;
        border-radius: 50%; border: 1px solid color-mix(in srgb, var(--gold) 60%, transparent);
        background: color-mix(in srgb, var(--gold) 12%, transparent); color: var(--gold-soft);
    }
    .fx__head-icon svg { width: 1.5rem; height: 1.5rem; }
    .fx__head-text { min-width: 0; flex: 1; }
    .fx__eyebrow {
        margin: 0; font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 0.66rem; font-weight: 600; letter-spacing: 0.24em; text-transform: uppercase; color: var(--gold-soft);
    }
    .fx__title {
        margin: 0.1rem 0 0; font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 1.7rem; font-weight: 600; letter-spacing: 0.04em; line-height: 1.1;
        color: var(--text-strong, var(--mist));
    }
    .fx__close {
        flex-shrink: 0; width: 2.4rem; height: 2.4rem; display: grid; place-items: center; padding: 0;
        border: 1px solid var(--line); border-radius: 50%; background: transparent; color: var(--muted); cursor: pointer;
        transition: border-color 0.2s ease, color 0.2s ease;
    }
    .fx__close:hover { border-color: var(--gold); color: var(--gold-soft); }
    .fx__close svg { width: 1.1rem; height: 1.1rem; }

    .fx__body {
        flex: 1; min-height: 0; overflow-y: auto; padding: 1.3rem 2.2rem;
        display: flex; flex-direction: column; gap: 1rem;
        scrollbar-width: none;
    }
    .fx__body::-webkit-scrollbar { display: none; }

    .fx__section {
        padding: 1.15rem 1.35rem 1.3rem;
        border: 1px solid var(--line); border-radius: 26px;
        background: color-mix(in srgb, var(--panel) 90%, #000 10%);
    }
    html[data-theme="light"] .fx__section { background: #faf8f3; }
    .fx__section-head { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
    .fx__section-head > div { min-width: 0; flex: 1; }
    .fx__section-head h3 {
        margin: 0; font-size: 0.9rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase;
        color: var(--text-strong, var(--mist));
    }
    .fx__section-head p { margin: 0.1rem 0 0; font-size: 0.76rem; color: var(--muted); }
    .fx__num {
        flex-shrink: 0; width: 1.75rem; height: 1.75rem; display: grid; place-items: center;
        border-radius: 50%; background: var(--gold); color: #121212;
        font-family: "Space Grotesk", "Outfit", sans-serif; font-size: 0.8rem; font-weight: 700;
    }
    .fx__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.95rem 1rem; }

    .field { display: flex; flex-direction: column; gap: 0.4rem; min-width: 0; }
    .field__label {
        font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted);
    }
    .field__label b, .fx__hint b { color: var(--gold-soft); font-weight: 700; }
    .field input, .field select {
        width: 100%; min-height: 2.8rem;
        background: rgba(0, 0, 0, 0.25); border: 1px solid var(--line); color: var(--mist);
        padding: 0.6rem 1.1rem; font: inherit; font-size: 0.92rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .field input::placeholder { color: color-mix(in srgb, var(--muted) 70%, transparent); }
    .field select { cursor: pointer; color-scheme: dark; }
    .field select option { background: #1e1e22; color: #f2f0ec; }
    html[data-theme="light"] .field input,
    html[data-theme="light"] .field select { background: #fff; color: #1c1b18; color-scheme: light; }
    html[data-theme="light"] .field select option { background: #fff; color: #1c1b18; }
    .field input:focus, .field select:focus {
        border-color: var(--gold); outline: none;
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--gold) 20%, transparent);
    }
    .field input[type="number"] { -moz-appearance: textfield; appearance: textfield; }
    .field input[type="number"]::-webkit-outer-spin-button,
    .field input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .field--full { grid-column: 1 / -1; }
    #fv-marque:not([hidden]) { margin-top: 0.45rem; }

    .fv-input { position: relative; }
    .fv-input input { padding-right: 3.6rem; }
    .fv-input em {
        position: absolute; right: 1.1rem; top: 50%; transform: translateY(-50%);
        font-style: normal; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em;
        color: var(--muted); pointer-events: none;
    }
    .fv-input--prix input {
        min-height: 3.3rem; padding: 0.7rem 4.5rem 0.7rem 1.35rem;
        font-family: "Space Grotesk", "Outfit", sans-serif; font-size: 1.35rem; font-weight: 700;
        color: var(--gold-soft); letter-spacing: 0.02em;
    }
    .fv-input--prix em { font-size: 0.85rem; color: var(--gold-soft); }
    .fv-cost-total {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.75rem 1.2rem; border-radius: 999px;
        border: 1px solid color-mix(in srgb, #6aa3d4 45%, var(--line));
        background: color-mix(in srgb, #6aa3d4 10%, transparent);
    }
    .fv-cost-total span {
        font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #8fbbe0;
    }
    .fv-cost-total small { text-transform: none; letter-spacing: 0; font-weight: 400; color: var(--muted); }
    .fv-cost-total strong {
        font-family: "Space Grotesk", "Outfit", sans-serif; font-size: 1.15rem; font-weight: 700;
        color: var(--text-strong, var(--mist)); white-space: nowrap;
    }
    html[data-theme="light"] .fv-cost-total span { color: #2f6a9e; }

    .fv-choice { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.5rem; }
    .fv-choice--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .fv-choice__opt { position: relative; cursor: pointer; }
    .fv-choice__opt input { position: absolute; opacity: 0; pointer-events: none; }
    .fv-choice__opt span {
        display: flex; align-items: center; justify-content: center; min-height: 2.7rem; padding: 0.5rem 0.6rem;
        border: 1px solid var(--line); border-radius: 999px;
        font-size: 0.85rem; color: var(--mist); text-align: center;
        transition: border-color 0.2s ease, background 0.2s ease, color 0.2s ease;
    }
    .fv-choice__opt:hover span { border-color: color-mix(in srgb, var(--gold) 60%, var(--line)); }
    .fv-choice__opt input:checked + span {
        border-color: var(--gold); color: var(--gold-soft); font-weight: 600;
        background: color-mix(in srgb, var(--gold) 16%, transparent);
    }
    .fv-choice__opt input:focus-visible + span { box-shadow: 0 0 0 3px color-mix(in srgb, var(--gold) 25%, transparent); }

    .panel__msg {
        padding: 0.7rem 1.2rem; border: 1px solid var(--line);
        border-radius: var(--radius); font-size: 0.88rem;
    }
    .panel__msg.is-ok { border-color: rgba(109, 191, 138, 0.4); color: #8fd6a8; }
    .panel__msg.is-err { border-color: rgba(212, 90, 90, 0.4); color: #e8a0a0; }
    html[data-theme="light"] .panel__msg.is-ok { color: #2f7a4a; }
    html[data-theme="light"] .panel__msg.is-err { color: #8a2f2f; }

    .fx__foot {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 1rem 2.2rem 1.15rem; border-top: 1px solid var(--line);
        background: color-mix(in srgb, var(--panel) 94%, #000 6%);
    }
    .fx__hint { margin-right: auto; font-size: 0.75rem; color: var(--muted); }
    .fx__foot .btn { min-width: 8.5rem; }

    .btn--sm { padding: 0.45rem 1rem; font-size: 0.72rem; }
    .fv-photos__layout {
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.6rem;
    }
    .fv-slot { position: relative; min-width: 0; }
    .fv-slot--main { grid-column: 1 / 3; grid-row: 1 / 3; }
    .fv-photo-frame {
        display: block; width: 100%; height: 100%; min-height: 4.5rem; aspect-ratio: 4 / 3; padding: 0;
        border: 1px dashed color-mix(in srgb, var(--gold) 40%, var(--line));
        background: color-mix(in srgb, var(--panel) 90%, #000 10%);
        cursor: pointer; position: relative; overflow: hidden;
    }
    .fv-slot:not(.fv-slot--main) .fv-photo-frame { border-radius: 14px; }
    .fv-slot--main .fv-photo-frame { border-radius: calc(var(--radius) * 0.6); }
    .fv-photo-frame:hover { border-style: solid; border-color: var(--gold); }
    .fv-photo-frame img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .fv-photo-frame__empty {
        position: absolute; inset: 0; display: grid; place-items: center; padding: 0.3rem;
        color: var(--muted); font-size: 0.72rem; letter-spacing: 0.04em; text-transform: uppercase; text-align: center;
    }
    .fv-slot--main .fv-photo-frame__empty { font-size: 0.82rem; color: var(--gold-soft); }
    .fv-slot__remove {
        position: absolute; top: 0.35rem; right: 0.35rem;
        width: 1.6rem; height: 1.6rem; display: grid; place-items: center; padding: 0;
        border: 1px solid rgba(242, 240, 236, 0.35); border-radius: 50%;
        background: rgba(8, 8, 10, 0.75); color: #f2f0ec; font-size: 1.05rem; line-height: 1; cursor: pointer;
    }
    .fv-slot--main .fv-slot__remove { top: 0.8rem; right: 1rem; }
    .fv-slot__remove:hover { border-color: #d45a5a; color: #ff9a9a; }
    .fv-slot__remove[hidden] { display: none; }

    @media (max-width: 700px) {
        .overlay { padding: 0.5rem; align-items: flex-end; }
        .panel { padding: 1.3rem 1.1rem 1.2rem; max-height: 94vh; border-radius: 28px 28px 0 0 !important; }
        .panel.fx { padding: 0; }
        .fx__form { max-height: 94vh; }
        .fx__head { padding: 1.1rem 1.25rem 1rem; }
        .fx__title { font-size: 1.4rem; }
        .field input, .field select { font-size: 1rem; }
        .fv-input--prix input { font-size: 1.35rem; }
        .fx__body { padding: 1rem 0.9rem; gap: 0.8rem; }
        .fx__section { padding: 1rem 1rem 1.1rem; border-radius: 22px; }
        .fx__section-head { flex-wrap: wrap; }
        .fx__section-head .btn { width: 100%; }
        .fx__grid { grid-template-columns: 1fr; }
        .fv-choice { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .fx__foot { padding: 0.85rem 1rem calc(0.9rem + env(safe-area-inset-bottom)); flex-wrap: wrap; }
        .fx__hint { width: 100%; text-align: center; margin: 0 0 0.2rem; }
        .fx__foot .btn { flex: 1 1 0; min-width: 0; }
        .fv-photos__layout { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .fv-slot--main { grid-column: 1 / -1; grid-row: auto; }
        .module__footer { width: 100%; }
        .module__footer .btn { flex: 1 1 0; }
    }
</style>

<script>
(function () {
    var baseUrl = @json(url('/achats/fiche-vehicule'));
    var storeUrl = @json(route('achats.fiche-vehicule.store'));
    var photoKeys = ['photo_1', 'photo_2', 'photo_3', 'photo_4', 'photo_5', 'photo_6', 'photo_7'];

    var panelForm = document.getElementById('panel-fv-form');
    var formFv = document.getElementById('form-fv');
    var methodInput = document.getElementById('fv-method');
    var titleForm = document.getElementById('panel-fv-title');
    var msgEl = document.getElementById('panel-fv-msg');
    var btnValider = document.getElementById('btn-valider-fv');
    var csrf = formFv.querySelector('input[name="_token"]').value;

    var current = null;
    var objectUrls = {};
    var slotFiles = {};
    var targetSlot = null;
    var multiInput = document.getElementById('fv-file-multi');

    function open(el) { el.hidden = false; document.body.style.overflow = 'hidden'; }
    function close(el) { el.hidden = true; document.body.style.overflow = ''; }

    document.getElementById('fvd-edit').addEventListener('click', function () {
        var f = window.FvDetail.current();
        if (!f) return;
        window.FvDetail.close();
        openForm(f);
    });

    function showMsg(text, isErr) {
        msgEl.hidden = false;
        msgEl.textContent = text;
        msgEl.classList.toggle('is-err', !!isErr);
        msgEl.classList.toggle('is-ok', !isErr);
    }
    function hideMsg() { msgEl.hidden = true; msgEl.textContent = ''; }

    function setPreview(n, url) {
        var img = document.getElementById('fv-preview-' + n);
        var empty = document.getElementById('fv-empty-' + n);
        var rm = document.getElementById('fv-remove-btn-' + n);
        if (objectUrls[n]) {
            URL.revokeObjectURL(objectUrls[n]);
            objectUrls[n] = null;
        }
        if (url) {
            img.src = url;
            img.hidden = false;
            empty.hidden = true;
            rm.hidden = false;
        } else {
            img.removeAttribute('src');
            img.hidden = true;
            empty.hidden = false;
            rm.hidden = true;
        }
    }

    var formBody = panelForm.querySelector('.fx__body');
    var marqueSelect = document.getElementById('fv-marque-select');
    var marqueInput = document.getElementById('fv-marque');

    function setMarque(value) {
        var known = Array.prototype.some.call(marqueSelect.options, function (o) {
            return o.value === value && value !== '' && value !== '__autre';
        });
        if (!value) {
            marqueSelect.value = '';
            marqueInput.hidden = true;
        } else if (known) {
            marqueSelect.value = value;
            marqueInput.hidden = true;
        } else {
            marqueSelect.value = '__autre';
            marqueInput.hidden = false;
        }
        marqueInput.value = value;
    }

    marqueSelect.addEventListener('change', function () {
        if (this.value === '__autre') {
            marqueInput.value = '';
            marqueInput.hidden = false;
            marqueInput.focus();
        } else {
            marqueInput.value = this.value;
            marqueInput.hidden = true;
        }
    });

    function setRadio(name, value) {
        formFv.querySelectorAll('input[name="' + name + '"]').forEach(function (r) {
            r.checked = !!value && r.value === value;
        });
    }

    function missingFields() {
        var missing = [];
        if (!marqueInput.value.trim()) missing.push('Marque');
        formFv.querySelectorAll('input[required][data-label]').forEach(function (el) {
            if (!String(el.value || '').trim()) missing.push(el.getAttribute('data-label'));
        });
        if (!formFv.querySelector('input[name="carburant"]:checked')) missing.push('Carburant');
        if (!formFv.querySelector('input[name="transmission"]:checked')) missing.push('Transmission');
        return missing;
    }

    function openForm(f) {
        hideMsg();
        formFv.reset();
        current = f || null;

        if (f) {
            titleForm.textContent = 'Modifier le véhicule';
            formFv.action = baseUrl + '/' + f.id;
            methodInput.value = 'PUT';
        } else {
            titleForm.textContent = 'Ajouter un véhicule';
            formFv.action = storeUrl;
            methodInput.value = 'POST';
        }

        setMarque(f ? (f.marque || '') : '');
        document.getElementById('fv-modele').value = f ? (f.modele || '') : '';
        document.getElementById('fv-km').value = f && f.kilometrage !== null ? f.kilometrage : '';
        document.getElementById('fv-annee').value = f && f.annee ? f.annee : '';
        document.getElementById('fv-annee-douane').value = f && f.annee_douane ? f.annee_douane : '';
        setRadio('carburant', f ? f.carburant : '');
        setRadio('transmission', f ? f.transmission : '');
        document.getElementById('fv-cv').value = f && f.puissance_fiscale ? f.puissance_fiscale : '';
        document.getElementById('fv-prix').value = f ? (f.prix || '') : '';
        document.getElementById('fv-achat').value = f ? (f.prix_achat || '') : '';
        document.getElementById('fv-depenses').value = f ? (f.depenses || '') : '';
        updateCoutTotal();

        multiInput.value = '';
        photoKeys.forEach(function (k, i) {
            var n = i + 1;
            slotFiles[n] = null;
            document.getElementById('fv-remove-' + n).value = '0';
            setPreview(n, f && f.photos ? f.photos[k] : null);
        });

        formBody.scrollTop = 0;
        open(panelForm);
    }

    document.getElementById('btn-fv-add').addEventListener('click', function () { openForm(null); });

    function slotIsFilled(n) {
        return !document.getElementById('fv-preview-' + n).hidden;
    }

    function putFileInSlot(n, file) {
        slotFiles[n] = file;
        document.getElementById('fv-remove-' + n).value = '0';
        setPreview(n, null);
        objectUrls[n] = URL.createObjectURL(file);
        setPreview(n, objectUrls[n]);
    }

    function pickPhotos(startSlot) {
        targetSlot = startSlot;
        multiInput.value = '';
        multiInput.click();
    }

    document.getElementById('btn-fv-multi').addEventListener('click', function () {
        var firstEmpty = null;
        for (var n = 1; n <= photoKeys.length; n++) {
            if (!slotIsFilled(n)) { firstEmpty = n; break; }
        }
        pickPhotos(firstEmpty);
    });
    document.querySelectorAll('[data-pick]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            pickPhotos(Number(btn.getAttribute('data-pick')));
        });
    });

    multiInput.addEventListener('change', function () {
        var files = Array.prototype.filter.call(this.files || [], function (f) {
            return f.type.indexOf('image/') === 0;
        });
        if (!files.length) return;

        var slots = [];
        if (targetSlot) slots.push(targetSlot);
        for (var n = 1; n <= photoKeys.length; n++) {
            if (n !== targetSlot && !slotIsFilled(n)) slots.push(n);
        }
        if (!targetSlot && !slots.length) {
            for (var m = 1; m <= photoKeys.length; m++) slots.push(m);
        }

        files.slice(0, slots.length).forEach(function (file, i) {
            putFileInSlot(slots[i], file);
        });

        if (files.length > slots.length) {
            showMsg((files.length - slots.length) + ' photo(s) ignorée(s) : 7 photos maximum. Retirez une photo avec × pour en ajouter une autre.', true);
        } else {
            hideMsg();
        }
        this.value = '';
    });

    document.querySelectorAll('[data-remove]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var n = btn.getAttribute('data-remove');
            slotFiles[n] = null;
            document.getElementById('fv-remove-' + n).value = '1';
            setPreview(n, null);
        });
    });

    function parseMoney(v) {
        var raw = String(v || '').replace(',', '.').replace(/\s/g, '');
        return raw !== '' && !isNaN(raw) ? Number(raw) : 0;
    }

    function updateCoutTotal() {
        var total = parseMoney(document.getElementById('fv-achat').value)
            + parseMoney(document.getElementById('fv-depenses').value);
        document.getElementById('fv-cout-total').textContent =
            total.toLocaleString('fr-FR', { maximumFractionDigits: 0 }) + ' MAD';
    }

    formFv.querySelectorAll('[data-money]').forEach(function (el) {
        el.addEventListener('blur', function () {
            var raw = (this.value || '').replace(',', '.').replace(/\s/g, '');
            if (raw !== '' && !isNaN(raw)) this.value = Number(raw).toFixed(2);
            updateCoutTotal();
        });
        el.addEventListener('input', updateCoutTotal);
    });

    formFv.addEventListener('submit', function (e) {
        e.preventDefault();
        hideMsg();

        var missing = missingFields();
        if (missing.length) {
            showMsg('Champs obligatoires : ' + missing.join(', ') + '.', true);
            formBody.scrollTop = 0;
            return;
        }

        btnValider.disabled = true;
        btnValider.textContent = '…';

        var fd = new FormData(formFv);
        photoKeys.forEach(function (k, i) {
            if (slotFiles[i + 1]) fd.append(k, slotFiles[i + 1]);
        });

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
            if (res.status === 413) throw new Error('Photos trop lourdes. Réduisez leur taille puis réessayez.');
            return res.json().catch(function () {
                throw new Error('Erreur serveur (' + res.status + ').');
            }).then(function (data) {
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
            setTimeout(function () { window.location.reload(); }, 700);
        })
        .catch(function (err) {
            showMsg(err.message || 'Erreur.', true);
            formBody.scrollTop = 0;
            btnValider.disabled = false;
            btnValider.textContent = 'Enregistrer';
        });
    });

    panelForm.querySelectorAll('[data-close]').forEach(function (btn) {
        btn.addEventListener('click', function () { close(panelForm); });
    });
    panelForm.addEventListener('click', function (e) {
        if (e.target === panelForm) close(panelForm);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panelForm.hidden) close(panelForm);
    });
})();
</script>
