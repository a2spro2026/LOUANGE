<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Espace') — AUTO LOUNGE</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600|outfit:300,400,500,600|space-grotesk:500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root,
        html[data-theme="dark"] {
            --ink: #101012;
            --panel: #17171a;
            --panel-2: #1e1e22;
            --mist: #f2f0ec;
            --muted: rgba(242, 240, 236, 0.62);
            --gold: #c9a227;
            --gold-soft: #e0c56a;
            --line: rgba(224, 197, 106, 0.22);
            --sidebar-bg: linear-gradient(180deg, #1c1b18 0%, #121214 100%);
            --navbar-bg: rgba(23, 23, 26, 0.92);
            --card-bg: linear-gradient(155deg, rgba(40, 38, 32, 0.95) 0%, rgba(20, 20, 22, 0.98) 100%);
            --text-strong: #fff;
            --sidebar-w: 16.5rem;
        }

        html[data-theme="light"] {
            --ink: #f4f2ec;
            --panel: #ffffff;
            --panel-2: #f7f5f0;
            --mist: #1c1b18;
            --muted: rgba(28, 27, 24, 0.58);
            --gold: #a8861a;
            --gold-soft: #8f7316;
            --line: rgba(168, 134, 26, 0.28);
            --sidebar-bg: linear-gradient(180deg, #fffdf8 0%, #f3efe6 100%);
            --navbar-bg: rgba(255, 253, 248, 0.94);
            --card-bg: linear-gradient(155deg, #ffffff 0%, #f7f3ea 100%);
            --text-strong: #121212;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100%;
            max-width: 100%;
            overflow-x: hidden;
            font-family: "Outfit", sans-serif;
            background: var(--ink);
            color: var(--mist);
        }

        body { min-height: 100vh; }

        /* Pas de barre de défilement horizontale (chrome Windows) */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(201, 162, 39, 0.35) transparent;
        }
        *::-webkit-scrollbar {
            width: 8px;
            height: 0;
        }
        *::-webkit-scrollbar:vertical {
            width: 8px;
        }
        *::-webkit-scrollbar:horizontal {
            height: 0 !important;
            display: none;
        }
        *::-webkit-scrollbar-button {
            width: 0;
            height: 0;
            display: none;
        }
        *::-webkit-scrollbar-track {
            background: transparent;
        }
        *::-webkit-scrollbar-thumb {
            background: rgba(201, 162, 39, 0.35);
        }
        .table-wrap,
        .main,
        .content {
            overflow-x: hidden;
        }

        .amount,
        .num,
        .card__value,
        .fv-card__prix-value,
        .fv-photo-view__prix strong,
        .reglement-item__montant,
        .reglements__chip strong {
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-variant-numeric: tabular-nums lining-nums;
            font-feature-settings: "tnum" 1, "lnum" 1;
            letter-spacing: 0.02em;
            font-weight: 600;
        }

        .app {
            display: flex;
            min-height: 100vh;
            max-width: 100%;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            padding: 1.35rem 1rem 1.1rem;
            transition: width 0.28s ease, min-width 0.28s ease, padding 0.28s ease, opacity 0.28s ease, border-color 0.28s ease;
            overflow: hidden;
        }

        .app.is-sidebar-collapsed .sidebar {
            width: 0 !important;
            min-width: 0 !important;
            max-width: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            border: 0 !important;
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
            visibility: hidden;
        }

        .sidebar__brand {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 0.75rem;
            padding: 0.35rem 0.55rem 1.15rem;
            border-bottom: 1px solid var(--line);
            margin-bottom: 1rem;
            text-decoration: none;
            color: inherit;
            white-space: nowrap;
        }

        .sidebar__brand-logo {
            width: 2.85rem;
            height: 2.85rem;
            flex-shrink: 0;
            display: block;
            object-fit: contain;
        }

        .sidebar__brand-text {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            min-width: 0;
        }

        .sidebar__brand-name {
            font-family: "Cormorant Garamond", serif;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gold-soft);
            line-height: 1.1;
        }

        .sidebar__brand-tag {
            font-size: 0.58rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .sidebar__nav {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            flex: 1;
            overflow-y: auto;
            padding-right: 0.15rem;
            min-height: 0;
        }

        .sidebar__link,
        .sidebar__toggle {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            width: 100%;
            padding: 0.72rem 0.8rem;
            color: var(--muted);
            text-decoration: none;
            font: inherit;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: 1px solid transparent;
            background: transparent;
            cursor: pointer;
            text-align: left;
            white-space: nowrap;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .sidebar__link:hover,
        .sidebar__toggle:hover,
        .nav-group.is-open > .sidebar__toggle {
            color: var(--text-strong);
            background: rgba(201, 162, 39, 0.12);
            border-color: rgba(201, 162, 39, 0.28);
        }

        .sidebar__link--dashboard {
            margin-bottom: 0.35rem;
            color: #1a1508;
            background: linear-gradient(135deg, #e8c85a 0%, #c9a227 55%, #a8861a 100%);
            border-color: rgba(255, 236, 180, 0.35);
            box-shadow:
                0 0 18px rgba(201, 162, 39, 0.28),
                inset 0 1px 0 rgba(255, 246, 210, 0.45);
            font-weight: 600;
            letter-spacing: 0.06em;
        }

        .sidebar__link--dashboard .sidebar__ico {
            color: #1a1508;
            border-color: rgba(26, 21, 8, 0.25);
            background: rgba(255, 255, 255, 0.28);
        }

        .sidebar__link--dashboard:hover,
        .sidebar__link--dashboard.is-active {
            color: #120e05;
            background: linear-gradient(135deg, #f0d878 0%, #d4af37 55%, #b8921f 100%);
            border-color: rgba(255, 246, 210, 0.55);
            box-shadow: 0 0 24px rgba(224, 197, 106, 0.4);
        }

        .sidebar__ico {
            width: 1.55rem;
            height: 1.55rem;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(224, 197, 106, 0.28);
            background: rgba(201, 162, 39, 0.1);
            color: var(--gold-soft);
        }

        .sidebar__ico svg {
            width: 0.95rem;
            height: 0.95rem;
        }

        .sidebar__label { flex: 1; min-width: 0; }

        .sidebar__chevron {
            width: 0.85rem;
            height: 0.85rem;
            color: var(--gold);
            opacity: 0.75;
            transition: transform 0.22s ease;
            flex-shrink: 0;
        }

        .nav-group.is-open > .sidebar__toggle .sidebar__chevron {
            transform: rotate(180deg);
        }

        .nav-group {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .nav-group__menu {
            display: none;
            flex-direction: column;
            gap: 0.12rem;
            padding: 0.15rem 0 0.35rem 0.55rem;
            margin-left: 0.85rem;
            border-left: 1px solid rgba(224, 197, 106, 0.22);
        }

        .nav-group.is-open > .nav-group__menu { display: flex; }

        .sidebar__sublink {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.5rem 0.65rem;
            color: var(--muted);
            text-decoration: none;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: 0.98rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border: 1px solid transparent;
            border-left: 2px solid transparent;
            white-space: nowrap;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .sidebar__sublink:hover,
        .sidebar__sublink.is-active {
            color: var(--text-strong);
            background: rgba(201, 162, 39, 0.1);
            border-color: transparent;
            border-left-color: var(--gold);
        }

        .sidebar__sublink.is-active {
            color: var(--gold-soft);
        }

        .sidebar__subico {
            width: 1.2rem;
            height: 1.2rem;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            opacity: 0.9;
        }

        .sidebar__subico svg {
            width: 0.85rem;
            height: 0.85rem;
        }

        html[data-theme="light"] .sidebar__link,
        html[data-theme="light"] .sidebar__toggle {
            color: #2a271f;
            font-weight: 700;
            text-transform: uppercase;
        }

        html[data-theme="light"] .sidebar__sublink {
            color: #3d3a32;
            font-weight: 600;
        }
        html[data-theme="light"] .sidebar__sublink.is-active {
            color: var(--gold-soft);
        }
        .nav-group--achats .sidebar__ico { color: #e0c56a; border-color: rgba(224, 197, 106, 0.35); background: rgba(224, 197, 106, 0.12); }
        .nav-group--ventes .sidebar__ico { color: #6dbf8a; border-color: rgba(109, 191, 138, 0.35); background: rgba(109, 191, 138, 0.12); }
        .nav-group--charges .sidebar__ico { color: #d4845a; border-color: rgba(212, 132, 90, 0.35); background: rgba(212, 132, 90, 0.12); }
        .nav-group--params .sidebar__ico { color: #9aa0a8; border-color: rgba(154, 160, 168, 0.35); background: rgba(154, 160, 168, 0.12); }

        .sidebar__bottom {
            margin-top: auto;
            padding-top: 0.85rem;
            border-top: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            white-space: nowrap;
        }

        .sidebar__logout {
            appearance: none;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            border: 1px solid rgba(224, 197, 106, 0.4);
            background: rgba(201, 162, 39, 0.08);
            color: var(--mist);
            font: inherit;
            font-size: 0.72rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            padding: 0.7rem 0.9rem;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .sidebar__logout:hover {
            background: rgba(201, 162, 39, 0.18);
            border-color: var(--gold-soft);
            color: var(--text-strong);
        }

        .sidebar__logout svg {
            width: 0.95rem;
            height: 0.95rem;
        }

        .sidebar__footer {
            font-size: 0.68rem;
            color: var(--muted);
            letter-spacing: 0.08em;
            text-align: center;
        }

        .main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.95rem 1.5rem;
            background: var(--navbar-bg);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .navbar__left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-width: 0;
        }

        .icon-btn {
            appearance: none;
            width: 2.4rem;
            height: 2.4rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            background: rgba(201, 162, 39, 0.08);
            color: var(--gold-soft);
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        .icon-btn:hover {
            background: rgba(201, 162, 39, 0.18);
            border-color: var(--gold);
            box-shadow: 0 0 14px rgba(201, 162, 39, 0.22);
        }

        .icon-btn svg {
            width: 1.15rem;
            height: 1.15rem;
        }

        .icon-btn .icon-hide { display: none; }
        .app.is-sidebar-collapsed #sidebar-toggle .icon-show { display: none; }
        .app.is-sidebar-collapsed #sidebar-toggle .icon-hide { display: block; }

        .theme-btn .icon-sun { display: none; }
        html[data-theme="light"] .theme-btn .icon-moon { display: none; }
        html[data-theme="light"] .theme-btn .icon-sun { display: block; }

        .navbar__title {
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(1.2rem, 2vw, 1.55rem);
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            line-height: 1.2;
            color: var(--text-strong);
            position: relative;
            padding-bottom: 0.2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 0.25rem 0.45rem;
        }
        .navbar__title::after {
            content: "";
            display: block;
            width: 2.4rem;
            height: 2px;
            margin-top: 0.28rem;
            background: linear-gradient(90deg, var(--gold), transparent);
            flex-basis: 100%;
        }
        .navbar__title-brand {
            color: var(--gold-soft);
            letter-spacing: 0.16em;
        }
        .navbar__title-comma {
            color: var(--gold-soft);
            margin-right: -0.15rem;
        }
        .navbar__title-tag {
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-size: clamp(0.68rem, 1.1vw, 0.78rem);
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .navbar__right {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .navbar__user {
            text-align: right;
            line-height: 1.25;
        }

        .navbar__user-name {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-strong);
        }

        .navbar__user-role {
            font-size: 0.7rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--gold);
        }

        .content {
            padding: 1.5rem clamp(1rem, 3vw, 1.75rem) 2rem;
            flex: 1;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1rem;
        }

        .card {
            position: relative;
            overflow: hidden;
            background: var(--card-bg);
            border: 1px solid var(--line);
            padding: 1.2rem 1.25rem 1.15rem;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--card-accent, var(--gold)), transparent);
            opacity: 0.9;
        }

        .card::after {
            content: "";
            position: absolute;
            inset: auto -20% -40% auto;
            width: 8rem;
            height: 8rem;
            border-radius: 50%;
            background: radial-gradient(circle, color-mix(in srgb, var(--card-accent, var(--gold)) 28%, transparent), transparent 70%);
            pointer-events: none;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: color-mix(in srgb, var(--card-accent, var(--gold)) 55%, transparent);
            box-shadow:
                0 16px 36px rgba(0, 0, 0, 0.18),
                0 0 24px color-mix(in srgb, var(--card-accent, var(--gold)) 18%, transparent);
        }

        .card--achats { --card-accent: #e0c56a; }
        .card--ventes { --card-accent: #6dbf8a; }
        .card--depenses { --card-accent: #c47a9a; }
        .card--charges { --card-accent: #d4845a; }
        .card--solde { --card-accent: #6aa3d4; }

        .card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .card__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.4rem;
            height: 2.4rem;
            border: 1px solid color-mix(in srgb, var(--card-accent) 45%, transparent);
            background: color-mix(in srgb, var(--card-accent) 14%, transparent);
            color: var(--card-accent);
        }

        .card__icon svg {
            width: 1.15rem;
            height: 1.15rem;
        }

        .card__badge {
            font-size: 0.62rem;
            font-weight: 500;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--card-accent);
            padding: 0.28rem 0.55rem;
            border: 1px solid color-mix(in srgb, var(--card-accent) 35%, transparent);
            background: color-mix(in srgb, var(--card-accent) 10%, transparent);
        }

        .card__label {
            position: relative;
            z-index: 1;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--mist);
            margin-bottom: 0.55rem;
        }

        .card__value {
            position: relative;
            z-index: 1;
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-size: clamp(1.55rem, 2.1vw, 1.95rem);
            font-weight: 700;
            font-variant-numeric: tabular-nums lining-nums;
            letter-spacing: 0.03em;
            color: var(--text-strong);
            line-height: 1.1;
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 0.18em;
            text-decoration-color: color-mix(in srgb, var(--card-accent) 75%, transparent);
            text-shadow: 0 0 18px color-mix(in srgb, var(--card-accent) 35%, transparent);
        }

        .card__value small {
            font-family: "Outfit", sans-serif;
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            color: var(--card-accent);
            margin-left: 0.15rem;
        }

        .card__foot {
            position: relative;
            z-index: 1;
            margin-top: 0.75rem;
            padding-top: 0.7rem;
            border-top: 1px solid rgba(128, 128, 128, 0.15);
            font-size: 0.75rem;
            color: var(--muted);
            line-height: 1.35;
        }

        @media (max-width: 1200px) {
            .cards { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 1100px) {
            .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 860px) {
            .app { flex-direction: column; }
            .sidebar {
                width: 100%;
                min-width: 0;
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }
            .app.is-sidebar-collapsed .sidebar {
                width: 100% !important;
                max-width: none !important;
                max-height: 0 !important;
                min-height: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                opacity: 0;
                visibility: hidden;
            }
            .nav-group__menu { margin-left: 0.5rem; }
        }

        @media (max-width: 560px) {
            .cards { grid-template-columns: 1fr; }
        }
    </style>
    <script>
        // Panneau masqué par défaut (avant rendu)
        (function () {
            try {
                if (localStorage.getItem('al_sidebar_v2') !== 'shown') {
                    document.documentElement.classList.add('sidebar-pref-hidden');
                }
            } catch (e) {}
        })();
    </script>
</head>
<body>
    <div class="app is-sidebar-collapsed" id="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="sidebar__brand" href="{{ route('dashboard') }}">
                <img class="sidebar__brand-logo" src="{{ asset('images/logo-ste-louange-auto.svg') }}" alt="STE LOUANGE" width="46" height="46">
                <span class="sidebar__brand-text">
                    <span class="sidebar__brand-name">STE Louange</span>
                </span>
            </a>

            <nav class="sidebar__nav" id="sidebar-nav">
                <a class="sidebar__link sidebar__link--dashboard {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">
                    <span class="sidebar__ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                    </span>
                    <span class="sidebar__label">Tableau de bord</span>
                </a>

                <div class="nav-group nav-group--achats is-open">
                    <button type="button" class="sidebar__toggle" aria-expanded="true">
                        <span class="sidebar__ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h2l1.4 9.2A2 2 0 0 0 8.4 17h8.3a2 2 0 0 0 2-1.6L20 8H7"/><circle cx="9.5" cy="20" r="1.2"/><circle cx="16.5" cy="20" r="1.2"/><path d="M14 4l2 2 3.5-3.5"/></svg>
                        </span>
                        <span class="sidebar__label">Achats</span>
                        <svg class="sidebar__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="nav-group__menu">
                        <a class="sidebar__sublink {{ request()->routeIs('achats.fiche-vehicule*') ? 'is-active' : '' }}" href="{{ route('achats.fiche-vehicule') }}">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16V8a1 1 0 0 1 1-1h4l2 3h8a1 1 0 0 1 1 1v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><circle cx="8.5" cy="14.5" r="1.2"/><circle cx="16" cy="14.5" r="1.2"/><path d="M12 3v3"/></svg>
                            </span>
                            Fiche Véhicule
                        </a>
                        <a class="sidebar__sublink {{ request()->routeIs('achats.bon-achat*') ? 'is-active' : '' }}" href="{{ route('achats.bon-achat') }}">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3h8l1 3H7l1-3z"/><path d="M6 6h12v13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6z"/><path d="M9 11h6M9 15h4"/></svg>
                            </span>
                            Bon D'achat
                        </a>
                        <a class="sidebar__sublink {{ request()->routeIs('achats.etat-depenses*') ? 'is-active' : '' }}" href="#">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h10"/><path d="M17 15l2 2 3-4"/></svg>
                            </span>
                            Etat Dépenses
                        </a>
                        <a class="sidebar__sublink {{ request()->routeIs('achats.balance*') ? 'is-active' : '' }}" href="#">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5h12v14H4z"/><path d="M8 9h4M8 13h4"/><path d="M16 8h4v8h-4"/></svg>
                            </span>
                            Balance Achats
                        </a>
                    </div>
                </div>

                <div class="nav-group nav-group--ventes is-open">
                    <button type="button" class="sidebar__toggle" aria-expanded="true">
                        <span class="sidebar__ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17V7a1 1 0 0 1 1-1h4l2 3h8a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><circle cx="9" cy="14" r="1.2"/><circle cx="16" cy="14" r="1.2"/><path d="M12 3v3M10.5 4.5h3"/></svg>
                        </span>
                        <span class="sidebar__label">Vente</span>
                        <svg class="sidebar__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="nav-group__menu">
                        <a class="sidebar__sublink" href="#">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h10v18H7z"/><path d="M10 7h4M10 11h4M10 15h2"/><path d="M16 8l2 2-2 2"/></svg>
                            </span>
                            Bon de Vente
                        </a>
                        <a class="sidebar__sublink" href="#">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M2 10h20"/><circle cx="16.5" cy="14" r="1.2"/></svg>
                            </span>
                            Paiement
                        </a>
                        <a class="sidebar__sublink" href="#">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5h12v14H4z"/><path d="M8 9h4M8 13h4"/><path d="M16 8h4v8h-4"/><path d="M3 17l5-5 3 3 4-4"/></svg>
                            </span>
                            Balance Ventes
                        </a>
                    </div>
                </div>

                <div class="nav-group nav-group--charges is-open">
                    <button type="button" class="sidebar__toggle" aria-expanded="true">
                        <span class="sidebar__ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 8h6M9 12h6M9 16h3"/><path d="M16.5 14.5l1.5 1.5 3-3"/></svg>
                        </span>
                        <span class="sidebar__label">Charges</span>
                        <svg class="sidebar__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="nav-group__menu">
                        <a class="sidebar__sublink" href="#">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3h8l1 3H7l1-3z"/><path d="M6 6h12v13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6z"/><path d="M12 10v6M9 13h6"/></svg>
                            </span>
                            Bon Charge
                        </a>
                        <a class="sidebar__sublink" href="#">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5h12v14H4z"/><path d="M8 9h4M8 13h4"/><path d="M16 8h4v8h-4"/><path d="M18 12h2"/></svg>
                            </span>
                            Balance Charges
                        </a>
                    </div>
                </div>

                <div class="nav-group nav-group--params {{ request()->routeIs('parametres.*') ? 'is-open' : '' }}">
                    <button type="button" class="sidebar__toggle" aria-expanded="{{ request()->routeIs('parametres.*') ? 'true' : 'false' }}">
                        <span class="sidebar__ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
                        </span>
                        <span class="sidebar__label">Paramètres</span>
                        <svg class="sidebar__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="nav-group__menu">
                        <a class="sidebar__sublink {{ request()->routeIs('parametres.utilisateurs*') ? 'is-active' : '' }}" href="{{ route('parametres.utilisateurs') }}">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.5"/><path d="M5 19a7 7 0 0 1 14 0"/></svg>
                            </span>
                            Utilisateur
                        </a>
                        <a class="sidebar__sublink" href="#">
                            <span class="sidebar__subico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/><circle cx="12" cy="16" r="1.2"/></svg>
                            </span>
                            Autorisations
                        </a>
                    </div>
                </div>
            </nav>

            <div class="sidebar__bottom">
                <form method="POST" action="{{ route('deconnexion') }}">
                    @csrf
                    <button type="submit" class="sidebar__logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5"/><path d="M15 12H8"/><path d="M15 12l3-3M15 12l3 3"/></svg>
                        Déconnexion
                    </button>
                </form>
                <div class="sidebar__footer">STE Louange</div>
            </div>
        </aside>

        <div class="main">
            <header class="navbar">
                <div class="navbar__left">
                    <button type="button" class="icon-btn" id="sidebar-toggle" title="Afficher / masquer le menu" aria-label="Afficher ou masquer le panneau latéral" aria-controls="sidebar" aria-expanded="false">
                        <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h10M4 18h16"/><path d="M18 9l3 3-3 3"/></svg>
                        <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/><path d="M9 9l-3 3 3 3"/></svg>
                    </button>
                    <h1 class="navbar__title">@yield('page-title', 'Tableau de bord')</h1>
                </div>
                <div class="navbar__right">
                    <button type="button" class="icon-btn theme-btn" id="theme-toggle" title="Mode sombre / clair" aria-label="Basculer le thème sombre ou clair">
                        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 14.5A8.5 8.5 0 1 1 9.5 3 7 7 0 0 0 21 14.5z"/></svg>
                        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                    </button>
                    <div class="navbar__user">
                        <div class="navbar__user-name">{{ auth()->user()->name }}</div>
                        <div class="navbar__user-role">{{ auth()->user()->statutLabel() }}</div>
                    </div>
                </div>
            </header>

            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('styles')
    @stack('scripts')

    <script>
        (function () {
            const app = document.getElementById('app-shell');
            const sidebarBtn = document.getElementById('sidebar-toggle');
            const themeBtn = document.getElementById('theme-toggle');
            const root = document.documentElement;

            document.querySelectorAll('.sidebar__toggle').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const group = btn.closest('.nav-group');
                    const open = group.classList.toggle('is-open');
                    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
            });

            sidebarBtn.addEventListener('click', function () {
                const collapsed = app.classList.toggle('is-sidebar-collapsed');
                sidebarBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                localStorage.setItem('al_sidebar_v2', collapsed ? 'hidden' : 'shown');
            });

            // Masqué par défaut ; ouvert seulement si demandé (nouvelle clé = reset ancien réglage)
            localStorage.removeItem('al_sidebar');
            if (localStorage.getItem('al_sidebar_v2') === 'shown') {
                app.classList.remove('is-sidebar-collapsed');
                document.documentElement.classList.remove('sidebar-pref-hidden');
                sidebarBtn.setAttribute('aria-expanded', 'true');
            } else {
                app.classList.add('is-sidebar-collapsed');
                document.documentElement.classList.add('sidebar-pref-hidden');
                sidebarBtn.setAttribute('aria-expanded', 'false');
                localStorage.setItem('al_sidebar_v2', 'hidden');
            }

            function applyTheme(theme) {
                root.setAttribute('data-theme', theme);
                localStorage.setItem('al_theme', theme);
            }

            const savedTheme = localStorage.getItem('al_theme');
            if (savedTheme === 'light' || savedTheme === 'dark') {
                applyTheme(savedTheme);
            }

            themeBtn.addEventListener('click', function () {
                const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
                applyTheme(next);
            });
        })();
    </script>
</body>
</html>
