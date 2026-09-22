<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catalogue — AUTO LOUNGE</title>
    <meta name="description" content="Catalogue des véhicules AUTO LOUNGE.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600|outfit:300,400,500,600|space-grotesk:500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --ink: #101012;
            --panel: #17171a;
            --mist: #f2f0ec;
            --muted: rgba(242, 240, 236, 0.62);
            --gold: #c9a227;
            --gold-soft: #e0c56a;
            --line: rgba(224, 197, 106, 0.22);
            --radius: 50px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            min-height: 100%;
            background:
                radial-gradient(ellipse 70% 40% at 50% -10%, rgba(201, 162, 39, 0.12), transparent 55%),
                var(--ink);
            color: var(--mist);
            font-family: "Outfit", sans-serif;
        }
        body { min-height: 100vh; min-height: 100dvh; overflow-x: hidden; }
        body.is-detail-open { overflow: hidden; }

        .cat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem clamp(1rem, 4vw, 2.5rem);
            padding-top: max(1.15rem, env(safe-area-inset-top));
            border-bottom: 1px solid var(--line);
            background: rgba(16, 16, 18, 0.88);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 20;
        }
        .cat-brand {
            text-decoration: none;
            color: inherit;
        }
        .cat-brand__name {
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(1.25rem, 3vw, 1.55rem);
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--gold-soft);
        }
        .cat-brand__tag {
            display: block;
            margin-top: 0.15rem;
            font-size: 0.65rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .cat-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.55rem;
            padding: 0.6rem 1.1rem;
            font: inherit;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            color: inherit;
            background: transparent;
            border-radius: var(--radius);
        }
        .btn--gold {
            background: var(--gold);
            color: #121212;
            border-color: var(--gold);
        }
        .btn--ghost {
            border-color: var(--line);
            color: var(--mist);
            background: rgba(255, 255, 255, 0.03);
        }
        .btn--ghost:hover { border-color: var(--gold); color: var(--gold-soft); }

        .cat-hero {
            padding: 1.75rem clamp(1rem, 4vw, 2.5rem) 1.25rem;
        }
        .cat-hero__eyebrow {
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold-soft);
            margin-bottom: 0.35rem;
        }
        .cat-hero__title {
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(1.8rem, 4vw, 2.6rem);
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .cat-hero__lead {
            margin-top: 0.55rem;
            max-width: 36rem;
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .cat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.15rem;
            padding: 0.5rem clamp(1rem, 4vw, 2.5rem) 2.5rem;
        }
        .cat-card {
            background: var(--panel);
            border: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            min-width: 0;
            border-radius: var(--radius);
            overflow: hidden;
            transition: border-color 0.2s ease, transform 0.2s ease;
        }
        .cat-card:hover {
            border-color: rgba(201, 162, 39, 0.55);
            transform: translateY(-2px);
        }
        .cat-card__photo {
            appearance: none;
            border: 0;
            padding: 0;
            cursor: pointer;
            display: block;
            width: 100%;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: #0c0c0e;
            position: relative;
        }
        .cat-card__photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.35s ease;
        }
        .cat-card:hover .cat-card__photo img { transform: scale(1.04); }
        .cat-card__photo-empty {
            display: grid;
            place-items: center;
            height: 100%;
            color: var(--muted);
            font-size: 0.8rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .cat-card__body {
            padding: 1rem 1.05rem 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            flex: 1;
        }
        .cat-card__title {
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--mist);
        }
        .cat-card__desc {
            color: var(--muted);
            font-size: 0.88rem;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .cat-card__price {
            margin-top: auto;
            padding-top: 0.65rem;
            border-top: 1px solid rgba(128, 128, 128, 0.18);
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 0.5rem;
        }
        .cat-card__price-label {
            font-size: 0.65rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .cat-card__price-value {
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--gold-soft);
            font-size: 1.05rem;
        }
        .cat-empty {
            grid-column: 1 / -1;
            padding: 3rem 1rem;
            text-align: center;
            border: 1px dashed var(--line);
            color: var(--muted);
            letter-spacing: 0.08em;
        }

        /* Detail overlay */
        .detail {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(4, 4, 6, 0.78);
            backdrop-filter: blur(10px);
        }
        .detail[hidden] { display: none !important; }
        .detail__shell {
            width: min(100%, 64rem);
            max-height: min(92dvh, 920px);
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            background: linear-gradient(165deg, rgba(36, 34, 28, 0.98), rgba(14, 14, 16, 0.99));
            border: 1px solid rgba(224, 197, 106, 0.35);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.55);
            overflow: hidden;
            border-radius: var(--radius);
        }
        .detail__gallery {
            position: relative;
            background: #0a0a0c;
            min-height: 18rem;
            display: flex;
            flex-direction: column;
        }
        .detail__main {
            flex: 1;
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
        }
        .detail__main img {
            width: 100%;
            height: 100%;
            max-height: 58vh;
            object-fit: contain;
            display: block;
        }
        .detail__nav {
            position: absolute;
            inset: 50% 0.65rem auto;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            pointer-events: none;
        }
        .detail__nav button {
            pointer-events: auto;
            width: 2.4rem;
            height: 2.4rem;
            border: 1px solid rgba(224, 197, 106, 0.4);
            background: rgba(12, 12, 14, 0.65);
            color: var(--gold-soft);
            cursor: pointer;
            font-size: 1.2rem;
            line-height: 1;
        }
        .detail__thumbs {
            display: flex;
            gap: 0.45rem;
            padding: 0.65rem;
            overflow-x: auto;
            border-top: 1px solid var(--line);
        }
        .detail__thumb {
            flex: 0 0 auto;
            width: 4.2rem;
            height: 3.1rem;
            border: 1px solid transparent;
            padding: 0;
            background: #111;
            cursor: pointer;
            overflow: hidden;
        }
        .detail__thumb.is-active { border-color: var(--gold); }
        .detail__thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .detail__info {
            padding: 1.35rem 1.3rem 1.2rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
        .detail__eyebrow {
            font-size: 0.65rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--gold-soft);
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-weight: 600;
        }
        .detail__title {
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(1.45rem, 3vw, 1.9rem);
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .detail__desc {
            color: var(--muted);
            line-height: 1.55;
            font-size: 0.95rem;
            white-space: pre-wrap;
        }
        .detail__meta {
            display: grid;
            gap: 0.55rem;
            margin-top: 0.25rem;
        }
        .detail__row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.55rem 0;
            border-bottom: 1px solid rgba(128, 128, 128, 0.15);
            font-size: 0.88rem;
        }
        .detail__row span { color: var(--muted); letter-spacing: 0.06em; text-transform: uppercase; font-size: 0.68rem; }
        .detail__row strong {
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-weight: 600;
            text-align: right;
        }
        .detail__price {
            margin-top: auto;
            padding-top: 0.85rem;
            border-top: 1px solid var(--line);
        }
        .detail__price-label {
            font-size: 0.68rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .detail__price-value {
            margin-top: 0.25rem;
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--gold-soft);
        }
        .detail__close {
            align-self: flex-start;
            margin-top: 0.5rem;
        }

        @media (max-width: 900px) {
            .cat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .detail__shell {
                grid-template-columns: 1fr;
                max-height: 94dvh;
                overflow-y: auto;
            }
            .detail__main img { max-height: 42vh; }
        }
        @media (max-width: 560px) {
            .cat-grid { grid-template-columns: 1fr; gap: 0.95rem; }
            .cat-top { padding: 0.9rem 0.95rem; }
            .cat-actions .btn { min-height: 2.7rem; padding: 0.55rem 0.85rem; font-size: 0.68rem; }
            .detail { padding: 0; align-items: flex-end; }
            .detail__shell {
                width: 100%;
                max-height: 94dvh;
                border-radius: var(--radius) var(--radius) 0 0;
            }
        }
    </style>
</head>
<body>
    <header class="cat-top">
        <a class="cat-brand" href="{{ route('home') }}">
            <span class="cat-brand__name">AUTO LOUNGE</span>
            <span class="cat-brand__tag">Catalogue véhicules</span>
        </a>
        <div class="cat-actions">
            <a class="btn btn--ghost" href="{{ route('home') }}">Accueil</a>
            <a class="btn btn--gold" href="{{ route('home') }}#connexion">Se Connecter</a>
        </div>
    </header>

    <section class="cat-hero">
        <p class="cat-hero__eyebrow">Sélection</p>
        <h1 class="cat-hero__title">Catalogue</h1>
        <p class="cat-hero__lead">Découvrez nos véhicules. Cliquez sur une photo pour voir la galerie et les informations.</p>
    </section>

    <div class="cat-grid">
        @forelse ($vehicules as $v)
            @php
                $photos = $v->photosUrls();
                $cover = $photos[0] ?? null;
                $titre = $v->titreAffiche();
                $desc = $v->description ?: trim(($v->marque ?? '').' '.$v->modele.' — '.$v->couleur);
            @endphp
            <article
                class="cat-card"
                data-titre="{{ $titre }}"
                data-description="{{ $v->description ?? '' }}"
                data-marque="{{ $v->marque }}"
                data-modele="{{ $v->modele }}"
                data-couleur="{{ $v->couleur ?? '' }}"
                data-km="{{ $v->kilometrage }}"
                data-prix="{{ montant_fr($v->montant_vente) }} MAD"
                data-photos='@json($photos)'
            >
                <button type="button" class="cat-card__photo" data-open-detail aria-label="Voir {{ $titre }}">
                    @if ($cover)
                        <img src="{{ $cover }}" alt="{{ $titre }}" loading="lazy">
                    @else
                        <span class="cat-card__photo-empty">Sans photo</span>
                    @endif
                </button>
                <div class="cat-card__body">
                    <h2 class="cat-card__title">{{ $titre }}</h2>
                    <p class="cat-card__desc">{{ $desc !== '' ? $desc : 'Véhicule disponible.' }}</p>
                    <div class="cat-card__price">
                        <span class="cat-card__price-label">Prix de vente</span>
                        <strong class="cat-card__price-value">{{ montant_fr($v->montant_vente) }} MAD</strong>
                    </div>
                </div>
            </article>
        @empty
            <div class="cat-empty">Aucun véhicule publié pour le moment.</div>
        @endforelse
    </div>

    <div class="detail" id="detail" hidden>
        <div class="detail__shell" role="dialog" aria-modal="true" aria-labelledby="detail-title">
            <div class="detail__gallery">
                <div class="detail__main">
                    <img id="detail-img" alt="">
                    <div class="detail__nav">
                        <button type="button" id="detail-prev" aria-label="Photo précédente">‹</button>
                        <button type="button" id="detail-next" aria-label="Photo suivante">›</button>
                    </div>
                </div>
                <div class="detail__thumbs" id="detail-thumbs"></div>
            </div>
            <div class="detail__info">
                <p class="detail__eyebrow">Fiche véhicule</p>
                <h2 class="detail__title" id="detail-title"></h2>
                <p class="detail__desc" id="detail-desc"></p>
                <div class="detail__meta">
                    <div class="detail__row"><span>Marque</span><strong id="detail-marque">—</strong></div>
                    <div class="detail__row"><span>Modèle</span><strong id="detail-modele">—</strong></div>
                    <div class="detail__row"><span>Couleur</span><strong id="detail-couleur">—</strong></div>
                    <div class="detail__row"><span>Kilométrage</span><strong id="detail-km">—</strong></div>
                </div>
                <div class="detail__price">
                    <div class="detail__price-label">Prix de vente</div>
                    <div class="detail__price-value" id="detail-prix"></div>
                </div>
                <button type="button" class="btn btn--ghost detail__close" id="detail-close">Fermer</button>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var detail = document.getElementById('detail');
        var img = document.getElementById('detail-img');
        var thumbs = document.getElementById('detail-thumbs');
        var photos = [];
        var index = 0;

        function showPhoto(i) {
            if (!photos.length) return;
            index = (i + photos.length) % photos.length;
            img.src = photos[index];
            thumbs.querySelectorAll('.detail__thumb').forEach(function (btn, n) {
                btn.classList.toggle('is-active', n === index);
            });
        }

        function openDetail(card) {
            try {
                photos = JSON.parse(card.getAttribute('data-photos') || '[]');
            } catch (e) {
                photos = [];
            }
            document.getElementById('detail-title').textContent = card.dataset.titre || '';
            document.getElementById('detail-desc').textContent = card.dataset.description || 'Aucune description.';
            document.getElementById('detail-marque').textContent = card.dataset.marque || '—';
            document.getElementById('detail-modele').textContent = card.dataset.modele || '—';
            document.getElementById('detail-couleur').textContent = card.dataset.couleur || '—';
            document.getElementById('detail-km').textContent = card.dataset.km
                ? (Number(card.dataset.km).toLocaleString('fr-FR') + ' km')
                : '—';
            document.getElementById('detail-prix').textContent = card.dataset.prix || '';
            thumbs.innerHTML = '';
            photos.forEach(function (src, n) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'detail__thumb' + (n === 0 ? ' is-active' : '');
                b.innerHTML = '<img src="' + src + '" alt="">';
                b.addEventListener('click', function () { showPhoto(n); });
                thumbs.appendChild(b);
            });
            showPhoto(0);
            detail.hidden = false;
            document.body.classList.add('is-detail-open');
        }

        function closeDetail() {
            detail.hidden = true;
            document.body.classList.remove('is-detail-open');
            photos = [];
            img.removeAttribute('src');
        }

        document.querySelectorAll('[data-open-detail]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openDetail(btn.closest('.cat-card'));
            });
        });

        document.getElementById('detail-close').addEventListener('click', closeDetail);
        document.getElementById('detail-prev').addEventListener('click', function () { showPhoto(index - 1); });
        document.getElementById('detail-next').addEventListener('click', function () { showPhoto(index + 1); });
        detail.addEventListener('click', function (e) {
            if (e.target === detail) closeDetail();
        });
        document.addEventListener('keydown', function (e) {
            if (detail.hidden) return;
            if (e.key === 'Escape') closeDetail();
            if (e.key === 'ArrowLeft') showPhoto(index - 1);
            if (e.key === 'ArrowRight') showPhoto(index + 1);
        });
    })();
    </script>
</body>
</html>
