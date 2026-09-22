@extends('layouts.app')

@section('title', 'LOUANGE')
@section('page-title')
    <span class="navbar__title-brand">Louange</span><span class="navbar__title-comma">,</span>
    <span class="navbar__title-tag">Pilotez en professionnel</span>
@endsection

@section('content')
    <div class="cards">
        <article class="card card--achats">
            <div class="card__top">
                <span class="card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h2l1.4 9.2A2 2 0 0 0 8.4 17h8.3a2 2 0 0 0 2-1.6L20 8H7"/>
                        <circle cx="9.5" cy="20" r="1.2"/>
                        <circle cx="16.5" cy="20" r="1.2"/>
                        <path d="M14 4l2 2 3.5-3.5"/>
                    </svg>
                </span>
                <span class="card__badge">Achats</span>
            </div>
            <div class="card__label">Total Achats</div>
            <div class="card__value">{{ montant_fr(array_sum($chart['achats'])) }} <small>MAD</small></div>
            <div class="card__foot">Cumul des achats véhicules</div>
        </article>

        <article class="card card--ventes">
            <div class="card__top">
                <span class="card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 17V7a1 1 0 0 1 1-1h4l2 3h8a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/>
                        <circle cx="9" cy="14" r="1.2"/>
                        <circle cx="16" cy="14" r="1.2"/>
                        <path d="M12 3v3M10.5 4.5h3"/>
                    </svg>
                </span>
                <span class="card__badge">Ventes</span>
            </div>
            <div class="card__label">Total Ventes</div>
            <div class="card__value">{{ montant_fr(array_sum($chart['ventes'])) }} <small>MAD</small></div>
            <div class="card__foot">Chiffre d’affaires ventes</div>
        </article>

        <article class="card card--depenses">
            <div class="card__top">
                <span class="card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3v18"/>
                        <path d="M17 8H9.5a2.5 2.5 0 0 0 0 5H14a2.5 2.5 0 0 1 0 5H6"/>
                    </svg>
                </span>
                <span class="card__badge">Dépenses</span>
            </div>
            <div class="card__label">Total Dépenses</div>
            <div class="card__value">{{ montant_fr(array_sum($chart['depenses'])) }} <small>MAD</small></div>
            <div class="card__foot">Dépenses opérationnelles</div>
        </article>

        <article class="card card--charges">
            <div class="card__top">
                <span class="card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="3" width="14" height="18" rx="1.5"/>
                        <path d="M9 8h6M9 12h6M9 16h3"/>
                        <path d="M16.5 14.5l1.5 1.5 3-3"/>
                    </svg>
                </span>
                <span class="card__badge">Charges</span>
            </div>
            <div class="card__label">Total Charges</div>
            <div class="card__value">{{ montant_fr(array_sum($chart['charges'])) }} <small>MAD</small></div>
            <div class="card__foot">Frais et charges enregistrés</div>
        </article>

        <article class="card card--solde">
            <div class="card__top">
                <span class="card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="8" r="3"/>
                        <path d="M2.5 19a6.5 6.5 0 0 1 13 0"/>
                        <path d="M17 11h5M19.5 8.5v5"/>
                        <rect x="15.5" y="14.5" width="6" height="4.5" rx="1"/>
                    </svg>
                </span>
                <span class="card__badge">Bénéfice</span>
            </div>
            <div class="card__label">Bénéfice</div>
            <div class="card__value">{{ montant_fr(array_sum($chart['benefices'])) }} <small>MAD</small></div>
            <div class="card__foot">Ventes − Achats − Dépenses − Charges</div>
        </article>
    </div>

    <section class="dash-chart" aria-labelledby="dash-chart-title">
        <div class="dash-chart__head">
            <div class="dash-chart__titles">
                <p class="dash-chart__eyebrow">Pilotage</p>
                <h2 class="dash-chart__title" id="dash-chart-title">{{ $chart['title'] }}</h2>
            </div>
            <form class="dash-chart__filters" method="GET" action="{{ route('dashboard') }}" autocomplete="off" id="dash-chart-filters">
                <div class="dash-chart__period" role="group" aria-label="Période">
                    <button type="submit" name="periode" value="mois" class="dash-chart__pill {{ $chartMode === 'mois' ? 'is-active' : '' }}">Mois</button>
                    <button type="submit" name="periode" value="annee" class="dash-chart__pill {{ $chartMode === 'annee' ? 'is-active' : '' }}">Année</button>
                </div>
                @if ($chartMode === 'mois')
                    <label class="dash-chart__year">
                        <span>Année</span>
                        <select name="annee" onchange="this.form.requestSubmit(this.form.querySelector('[name=periode][value=mois]'))">
                            @foreach ($availableYears as $y)
                                <option value="{{ $y }}" @selected($y === $chartYear)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </label>
                @else
                    <input type="hidden" name="annee" value="{{ $chartYear }}">
                @endif
            </form>
        </div>

        <div class="dash-chart__legend" aria-hidden="true">
            <span class="dash-chart__leg dash-chart__leg--achat">Achat</span>
            <span class="dash-chart__leg dash-chart__leg--vente">Vente</span>
            <span class="dash-chart__leg dash-chart__leg--depense">Dépenses</span>
            <span class="dash-chart__leg dash-chart__leg--charge">Charge</span>
            <span class="dash-chart__leg dash-chart__leg--benefice">Bénéfice</span>
        </div>

        <div class="dash-chart__canvas-wrap">
            <canvas id="dash-performance-chart" height="110"></canvas>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .dash-chart {
        margin-top: 1.35rem;
        padding: 1.25rem 1.3rem 1.15rem;
        border: 1px solid var(--line);
        background: var(--panel);
        position: relative;
        overflow: hidden;
    }
    .dash-chart::before {
        content: "";
        position: absolute;
        inset: 0 0 auto;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--gold), transparent);
        opacity: 0.85;
    }
    .dash-chart__head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .dash-chart__eyebrow {
        margin: 0 0 0.2rem;
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--gold-soft);
    }
    .dash-chart__title {
        margin: 0;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: clamp(1.35rem, 2.2vw, 1.75rem);
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--text-strong);
    }
    .dash-chart__filters {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.65rem;
    }
    .dash-chart__period {
        display: inline-flex;
        border: 1px solid var(--line);
        padding: 0.15rem;
        gap: 0.15rem;
        background: color-mix(in srgb, var(--panel) 80%, #000 20%);
    }
    .dash-chart__pill {
        border: 0;
        background: transparent;
        color: var(--muted);
        font: inherit;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 0.45rem 0.85rem;
        cursor: pointer;
    }
    .dash-chart__pill.is-active {
        background: color-mix(in srgb, var(--gold) 22%, transparent);
        color: var(--gold-soft);
    }
    .dash-chart__year {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.72rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .dash-chart__year select {
        background: transparent;
        border: 1px solid var(--line);
        color: var(--text-strong);
        padding: 0.4rem 0.55rem;
        font: inherit;
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-weight: 600;
    }
    html[data-theme="light"] .dash-chart__year select { background: #fff; }

    .dash-chart__legend {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem 1.1rem;
        margin-bottom: 0.85rem;
    }
    .dash-chart__leg {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .dash-chart__leg::before {
        content: "";
        width: 0.65rem;
        height: 0.65rem;
        border-radius: 1px;
    }
    .dash-chart__leg--achat::before { background: #c9a227; }
    .dash-chart__leg--vente::before { background: #6dbf8a; }
    .dash-chart__leg--depense::before { background: #d4845a; }
    .dash-chart__leg--charge::before { background: #9aa0a8; }
    .dash-chart__leg--benefice::before { background: #6aa3d4; }

    .dash-chart__canvas-wrap {
        position: relative;
        width: 100%;
        min-height: 280px;
    }

    @media (max-width: 768px) {
        .dash-chart {
            padding: 1rem 0.95rem 0.95rem;
            margin-top: 1rem;
        }
        .dash-chart__title {
            font-size: 1.25rem;
            letter-spacing: 0.08em;
        }
        .dash-chart__filters {
            width: 100%;
        }
        .dash-chart__legend {
            gap: 0.55rem 0.85rem;
            font-size: 0.72rem;
        }
        .dash-chart__canvas-wrap {
            min-height: 220px;
        }
    }

    @media (max-width: 480px) {
        .dash-chart__canvas-wrap {
            min-height: 200px;
        }
        .dash-chart__pill {
            padding: 0.4rem 0.65rem;
            font-size: 0.68rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
(function () {
    var payload = @json($chart);
    var canvas = document.getElementById('dash-performance-chart');
    if (!canvas || typeof Chart === 'undefined') return;

    var root = document.documentElement;
    function cssVar(name, fallback) {
        var v = getComputedStyle(root).getPropertyValue(name).trim();
        return v || fallback;
    }

    var muted = cssVar('--muted', 'rgba(242,240,236,0.55)');
    var line = cssVar('--line', 'rgba(224,197,106,0.22)');
    var text = cssVar('--text-strong', '#fff');

    var money = function (value) {
        return new Intl.NumberFormat('fr-MA', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value) + ' MAD';
    };

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: payload.labels,
            datasets: [
                {
                    label: 'Achat',
                    data: payload.achats,
                    backgroundColor: 'rgba(201, 162, 39, 0.75)',
                    borderColor: '#c9a227',
                    borderWidth: 1,
                    borderRadius: 2,
                    maxBarThickness: 18
                },
                {
                    label: 'Vente',
                    data: payload.ventes,
                    backgroundColor: 'rgba(109, 191, 138, 0.75)',
                    borderColor: '#6dbf8a',
                    borderWidth: 1,
                    borderRadius: 2,
                    maxBarThickness: 18
                },
                {
                    label: 'Dépenses',
                    data: payload.depenses,
                    backgroundColor: 'rgba(212, 132, 90, 0.75)',
                    borderColor: '#d4845a',
                    borderWidth: 1,
                    borderRadius: 2,
                    maxBarThickness: 18
                },
                {
                    label: 'Charge',
                    data: payload.charges,
                    backgroundColor: 'rgba(154, 160, 168, 0.75)',
                    borderColor: '#9aa0a8',
                    borderWidth: 1,
                    borderRadius: 2,
                    maxBarThickness: 18
                },
                {
                    type: 'line',
                    label: 'Bénéfice',
                    data: payload.benefices,
                    borderColor: '#6aa3d4',
                    backgroundColor: 'rgba(106, 163, 212, 0.15)',
                    borderWidth: 2.5,
                    pointRadius: 3.5,
                    pointBackgroundColor: '#6aa3d4',
                    tension: 0.35,
                    yAxisID: 'y',
                    order: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(16, 16, 18, 0.94)',
                    titleFont: { family: 'Space Grotesk', size: 12, weight: '600' },
                    bodyFont: { family: 'Space Grotesk', size: 12 },
                    borderColor: line,
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function (ctx) {
                            return ' ' + ctx.dataset.label + ' : ' + money(ctx.parsed.y || 0);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'transparent' },
                    ticks: {
                        color: muted,
                        font: { family: 'Space Grotesk', size: 11, weight: '600' }
                    }
                },
                y: {
                    grid: { color: line, drawBorder: false },
                    ticks: {
                        color: muted,
                        font: { family: 'Space Grotesk', size: 11, weight: '500' },
                        callback: function (value) {
                            return new Intl.NumberFormat('fr-MA', {
                                notation: 'compact',
                                maximumFractionDigits: 1
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
})();
</script>
@endpush
