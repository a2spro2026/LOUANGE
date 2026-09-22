@extends('layouts.app')

@section('title', "Bon D'achat")
@section('page-title', "Bon D'achat")

@section('content')
<div class="module">
    @if (session('success'))
        <div class="flash flash--ok">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash flash--err">{{ $errors->first() }}</div>
    @endif

    <div class="module__toolbar">
        <form class="filters" method="GET" action="{{ route('achats.bon-achat') }}" autocomplete="off">
            <div class="filters__grid">
                <label class="field">
                    <span>Mois</span>
                    <select name="mois">
                        <option value="">— Tous —</option>
                        @foreach ([1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'] as $num => $label)
                            <option value="{{ $num }}" @selected(($filters['mois'] ?? '') == $num)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="field">
                    <span>Véhicule</span>
                    <input type="text" name="vehicule" value="{{ $filters['vehicule'] ?? '' }}" placeholder="Type ou matricule" autocomplete="off">
                </label>
                <label class="field">
                    <span>Montant</span>
                    <input type="text" name="montant" value="{{ $filters['montant'] ?? '' }}" placeholder="0.00" autocomplete="off">
                </label>
                <div class="filters__actions">
                    <button type="submit" class="btn btn--gold">Rechercher</button>
                    <a href="{{ route('achats.bon-achat') }}" class="btn btn--ghost">Reset</a>
                </div>
            </div>
        </form>

        <div class="module__footer">
            <button type="button" class="btn btn--gold" id="btn-ajouter">Ajouter</button>
            <button type="button" class="btn btn--ghost" id="btn-imprimer-liste">Imprimer</button>
            <a href="{{ route('dashboard') }}" class="btn btn--ghost">Fermer</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Date</th>
                    <th>Nom Vendeur</th>
                    <th>Ville</th>
                    <th>Type Véhicule</th>
                    <th>Matricule</th>
                    <th>Montant</th>
                    <th>Montant Payé</th>
                    <th>Solde</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bons as $bon)
                    @php
                        $photoUrl = $bon->piece_jointe ? asset('storage/'.$bon->piece_jointe) : '';
                        $modesLabels = [
                            'especes' => 'Espèces',
                            'virement' => 'Virement',
                            'cheque' => 'Chèque',
                            'traite' => 'Traite',
                            'autre' => 'Autre',
                        ];
                        $paiementsPayload = $bon->paiements->map(function ($p) use ($modesLabels) {
                            return [
                                'date' => date_fr($p->date_paiement),
                                'montant' => montant_fr($p->montant),
                                'mode' => $modesLabels[$p->mode_paiement] ?? $p->mode_paiement,
                                'mode_key' => $p->mode_paiement,
                                'reference' => $p->reference ?: '',
                                'nom_tire' => $p->nom_tire ?: '',
                                'date_decaissement' => $p->date_decaissement ? date_fr($p->date_decaissement) : '',
                            ];
                        })->values();
                    @endphp
                    <tr
                        data-id="{{ $bon->id }}"
                        data-date="{{ date_fr($bon->date_bon) }}"
                        data-vendeur="{{ $bon->nom_vendeur }}"
                        data-ville="{{ $bon->ville }}"
                        data-type="{{ $bon->type_vehicule }}"
                        data-matricule="{{ $bon->matricule }}"
                        data-montant="{{ montant_fr($bon->montant) }}"
                        data-paye="{{ montant_fr($bon->montant_paye) }}"
                        data-solde="{{ montant_fr($bon->solde) }}"
                        data-photo="{{ $photoUrl }}"
                        data-piece-nom="{{ $bon->piece_jointe_nom }}"
                        data-paiements='@json($paiementsPayload)'
                    >
                        <td>
                            @if ($photoUrl)
                                <img class="table-photo" src="{{ $photoUrl }}" alt="{{ $bon->piece_jointe_nom }}" loading="lazy">
                            @else
                                <span class="table-photo-empty">—</span>
                            @endif
                        </td>
                        <td>{{ date_fr($bon->date_bon) }}</td>
                        <td>{{ $bon->nom_vendeur }}</td>
                        <td>{{ $bon->ville ?: '—' }}</td>
                        <td>{{ $bon->type_vehicule }}</td>
                        <td>{{ $bon->matricule }}</td>
                        <td class="num">{{ montant_fr($bon->montant) }}</td>
                        <td class="num">{{ montant_fr($bon->montant_paye) }}</td>
                        <td class="num">{{ montant_fr($bon->solde) }}</td>
                        <td>
                            <div class="row-actions">
                                <button type="button" class="act" title="Voir" data-action="voir">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button type="button" class="act" title="Modifier" data-action="modifier">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                </button>
                                @if ((float) $bon->solde > 0)
                                <button type="button" class="act act--pay" title="Payer / Complément" data-action="payer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M2 10h20"/><circle cx="16.5" cy="14" r="1.2"/></svg>
                                </button>
                                @endif
                                <button type="button" class="act" title="Imprimer" data-action="imprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 9V3h12v6"/><path d="M6 17H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><path d="M6 13h12v8H6z"/></svg>
                                </button>
                                <button type="button" class="act" title="Télécharger" data-action="telecharger">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                                </button>
                                <button type="button" class="act" title="Importer" data-action="importer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 21V9"/><path d="M7 14l5-5 5 5"/><path d="M5 3h14"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty">Aucun bon d’achat trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="module__pager">{{ $bons->links() }}</div>
</div>

{{-- Panneau Ajouter / Modifier / Voir --}}
<div class="overlay" id="panel-bon" hidden>
    <div class="panel" role="dialog" aria-modal="true" aria-labelledby="panel-bon-title">
        <h2 class="panel__title" id="panel-bon-title">Ajouter un bon d’achat</h2>
        <div class="panel__msg" id="panel-bon-msg" hidden></div>
        <form method="POST" action="{{ route('achats.bon-achat.store') }}" id="form-bon" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="bon-method" value="POST">
            <div class="panel__grid">
                <label class="field">
                    <span>Date</span>
                    <input type="text" name="date_bon" id="bon-date" value="" placeholder="jj/mm/aaaa" maxlength="10" required autocomplete="off" inputmode="numeric" class="input-date">
                </label>
                <label class="field">
                    <span>Nom Vendeur</span>
                    <input type="text" name="nom_vendeur" id="bon-vendeur" value="" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Ville</span>
                    <input type="text" name="ville" id="bon-ville" value="" autocomplete="off">
                </label>
                <label class="field">
                    <span>Type Véhicule</span>
                    <input type="text" name="type_vehicule" id="bon-type" value="" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Matricule</span>
                    <input type="text" name="matricule" id="bon-matricule" value="" required autocomplete="off">
                </label>
                <label class="field">
                    <span>Montant</span>
                    <input type="text" name="montant" id="bon-montant" value="" placeholder="0.00" required autocomplete="off" inputmode="decimal">
                </label>
                <div class="field field--full">
                    <span>Photo</span>
                    <div class="photo-box">
                        <div class="photo-frame" id="bon-photo-frame">
                            <img id="bon-photo-preview" alt="Photo importée" hidden>
                            <span class="photo-empty" id="bon-photo-empty">Aucune photo importée</span>
                        </div>
                        <div class="import-row">
                            <input type="file" name="piece" id="bon-piece" accept="image/*" hidden>
                            <button type="button" class="btn btn--ghost" id="btn-importer">Importer</button>
                            <span class="import-name" id="bon-piece-name">Aucun fichier</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="reglements" id="bon-reglements" hidden>
                <div class="reglements__head">
                    <h3 class="reglements__title">Règlements</h3>
                    <div class="reglements__summary" id="bon-reglements-summary"></div>
                </div>
                <div class="reglements__list" id="bon-reglements-list"></div>
            </div>
            <div class="panel__actions">
                <button type="submit" class="btn btn--gold" id="btn-valider-bon">Valider</button>
                <button type="button" class="btn btn--gold" id="btn-payer-open">Payer</button>
                <button type="button" class="btn btn--ghost" id="btn-imprimer-bon" hidden>Imprimer</button>
                <button type="button" class="btn btn--ghost" data-close="panel-bon" id="btn-fermer-bon">Fermer</button>
            </div>
        </form>
    </div>
</div>

{{-- Panneau Payer --}}
<div class="overlay" id="panel-payer" hidden>
    <div class="panel panel--sm" role="dialog" aria-modal="true" aria-labelledby="panel-payer-title">
        <h2 class="panel__title" id="panel-payer-title">Paiement bon d’achat</h2>
        <p class="panel__hint" id="pay-solde-hint"></p>
        <form method="POST" action="#" id="form-payer" autocomplete="off">
            @csrf
            <div class="panel__grid">
                <label class="field">
                    <span>Date paiement</span>
                    <input type="text" name="date_paiement" id="pay-date" value="" placeholder="jj/mm/aaaa" maxlength="10" required autocomplete="off" inputmode="numeric" class="input-date" readonly>
                </label>
                <label class="field">
                    <span>Montant paiement</span>
                    <input type="text" name="montant_paiement" id="pay-montant" value="" placeholder="0.00" required autocomplete="off" inputmode="decimal">
                </label>
                <label class="field">
                    <span>Mode paiement</span>
                    <select name="mode_paiement" id="pay-mode" required autocomplete="off">
                        <option value="" disabled selected>— Sélectionner —</option>
                        <option value="especes">Espèces</option>
                        <option value="virement">Virement</option>
                        <option value="cheque">Chèque</option>
                        <option value="traite">Traite</option>
                        <option value="autre">Autre</option>
                    </select>
                </label>
                <label class="field" id="field-pay-ref">
                    <span>Référence</span>
                    <input type="text" name="reference" id="pay-ref" value="" autocomplete="off">
                </label>
                <label class="field" id="field-nom-tire">
                    <span>Nom tiré</span>
                    <input type="text" name="nom_tire" id="pay-nom-tire" value="" autocomplete="off">
                </label>
                <label class="field" id="field-date-decaissement">
                    <span>Date décaissement</span>
                    <input type="text" name="date_decaissement" id="pay-date-decaissement" value="" placeholder="jj/mm/aaaa" maxlength="10" autocomplete="off" inputmode="numeric" class="input-date" data-manual="1">
                </label>
            </div>
            <div class="panel__actions">
                <button type="submit" class="btn btn--gold">Valider</button>
                <button type="button" class="btn btn--ghost" data-close="panel-payer">Fermer</button>
            </div>
        </form>
    </div>
</div>

{{-- Aperçu impression --}}
<div class="overlay overlay--print" id="panel-print" hidden>
    <div class="print-shell" role="dialog" aria-modal="true" aria-labelledby="print-sheet-title">
        <div class="print-toolbar no-print">
            <button type="button" class="btn btn--ghost" id="btn-print-close">Fermer</button>
            <button type="button" class="btn btn--gold" id="btn-print-confirm">Imprimer</button>
        </div>
        <div class="print-sheet" id="print-sheet">
            <header class="print-brand">
                <img class="print-brand__logo" src="{{ asset('images/logo-ste-louange-auto.svg') }}" alt="Logo STE LOUANGE AUTO" width="72" height="72">
                <div class="print-brand__text">
                    <p class="print-brand__name">STE LOUANGE AUTO</p>
                    <p class="print-brand__tag">Excellence automobile</p>
                </div>
            </header>
            <h1 class="print-doc-title" id="print-sheet-title">Bon d’achat</h1>
            <p class="print-doc-sub" id="print-doc-date"></p>
            <div class="print-grid" id="print-fields"></div>
            <div class="print-photo" id="print-photo"></div>
            <div class="print-resume" id="print-resume"></div>
            <h2 class="print-section-title">Règlements</h2>
            <div id="print-reglements"></div>
        </div>
    </div>
</div>

<script>
(function () {
    function maskDate(el) {
        var digits = String(el.value || '').replace(/\D/g, '').substring(0, 8);
        var out = digits;
        if (digits.length >= 3 && digits.length <= 4) {
            out = digits.substring(0, 2) + '/' + digits.substring(2);
        } else if (digits.length >= 5) {
            out = digits.substring(0, 2) + '/' + digits.substring(2, 4) + '/' + digits.substring(4);
        }
        if (el.value !== out) {
            el.value = out;
        }
    }

    function bindDateMask(el) {
        if (!el || el.dataset.dateMasked === '1') return;
        el.dataset.dateMasked = '1';
        el.setAttribute('maxlength', '10');
        el.setAttribute('placeholder', 'jj/mm/aaaa');
        ['input', 'keyup', 'change', 'blur', 'paste'].forEach(function (evt) {
            el.addEventListener(evt, function () {
                setTimeout(function () { maskDate(el); }, 0);
            });
        });
    }

    document.querySelectorAll('#bon-date, #pay-date, .input-date').forEach(bindDateMask);

    document.addEventListener('focusin', function (e) {
        if (e.target && (e.target.id === 'bon-date' || e.target.id === 'pay-date' || e.target.classList.contains('input-date'))) {
            bindDateMask(e.target);
        }
    });
})();
</script>
@endsection

@push('styles')
<style>
    .module {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        min-height: calc(100vh - 7.5rem);
    }
    .module__toolbar {
        z-index: 8;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        flex-shrink: 0;
    }
    .flash { padding: 0.75rem 1rem; border: 1px solid var(--line); font-size: 0.88rem; }
    .flash--ok { background: rgba(109, 191, 138, 0.12); border-color: rgba(109, 191, 138, 0.35); }
    .flash--err { background: rgba(212, 90, 90, 0.12); border-color: rgba(212, 90, 90, 0.35); color: #e8a0a0; }
    html[data-theme="light"] .flash--err { color: #8a2f2f; }

    .filters {
        background: var(--panel);
        border: 1px solid var(--line);
        padding: 1rem;
    }
    .filters__grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.85rem;
        align-items: end;
    }
    .filters__actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

    .module__footer {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        justify-content: flex-end;
    }

    .table-wrap {
        flex: 1;
        min-height: 16rem;
        max-height: calc(100vh - 18rem);
        overflow-x: hidden;
        overflow-y: auto;
        border: 1px solid var(--line);
        background: var(--panel);
    }
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }
    .data-table th,
    .data-table td {
        padding: 0.75rem 0.7rem;
        border-bottom: 1px solid rgba(128, 128, 128, 0.15);
        text-align: center;
        font-size: 0.86rem;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
        background: var(--panel);
    }
    .data-table th {
        position: sticky;
        top: 0;
        z-index: 3;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--gold-soft);
        background: color-mix(in srgb, var(--panel) 88%, #c9a227 12%);
        box-shadow: 0 1px 0 var(--line);
    }
    .data-table .num {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-variant-numeric: tabular-nums lining-nums;
        font-feature-settings: "tnum" 1, "lnum" 1;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .data-table .empty { text-align: center; color: var(--muted); padding: 2rem; }

    .field { display: flex; flex-direction: column; gap: 0.35rem; }
    .field span {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .field input,
    .field select,
    .field textarea {
        min-height: 2.55rem;
        padding: 0.55rem 0.75rem;
        border: 1px solid var(--line);
        background: rgba(0, 0, 0, 0.18);
        color: var(--mist);
        font: inherit;
        outline: none;
    }
    html[data-theme="light"] .field input,
    html[data-theme="light"] .field select,
    html[data-theme="light"] .field textarea {
        background: #fff;
        color: #1c1b18;
    }
    .field input:focus,
    .field select:focus,
    .field textarea:focus { border-color: var(--gold); }
    .field.is-disabled {
        opacity: 0.55;
        pointer-events: none;
    }
    .field.is-disabled input,
    .field.is-disabled select,
    .field.is-disabled textarea {
        background: color-mix(in srgb, var(--muted) 18%, transparent);
        color: var(--muted);
        cursor: not-allowed;
    }
    .field--full { grid-column: 1 / -1; }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.55rem;
        padding: 0.55rem 1.1rem;
        border: 1px solid transparent;
        font: inherit;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        text-decoration: none;
        cursor: pointer;
    }
    .btn--gold { background: var(--gold); color: #121212; border-color: var(--gold); }
    .btn--gold:hover { background: var(--gold-soft); }
    .btn--ghost { background: transparent; color: var(--mist); border-color: var(--line); }
    .btn--ghost:hover { border-color: var(--gold); }

    .row-actions { display: flex; gap: 0.3rem; justify-content: center; }
    .act {
        width: 1.9rem;
        height: 1.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--line);
        background: rgba(201, 162, 39, 0.08);
        color: var(--gold-soft);
        cursor: pointer;
    }
    .act--pay {
        border-color: rgba(109, 191, 138, 0.45);
        background: rgba(109, 191, 138, 0.14);
        color: #6dbf8a;
    }
    .act--pay:hover {
        background: rgba(109, 191, 138, 0.28);
        border-color: #6dbf8a;
    }
    .table-photo {
        width: 3.2rem;
        height: 3.2rem;
        object-fit: cover;
        border: 1px solid var(--line);
        display: inline-block;
        vertical-align: middle;
        background: rgba(0,0,0,0.15);
    }
    .table-photo-empty { color: var(--muted); }
    .photo-box {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .photo-frame {
        width: 100%;
        min-height: 11rem;
        border: 1px solid var(--line);
        background:
            linear-gradient(145deg, rgba(201, 162, 39, 0.06), rgba(0, 0, 0, 0.12));
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .photo-frame img {
        max-width: 100%;
        max-height: 14rem;
        object-fit: contain;
        display: block;
    }
    .photo-empty {
        font-size: 0.82rem;
        color: var(--muted);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-weight: 600;
    }
    .act {
        width: 1.9rem;
        height: 1.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--line);
        background: rgba(201, 162, 39, 0.08);
        color: var(--gold-soft);
        cursor: pointer;
    }
    .act:hover { background: rgba(201, 162, 39, 0.2); border-color: var(--gold); }
    .act svg { width: 0.95rem; height: 0.95rem; }

    .module__pager { flex-shrink: 0; }

    .overlay {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(6, 6, 8, 0.65);
        backdrop-filter: blur(6px);
    }
    .overlay[hidden] { display: none !important; }
    .panel {
        width: min(100%, 44rem);
        background: var(--panel);
        border: 1px solid var(--line);
        padding: 1.35rem 1.3rem 1.2rem;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
        max-height: 92vh;
        overflow: auto;
    }
    .panel--sm { width: min(100%, 28rem); }
    .panel__title {
        font-family: "Cormorant Garamond", serif;
        font-size: 1.45rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--gold-soft);
        margin-bottom: 1rem;
    }
    .panel__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
    }
    .panel__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        justify-content: flex-end;
        margin-top: 1.2rem;
    }
    .reglements {
        margin-top: 1.35rem;
        padding-top: 1.15rem;
        border-top: 1px solid var(--line);
    }
    .reglements__head {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
    }
    .reglements__title {
        margin: 0;
        font-family: "Cormorant Garamond", serif;
        font-size: 1.15rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--gold-soft);
    }
    .reglements__summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
    }
    .reglements__chip {
        display: inline-flex;
        flex-direction: column;
        gap: 0.1rem;
        min-width: 5.5rem;
        padding: 0.4rem 0.65rem;
        border: 1px solid var(--line);
        background: color-mix(in srgb, var(--gold) 8%, transparent);
    }
    .reglements__chip span {
        font-size: 0.65rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .reglements__chip strong {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums lining-nums;
        letter-spacing: 0.02em;
    }
    .reglements__chip--solde strong { color: var(--gold-soft); }
    .reglements__chip--ok strong { color: #6dbf8a; }
    .reglements__list {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }
    .reglement-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 0.65rem 0.9rem;
        align-items: start;
        padding: 0.75rem 0.85rem;
        border: 1px solid var(--line);
        background: color-mix(in srgb, var(--panel) 92%, var(--gold) 8%);
    }
    .reglement-item__index {
        width: 1.7rem;
        height: 1.7rem;
        display: grid;
        place-items: center;
        border: 1px solid color-mix(in srgb, var(--gold) 45%, var(--line));
        color: var(--gold-soft);
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .reglement-item__body {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        min-width: 0;
    }
    .reglement-item__top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.45rem 0.7rem;
    }
    .reglement-item__date {
        font-size: 0.88rem;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }
    .reglement-item__mode {
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0.15rem 0.45rem;
        border: 1px solid var(--line);
        color: var(--gold-soft);
    }
    .reglement-item__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 0.9rem;
        font-size: 0.8rem;
        color: var(--muted);
    }
    .reglement-item__meta b {
        color: var(--text);
        font-weight: 500;
    }
    .reglement-item__montant {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums lining-nums;
        letter-spacing: 0.02em;
        color: var(--gold-soft);
        white-space: nowrap;
    }
    .reglements__empty {
        padding: 0.9rem;
        border: 1px dashed var(--line);
        text-align: center;
        color: var(--muted);
        font-size: 0.88rem;
    }
    @media (max-width: 560px) {
        .reglement-item {
            grid-template-columns: auto 1fr;
        }
        .reglement-item__montant {
            grid-column: 2;
            justify-self: start;
        }
    }

    .overlay--print {
        align-items: flex-start;
        padding: 1.25rem;
        overflow: auto;
        z-index: 90;
    }
    .print-shell {
        width: min(100%, 52rem);
        margin: 0 auto 2rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }
    .print-toolbar {
        display: flex;
        gap: 0.55rem;
        justify-content: flex-end;
        position: sticky;
        top: 0;
        z-index: 2;
        padding: 0.55rem 0;
        background: rgba(6, 6, 8, 0.72);
        backdrop-filter: blur(6px);
    }
    .print-sheet {
        background: #fff;
        color: #111;
        padding: 1.6rem 1.7rem 2rem;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
        font-family: Georgia, "Times New Roman", serif;
    }
    .print-brand {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-bottom: 0.9rem;
        margin-bottom: 0.35rem;
        border-bottom: 2px solid #c9a227;
    }
    .print-brand__logo {
        width: 72px;
        height: 72px;
        display: block;
        flex-shrink: 0;
    }
    .print-brand__name {
        margin: 0;
        font-size: 1.55rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        font-weight: 700;
        line-height: 1.15;
        color: #111;
    }
    .print-brand__tag {
        margin: 0.15rem 0 0;
        font-size: 0.72rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #8a7020;
    }
    .print-doc-title {
        margin: 1rem 0 0.25rem;
        font-size: 1.1rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #222;
    }
    .print-doc-sub {
        margin: 0 0 1.1rem;
        color: #666;
        font-size: 0.86rem;
    }
    .print-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.7rem 1.6rem;
        margin-bottom: 1rem;
    }
    .print-field span {
        display: block;
        font-size: 0.68rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #666;
    }
    .print-field strong {
        font-size: 0.98rem;
        font-weight: 700;
        color: #111;
    }
    .print-photo {
        margin: 0.4rem 0 1rem;
    }
    .print-photo__label {
        display: block;
        font-size: 0.68rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #666;
        margin-bottom: 0.4rem;
    }
    .print-photo img {
        max-width: 180px;
        max-height: 140px;
        object-fit: cover;
        border: 1px solid #ccc;
        display: block;
    }
    .print-photo__empty { color: #888; font-size: 0.9rem; }
    .print-resume {
        display: flex;
        flex-wrap: wrap;
        gap: 1.2rem;
        margin: 0.8rem 0;
        padding: 0.7rem 0;
        border-top: 1px solid #222;
        border-bottom: 1px solid #222;
    }
    .print-resume span {
        display: block;
        font-size: 0.68rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #666;
    }
    .print-resume strong {
        font-size: 1.05rem;
        color: #111;
    }
    .print-section-title {
        margin: 1.2rem 0 0.55rem;
        font-size: 1rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .print-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
    }
    .print-table th,
    .print-table td {
        padding: 0.4rem 0.3rem;
        border-bottom: 1px solid #ddd;
        text-align: left;
        vertical-align: top;
    }
    .print-table th {
        border-bottom-color: #222;
        font-size: 0.72rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #444;
    }
    .print-table .num { text-align: right; font-weight: 700; }
    .print-empty {
        color: #666;
        font-style: italic;
        margin: 0.4rem 0 0;
    }

    @media print {
        body * { visibility: hidden !important; }
        #panel-print,
        #panel-print * { visibility: visible !important; }
        #panel-print {
            position: static !important;
            inset: auto !important;
            display: block !important;
            background: #fff !important;
            padding: 0 !important;
            overflow: visible !important;
        }
        .print-shell {
            width: 100% !important;
            margin: 0 !important;
            box-shadow: none !important;
        }
        .no-print { display: none !important; }
        .print-sheet {
            box-shadow: none !important;
            padding: 0 !important;
        }
        .print-brand {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
    .panel__msg {
        margin-bottom: 0.85rem;
        padding: 0.65rem 0.8rem;
        border: 1px solid rgba(109, 191, 138, 0.4);
        background: rgba(109, 191, 138, 0.12);
        font-size: 0.86rem;
    }
    .panel__msg.is-error {
        border-color: rgba(212, 90, 90, 0.4);
        background: rgba(212, 90, 90, 0.12);
    }
    .import-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .import-name {
        font-size: 0.82rem;
        color: var(--muted);
    }

    @media (max-width: 900px) {
        .filters__grid { grid-template-columns: 1fr 1fr; }
        .panel__grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 560px) {
        .filters__grid { grid-template-columns: 1fr; }
        .module__toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .module__footer {
            justify-content: stretch;
        }
        .module__footer .btn {
            flex: 1;
        }
        .table-wrap {
            max-height: calc(100vh - 14rem);
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const panelBon = document.getElementById('panel-bon');
    const panelPayer = document.getElementById('panel-payer');
    const formBon = document.getElementById('form-bon');
    const formPayer = document.getElementById('form-payer');
    const methodInput = document.getElementById('bon-method');
    const titleBon = document.getElementById('panel-bon-title');
    const btnPayerOpen = document.getElementById('btn-payer-open');
    const btnValider = document.getElementById('btn-valider-bon');
    const btnImprimerBon = document.getElementById('btn-imprimer-bon');
    const btnImporter = document.getElementById('btn-importer');
    let currentViewRow = null;
    const fileInput = document.getElementById('bon-piece');
    const fileName = document.getElementById('bon-piece-name');
    const photoPreview = document.getElementById('bon-photo-preview');
    const photoEmpty = document.getElementById('bon-photo-empty');
    const panelMsg = document.getElementById('panel-bon-msg');
    const csrf = formBon.querySelector('input[name="_token"]').value;
    let currentId = null;
    let previewObjectUrl = null;

    function showPhoto(url) {
        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
        }
        if (url) {
            photoPreview.src = url;
            photoPreview.hidden = false;
            photoEmpty.hidden = true;
        } else {
            photoPreview.removeAttribute('src');
            photoPreview.hidden = true;
            photoEmpty.hidden = false;
        }
    }

    function showMsg(text, isError) {
        panelMsg.hidden = false;
        panelMsg.textContent = text;
        panelMsg.classList.toggle('is-error', !!isError);
    }

    function hideMsg() {
        panelMsg.hidden = true;
        panelMsg.textContent = '';
        panelMsg.classList.remove('is-error');
    }

    function openPanel(el) {
        el.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closePanel(el) {
        el.hidden = true;
        if (panelBon.hidden && panelPayer.hidden) {
            document.body.style.overflow = '';
            if (el === panelBon && currentId) {
                window.location.reload();
            }
        }
    }

    function formatMontantInput(el) {
        const raw = (el.value || '').replace(',', '.').replace(/\s/g, '');
        if (raw === '' || isNaN(raw)) return;
        el.value = Number(raw).toFixed(2);
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function hideReglements() {
        document.getElementById('bon-reglements').hidden = true;
        document.getElementById('bon-reglements-list').innerHTML = '';
        document.getElementById('bon-reglements-summary').innerHTML = '';
    }

    function renderReglements(row) {
        var box = document.getElementById('bon-reglements');
        var list = document.getElementById('bon-reglements-list');
        var summary = document.getElementById('bon-reglements-summary');
        var paiements = [];
        try {
            paiements = JSON.parse(row.dataset.paiements || '[]');
        } catch (e) {
            paiements = [];
        }

        var montant = row.dataset.montant || '0.00';
        var paye = row.dataset.paye || '0.00';
        var solde = row.dataset.solde || '0.00';
        var soldeNum = parseFloat(String(solde).replace(',', '.')) || 0;

        summary.innerHTML =
            '<div class="reglements__chip"><span>Montant</span><strong>' + escapeHtml(montant) + '</strong></div>' +
            '<div class="reglements__chip"><span>Payé</span><strong>' + escapeHtml(paye) + '</strong></div>' +
            '<div class="reglements__chip ' + (soldeNum <= 0 ? 'reglements__chip--ok' : 'reglements__chip--solde') + '">' +
                '<span>Solde</span><strong>' + escapeHtml(solde) + '</strong></div>';

        if (!paiements.length) {
            list.innerHTML = '<div class="reglements__empty">Aucun règlement enregistré pour ce bon.</div>';
        } else {
            list.innerHTML = paiements.map(function (p, i) {
                var meta = [];
                if (p.reference) meta.push('Réf. <b>' + escapeHtml(p.reference) + '</b>');
                if (p.nom_tire) meta.push('Nom tiré <b>' + escapeHtml(p.nom_tire) + '</b>');
                if (p.date_decaissement) meta.push('Décaissement <b>' + escapeHtml(p.date_decaissement) + '</b>');
                return (
                    '<article class="reglement-item">' +
                        '<div class="reglement-item__index">' + (i + 1) + '</div>' +
                        '<div class="reglement-item__body">' +
                            '<div class="reglement-item__top">' +
                                '<span class="reglement-item__date">' + escapeHtml(p.date) + '</span>' +
                                '<span class="reglement-item__mode">' + escapeHtml(p.mode) + '</span>' +
                            '</div>' +
                            (meta.length ? '<div class="reglement-item__meta">' + meta.join(' · ') + '</div>' : '') +
                        '</div>' +
                        '<div class="reglement-item__montant">' + escapeHtml(p.montant) + '</div>' +
                    '</article>'
                );
            }).join('');
        }

        box.hidden = false;
    }

    function setBonFields(data, readOnly) {
        var dateEl = document.getElementById('bon-date');
        dateEl.value = data.date || '';
        if (dateEl.value) {
            var d = String(dateEl.value).replace(/\D/g, '').substring(0, 8);
            if (d.length === 8) {
                dateEl.value = d.substring(0, 2) + '/' + d.substring(2, 4) + '/' + d.substring(4);
            }
        }
        document.getElementById('bon-vendeur').value = data.vendeur || '';
        document.getElementById('bon-ville').value = data.ville || '';
        document.getElementById('bon-type').value = data.type || '';
        document.getElementById('bon-matricule').value = data.matricule || '';
        document.getElementById('bon-montant').value = data.montant || '';
        fileName.textContent = data.piece_nom || 'Aucun fichier';
        showPhoto(data.photo || '');
        if (!data.piece_nom && !data.photo) fileInput.value = '';

        formBon.querySelectorAll('input, textarea, select').forEach(function (el) {
            if (el.id === 'bon-method' || el.name === '_token') return;
            if (el.type === 'file') {
                el.disabled = !!readOnly;
                return;
            }
            el.readOnly = !!readOnly;
            el.disabled = !!readOnly;
        });
        btnValider.hidden = !!readOnly;
        btnImporter.hidden = !!readOnly;
        btnPayerOpen.hidden = !!readOnly;
        btnImprimerBon.hidden = !readOnly;
        if (!readOnly) currentViewRow = null;
    }

    function applySavedBon(bon) {
        currentId = bon.id;
        currentViewRow = null;
        hideReglements();
        titleBon.textContent = 'Bon d’achat validé';
        formBon.action = @json(url('/achats/bon-achat')) + '/' + currentId;
        methodInput.value = 'PUT';
        setBonFields({
            date: bon.date,
            vendeur: bon.vendeur,
            ville: bon.ville,
            type: bon.type,
            matricule: bon.matricule,
            montant: bon.montant,
            photo: bon.piece_url || '',
            piece_nom: bon.piece_nom || ''
        }, false);
        btnPayerOpen.hidden = false;
        btnImprimerBon.hidden = true;
    }

    function openCreate() {
        currentId = null;
        currentViewRow = null;
        hideMsg();
        hideReglements();
        titleBon.textContent = "Ajouter un bon d’achat";
        formBon.action = @json(route('achats.bon-achat.store'));
        methodInput.value = 'POST';
        setBonFields({}, false);
        btnPayerOpen.hidden = false;
        btnImprimerBon.hidden = true;
        openPanel(panelBon);
    }

    function openEdit(row, readOnly) {
        hideMsg();
        currentId = row.dataset.id;
        currentViewRow = readOnly ? row : null;
        titleBon.textContent = readOnly ? 'Voir le bon d’achat' : 'Modifier le bon d’achat';
        formBon.action = @json(url('/achats/bon-achat')) + '/' + currentId;
        methodInput.value = 'PUT';
        setBonFields({
            date: row.dataset.date,
            vendeur: row.dataset.vendeur,
            ville: row.dataset.ville === '—' ? '' : row.dataset.ville,
            type: row.dataset.type,
            matricule: row.dataset.matricule,
            montant: row.dataset.montant,
            photo: row.dataset.photo || '',
            piece_nom: row.dataset.pieceNom || ''
        }, readOnly);
        if (readOnly) {
            renderReglements(row);
            btnImprimerBon.hidden = false;
        } else {
            hideReglements();
            btnImprimerBon.hidden = true;
        }
        openPanel(panelBon);
    }

    function printBonView() {
        var row = currentViewRow;
        if (!row) {
            alert('Ouvrez d’abord le bon avec Voir.');
            return;
        }

        var paiements = [];
        try { paiements = JSON.parse(row.dataset.paiements || '[]'); } catch (e) { paiements = []; }

        document.getElementById('print-doc-date').textContent =
            'Document généré le ' + new Date().toLocaleDateString('fr-FR');

        document.getElementById('print-fields').innerHTML =
            '<div class="print-field"><span>Date</span><strong>' + escapeHtml(row.dataset.date) + '</strong></div>' +
            '<div class="print-field"><span>Nom vendeur</span><strong>' + escapeHtml(row.dataset.vendeur) + '</strong></div>' +
            '<div class="print-field"><span>Ville</span><strong>' + escapeHtml(row.dataset.ville || '—') + '</strong></div>' +
            '<div class="print-field"><span>Type véhicule</span><strong>' + escapeHtml(row.dataset.type) + '</strong></div>' +
            '<div class="print-field"><span>Matricule</span><strong>' + escapeHtml(row.dataset.matricule) + '</strong></div>' +
            '<div class="print-field"><span>Montant</span><strong>' + escapeHtml(row.dataset.montant) + '</strong></div>';

        var photo = row.dataset.photo || '';
        document.getElementById('print-photo').innerHTML = photo
            ? '<span class="print-photo__label">Photo</span><img src="' + escapeHtml(photo) + '" alt="Photo">'
            : '<span class="print-photo__label">Photo</span><span class="print-photo__empty">Aucune photo</span>';

        document.getElementById('print-resume').innerHTML =
            '<div><span>Montant</span><strong>' + escapeHtml(row.dataset.montant) + '</strong></div>' +
            '<div><span>Payé</span><strong>' + escapeHtml(row.dataset.paye) + '</strong></div>' +
            '<div><span>Solde</span><strong>' + escapeHtml(row.dataset.solde) + '</strong></div>';

        var regBox = document.getElementById('print-reglements');
        if (!paiements.length) {
            regBox.innerHTML = '<p class="print-empty">Aucun règlement enregistré.</p>';
        } else {
            regBox.innerHTML =
                '<table class="print-table"><thead><tr>' +
                '<th>#</th><th>Date</th><th>Mode</th><th>Référence</th><th>Nom tiré</th><th>Décaissement</th><th class="num">Montant</th>' +
                '</tr></thead><tbody>' +
                paiements.map(function (p, i) {
                    return '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + escapeHtml(p.date) + '</td>' +
                        '<td>' + escapeHtml(p.mode) + '</td>' +
                        '<td>' + escapeHtml(p.reference || '—') + '</td>' +
                        '<td>' + escapeHtml(p.nom_tire || '—') + '</td>' +
                        '<td>' + escapeHtml(p.date_decaissement || '—') + '</td>' +
                        '<td class="num">' + escapeHtml(p.montant) + '</td>' +
                        '</tr>';
                }).join('') +
                '</tbody></table>';
        }

        openPanel(document.getElementById('panel-print'));
    }

    function setPayFieldEnabled(fieldId, inputId, enabled, required) {
        var field = document.getElementById(fieldId);
        var input = document.getElementById(inputId);
        input.disabled = !enabled;
        if (enabled) {
            input.removeAttribute('readonly');
            input.readOnly = false;
        } else {
            input.readOnly = true;
            input.value = '';
        }
        input.required = !!required && enabled;
        field.classList.toggle('is-disabled', !enabled);
    }

    function togglePayExtras() {
        var mode = document.getElementById('pay-mode').value;
        var isEspeces = mode === 'especes';
        var isChequeTraite = mode === 'cheque' || mode === 'traite';
        var refEnabled = !!mode && !isEspeces;
        var nomTireEnabled = isChequeTraite;

        setPayFieldEnabled('field-pay-ref', 'pay-ref', refEnabled, false);
        setPayFieldEnabled('field-nom-tire', 'pay-nom-tire', nomTireEnabled, nomTireEnabled);

        // Date décaissement : toujours saisie manuelle (jamais auto-remplie)
        var dateDec = document.getElementById('pay-date-decaissement');
        var fieldDateDec = document.getElementById('field-date-decaissement');
        dateDec.disabled = false;
        dateDec.readOnly = false;
        dateDec.removeAttribute('readonly');
        dateDec.required = isChequeTraite;
        fieldDateDec.classList.remove('is-disabled');
    }

    function openPayer(row) {
        var dateAchat = '';
        if (row) {
            currentId = row.dataset.id;
            dateAchat = row.dataset.date || '';
            var solde = row.dataset.solde || '0.00';
            if (parseFloat(solde) <= 0) {
                alert('Ce bon est déjà soldé.');
                return;
            }
            document.getElementById('pay-solde-hint').textContent =
                'Solde restant : ' + solde + ' — paiement total ou partiel possible.';
            document.getElementById('pay-montant').value = solde;
        } else {
            if (!currentId) {
                showMsg('Validez d’abord le bon d’achat avant de payer.', true);
                return;
            }
            dateAchat = document.getElementById('bon-date').value || '';
            document.getElementById('pay-solde-hint').textContent = 'Paiement total ou partiel.';
            document.getElementById('pay-montant').value = '';
        }

        formPayer.action = @json(url('/achats/bon-achat')) + '/' + currentId + '/payer';
        var payDate = document.getElementById('pay-date');
        payDate.value = dateAchat;
        payDate.readOnly = true;
        document.getElementById('pay-mode').selectedIndex = 0;
        document.getElementById('pay-ref').value = '';
        document.getElementById('pay-nom-tire').value = '';
        document.getElementById('pay-date-decaissement').value = '';
        togglePayExtras();
        openPanel(panelPayer);
    }

    document.getElementById('btn-ajouter').addEventListener('click', openCreate);
    btnPayerOpen.addEventListener('click', function () { openPayer(null); });
    btnImprimerBon.addEventListener('click', printBonView);
    document.getElementById('btn-print-close').addEventListener('click', function () {
        closePanel(document.getElementById('panel-print'));
    });
    document.getElementById('btn-print-confirm').addEventListener('click', function () {
        window.print();
    });
    document.getElementById('pay-mode').addEventListener('change', togglePayExtras);
    btnImporter.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        const file = fileInput.files[0];
        fileName.textContent = file ? file.name : 'Aucun fichier';
        if (file && file.type.indexOf('image/') === 0) {
            if (previewObjectUrl) URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = URL.createObjectURL(file);
            showPhoto(previewObjectUrl);
        } else if (!file) {
            showPhoto('');
        }
    });

    document.getElementById('bon-montant').addEventListener('blur', function () {
        formatMontantInput(this);
    });
    document.getElementById('pay-montant').addEventListener('blur', function () {
        formatMontantInput(this);
    });

    function maskDateInput(el) {
        const digits = (el.value || '').replace(/\D+/g, '').slice(0, 8);
        let out = digits;
        if (digits.length > 2 && digits.length <= 4) {
            out = digits.slice(0, 2) + '/' + digits.slice(2);
        } else if (digits.length > 4) {
            out = digits.slice(0, 2) + '/' + digits.slice(2, 4) + '/' + digits.slice(4);
        }
        el.value = out;
    }

    document.querySelectorAll('.input-date').forEach(function (el) {
        el.addEventListener('input', function () { maskDateInput(el); });
        el.addEventListener('blur', function () { maskDateInput(el); });
    });

    formBon.addEventListener('submit', function (e) {
        e.preventDefault();
        formatMontantInput(document.getElementById('bon-montant'));
        hideMsg();

        const formData = new FormData(formBon);
        if (methodInput.value === 'PUT') {
            formData.set('_method', 'PUT');
        }

        btnValider.disabled = true;
        btnValider.textContent = '…';

        fetch(formBon.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: formData,
            credentials: 'same-origin'
        })
        .then(async function (res) {
            const data = await res.json().catch(function () { return {}; });
            if (!res.ok) {
                const msg = data.message || (data.errors && Object.values(data.errors)[0][0]) || 'Erreur de validation.';
                throw new Error(msg);
            }
            applySavedBon(data.bon);
            showMsg(data.message || 'Bon d’achat validé. Vous pouvez payer ou fermer.');
            fileInput.value = '';
            if (data.bon && data.bon.piece_url) {
                showPhoto(data.bon.piece_url);
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

    [panelBon, panelPayer, document.getElementById('panel-print')].forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closePanel(overlay);
        });
    });

    document.querySelectorAll('.data-table tbody tr[data-id]').forEach(function (row) {
        row.querySelectorAll('[data-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = btn.getAttribute('data-action');
                if (action === 'voir') openEdit(row, true);
                if (action === 'modifier') openEdit(row, false);
                if (action === 'payer') openPayer(row);
                if (action === 'imprimer') {
                    openEdit(row, true);
                    setTimeout(printBonView, 50);
                }
                if (action === 'telecharger') alert('Téléchargement à brancher.');
                if (action === 'importer') {
                    openEdit(row, false);
                    setTimeout(function () { fileInput.click(); }, 150);
                }
            });
        });
    });

    document.getElementById('btn-imprimer-liste').addEventListener('click', function () {
        window.print();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var panelPrint = document.getElementById('panel-print');
            if (panelPrint && !panelPrint.hidden) closePanel(panelPrint);
            else if (!panelPayer.hidden) closePanel(panelPayer);
            else if (!panelBon.hidden) closePanel(panelBon);
        }
    });

    @if (!empty($keepPanel) && !empty($savedBon))
    applySavedBon(@json($savedBon));
    showMsg('Bon d’achat validé. Vous pouvez payer ou fermer.');
    openPanel(panelBon);
    @endif
})();
</script>
@endpush
