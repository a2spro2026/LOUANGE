<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AUTO LOUNGE — L’excellence automobile</title>
    <meta name="description" content="Des voitures sélectionnées pour une conduite en toute confiance. Achat, vente, échange et financement.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600|outfit:300,400,500,600&display=swap" rel="stylesheet" />

    <style>
        :root {
            --ink: #121214;
            --mist: #f2f0ec;
            --gold: #c9a227;
            --gold-soft: #e0c56a;
            --panel: #1a1a1d;
            --line: rgba(224, 197, 106, 0.35);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100%;
            color: var(--mist);
            font-family: "Outfit", sans-serif;
            background: var(--ink);
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: hidden;
        }

        body.is-login-open {
            overflow: hidden;
        }

        .hero {
            position: relative;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            isolation: isolate;
        }

        .hero__media {
            position: absolute;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            background: #1a1a1c;
        }

        .hero__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: high-quality;
            transform: scale(1.015);
            animation: hero-drift 24s ease-in-out infinite alternate;
            will-change: transform;
        }

        .hero__veil {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background:
                linear-gradient(180deg, rgba(8, 8, 10, 0.42) 0%, transparent 22%, transparent 78%, rgba(8, 8, 10, 0.55) 100%);
        }

        .hero__top,
        .hero__bottom {
            position: relative;
            z-index: 2;
        }

        .hero__top {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 1.25rem clamp(1.25rem, 4vw, 3.5rem);
            animation: rise 0.9s ease both;
        }

        .nav-cta {
            position: relative;
            appearance: none;
            cursor: pointer;
            isolation: isolate;
            overflow: hidden;
            border: 1px solid rgba(224, 197, 106, 0.65);
            background:
                linear-gradient(135deg, rgba(201, 162, 39, 0.35) 0%, rgba(12, 12, 14, 0.72) 48%, rgba(224, 197, 106, 0.22) 100%);
            color: #fff8e7;
            font: inherit;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            text-decoration: none;
            padding: 0.78rem 1.35rem;
            backdrop-filter: blur(10px);
            box-shadow:
                0 0 0 1px rgba(224, 197, 106, 0.15),
                0 0 18px rgba(201, 162, 39, 0.35),
                0 0 36px rgba(201, 162, 39, 0.18),
                inset 0 1px 0 rgba(255, 236, 180, 0.35);
            text-shadow: 0 0 12px rgba(224, 197, 106, 0.55);
            animation: btn-glow 2.6s ease-in-out infinite;
            transition: border-color 0.25s ease, background 0.25s ease, color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
        }

        .nav-cta::before {
            content: "";
            position: absolute;
            inset: -40% -60%;
            background: linear-gradient(
                115deg,
                transparent 35%,
                rgba(255, 245, 210, 0.45) 48%,
                transparent 62%
            );
            transform: translateX(-45%) rotate(8deg);
            animation: btn-shine 3.4s ease-in-out infinite;
            pointer-events: none;
            z-index: -1;
        }

        .nav-cta:hover {
            border-color: #f0d878;
            color: #fff;
            transform: translateY(-1px);
            box-shadow:
                0 0 0 1px rgba(240, 216, 120, 0.35),
                0 0 22px rgba(224, 197, 106, 0.55),
                0 0 48px rgba(201, 162, 39, 0.35),
                inset 0 1px 0 rgba(255, 246, 210, 0.5);
            text-shadow: 0 0 16px rgba(240, 216, 120, 0.85);
        }

        .nav-cta:active {
            transform: translateY(0);
        }

        .hero__bottom {
            margin-top: auto;
            display: flex;
            justify-content: flex-end;
            padding: clamp(1.25rem, 4vw, 2.75rem) clamp(1.25rem, 4vw, 3.5rem);
            animation: rise 1.05s 0.12s ease both;
        }

        .hero__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.85rem;
            padding: 0.75rem 1.35rem;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
            backdrop-filter: blur(6px);
            transition: background 0.25s ease, color 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn--gold {
            background: var(--gold);
            color: var(--ink);
            border-color: var(--gold);
        }

        .btn--gold:hover {
            background: var(--gold-soft);
            border-color: var(--gold-soft);
        }

        .btn--ghost {
            background: rgba(12, 12, 14, 0.35);
            color: var(--mist);
            border-color: rgba(242, 240, 236, 0.4);
        }

        .btn--ghost:hover {
            border-color: rgba(242, 240, 236, 0.8);
            background: rgba(255, 255, 255, 0.08);
        }

        /* ——— Panneau connexion ——— */
        .login-overlay {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background:
                radial-gradient(ellipse 55% 45% at 50% 42%, rgba(201, 162, 39, 0.14), transparent 70%),
                rgba(4, 4, 6, 0.72);
            backdrop-filter: blur(10px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .login-overlay.is-open {
            opacity: 1;
            visibility: visible;
        }

        .login-panel {
            position: relative;
            width: min(100%, 26rem);
            padding: 2rem 1.75rem 1.65rem;
            border-radius: 2px;
            background:
                linear-gradient(165deg, rgba(36, 34, 28, 0.96) 0%, rgba(16, 16, 18, 0.98) 55%, rgba(12, 12, 14, 0.99) 100%);
            border: 1px solid rgba(224, 197, 106, 0.38);
            box-shadow:
                0 0 0 1px rgba(255, 236, 180, 0.06) inset,
                0 28px 70px rgba(0, 0, 0, 0.55),
                0 0 40px rgba(201, 162, 39, 0.12);
            transform: translateY(16px) scale(0.98);
            transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1);
            overflow: hidden;
        }

        .login-panel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold-soft), transparent);
            box-shadow: 0 0 18px rgba(224, 197, 106, 0.65);
        }

        .login-panel::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 80% 40% at 50% -10%, rgba(224, 197, 106, 0.16), transparent 60%);
        }

        .login-overlay.is-open .login-panel {
            transform: translateY(0) scale(1);
        }

        .login-panel__title {
            position: relative;
            z-index: 1;
            font-family: "Cormorant Garamond", serif;
            font-size: 1.7rem;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #f3e6b8;
            margin-bottom: 0.35rem;
            text-align: center;
            text-shadow: 0 0 24px rgba(201, 162, 39, 0.35);
        }

        .login-panel__subtitle {
            position: relative;
            z-index: 1;
            display: block;
            text-align: center;
            font-size: 0.68rem;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: rgba(224, 197, 106, 0.7);
            margin-bottom: 1.55rem;
        }

        .login-field {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            margin-bottom: 1.05rem;
        }

        .login-field label {
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(242, 240, 236, 0.78);
        }

        .login-field input,
        .login-field select {
            width: 100%;
            min-height: 2.9rem;
            padding: 0.7rem 0.95rem;
            border: 1px solid rgba(224, 197, 106, 0.22);
            background: rgba(6, 6, 8, 0.65);
            color: var(--mist);
            font: inherit;
            font-size: 0.95rem;
            outline: none;
            border-radius: 1px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .login-field select {
            cursor: pointer;
            appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, var(--gold) 50%),
                linear-gradient(135deg, var(--gold) 50%, transparent 50%);
            background-position: calc(100% - 18px) calc(50% - 3px), calc(100% - 12px) calc(50% - 3px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
            padding-right: 2.2rem;
        }

        .login-field select option {
            background: #1a1a1d;
            color: var(--mist);
        }

        .login-field input:focus,
        .login-field select:focus {
            border-color: rgba(224, 197, 106, 0.75);
            background: rgba(10, 10, 12, 0.85);
            box-shadow:
                0 0 0 1px rgba(201, 162, 39, 0.2),
                0 0 18px rgba(201, 162, 39, 0.18);
        }

        .login-actions {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 0.7rem;
            margin-top: 1.5rem;
        }

        .login-actions .btn {
            flex: 1;
            backdrop-filter: none;
            min-height: 2.95rem;
        }

        .login-actions .btn--gold {
            box-shadow: 0 0 18px rgba(201, 162, 39, 0.28);
        }

        .login-actions .btn--gold:hover {
            box-shadow: 0 0 28px rgba(224, 197, 106, 0.45);
        }

        .login-error {
            position: relative;
            z-index: 1;
            margin-bottom: 1rem;
            padding: 0.7rem 0.85rem;
            border: 1px solid rgba(220, 80, 80, 0.45);
            background: rgba(120, 20, 20, 0.35);
            color: #ffd4d4;
            font-size: 0.85rem;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes hero-drift {
            from { transform: scale(1.015) translate3d(0, 0, 0); }
            to { transform: scale(1.045) translate3d(-0.8%, -0.4%, 0); }
        }

        @keyframes btn-glow {
            0%, 100% {
                box-shadow:
                    0 0 0 1px rgba(224, 197, 106, 0.15),
                    0 0 14px rgba(201, 162, 39, 0.28),
                    0 0 28px rgba(201, 162, 39, 0.14),
                    inset 0 1px 0 rgba(255, 236, 180, 0.3);
            }
            50% {
                box-shadow:
                    0 0 0 1px rgba(240, 216, 120, 0.28),
                    0 0 22px rgba(224, 197, 106, 0.5),
                    0 0 44px rgba(201, 162, 39, 0.28),
                    inset 0 1px 0 rgba(255, 246, 210, 0.45);
            }
        }

        @keyframes btn-shine {
            0%, 35% { transform: translateX(-55%) rotate(8deg); opacity: 0; }
            45% { opacity: 1; }
            70%, 100% { transform: translateX(55%) rotate(8deg); opacity: 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            .hero__media img,
            .hero__top,
            .hero__bottom,
            .login-overlay,
            .login-panel,
            .nav-cta,
            .nav-cta::before {
                animation: none;
                transition: none;
            }
        }

        @media (max-width: 720px) {
            .hero__media img {
                object-position: 28% center;
            }
        }
    </style>
</head>
<body>
    <section class="hero" aria-label="Accueil AUTO LOUNGE">
        <div class="hero__media" aria-hidden="true">
            <img
                src="{{ asset('images/hero-showroom.jpg') }}?v=2"
                alt="Showroom AUTO LOUNGE — L’excellence automobile"
                width="1376"
                height="768"
                decoding="async"
                fetchpriority="high"
            >
        </div>
        <div class="hero__veil" aria-hidden="true"></div>

        <header class="hero__top">
            <button type="button" class="nav-cta" id="open-login" aria-haspopup="dialog" aria-controls="login-overlay">
                Se Connecter
            </button>
        </header>

        <div class="hero__bottom">
            <div class="hero__actions">
                <a class="btn btn--gold" href="#stock">Voir les véhicules</a>
                <a class="btn btn--ghost" href="#contact">Nous contacter</a>
            </div>
        </div>
    </section>

    <div
        class="login-overlay"
        id="login-overlay"
        role="dialog"
        aria-modal="true"
        aria-labelledby="login-title"
        hidden
    >
        <div class="login-panel">
            <h2 class="login-panel__title" id="login-title">Connexion</h2>
            <span class="login-panel__subtitle">Espace sécurisé</span>

            <form method="POST" action="{{ route('connexion') }}" autocomplete="off" id="login-form">
                @csrf

                @if ($errors->any())
                    <div class="login-error" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="login-field">
                    <label for="statut">Statut</label>
                    <select name="statut" id="statut" required autocomplete="off">
                        <option value="" disabled selected>— Sélectionner —</option>
                        <option value="gerant">Gérant</option>
                        <option value="commercial">Commercial</option>
                        <option value="assistant">Assistante</option>
                        <option value="atelier">Atelier</option>
                    </select>
                </div>

                <div class="login-field">
                    <label for="login">Login</label>
                    <input
                        type="text"
                        name="login"
                        id="login"
                        value=""
                        autocomplete="off"
                        required
                    >
                </div>

                <div class="login-field">
                    <label for="password">Mot de passe</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        value=""
                        autocomplete="off"
                        required
                    >
                </div>

                <div class="login-actions">
                    <button type="submit" class="btn btn--gold">Connecter</button>
                    <button type="button" class="btn btn--ghost" id="close-login">FERMER</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const overlay = document.getElementById('login-overlay');
            const openBtn = document.getElementById('open-login');
            const closeBtn = document.getElementById('close-login');
            const form = document.getElementById('login-form');

            function openLogin() {
                overlay.hidden = false;
                requestAnimationFrame(function () {
                    overlay.classList.add('is-open');
                });
                document.body.classList.add('is-login-open');
                document.getElementById('statut').focus();
            }

            function closeLogin() {
                overlay.classList.remove('is-open');
                document.body.classList.remove('is-login-open');
                window.setTimeout(function () {
                    if (!overlay.classList.contains('is-open')) {
                        overlay.hidden = true;
                        form.reset();
                        document.getElementById('statut').selectedIndex = 0;
                    }
                }, 250);
            }

            openBtn.addEventListener('click', openLogin);
            closeBtn.addEventListener('click', closeLogin);

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    closeLogin();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
                    closeLogin();
                }
            });

            @if ($errors->any())
            openLogin();
            @endif
        })();
    </script>
</body>
</html>
