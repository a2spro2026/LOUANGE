@php $editable = $editable ?? false; @endphp
<style>
    .fv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(17rem, 1fr));
        gap: 1.25rem;
        align-content: start;
    }
    .fv-empty {
        grid-column: 1 / -1; padding: 2.5rem 1rem; text-align: center;
        border: 1px dashed var(--line); border-radius: var(--radius); color: var(--muted);
    }

    .fv-card {
        display: flex; flex-direction: column; gap: 0.85rem;
        padding: 0.7rem 0.7rem 1.15rem;
        border: 1px solid var(--line); border-radius: var(--radius);
        background: var(--fv-card-bg, var(--panel));
        cursor: pointer; outline: none; min-width: 0;
        transition: border-color 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
    }
    .fv-card:hover, .fv-card:focus-visible {
        border-color: var(--gold);
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35), 0 0 0 1px color-mix(in srgb, var(--gold) 35%, transparent);
    }
    .fv-card__media {
        position: relative; aspect-ratio: 16 / 10; overflow: hidden;
        border-radius: calc(var(--radius) - 0.7rem) calc(var(--radius) - 0.7rem) 18px 18px;
        background: #0c0c0e;
    }
    .fv-card__media img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform 0.5s ease;
    }
    .fv-card:hover .fv-card__media img { transform: scale(1.04); }
    .fv-card__noimg {
        position: absolute; inset: 0; display: grid; place-items: center;
        color: var(--muted); font-size: 0.85rem;
    }
    .fv-card__count {
        position: absolute; right: 1.1rem; bottom: 0.7rem;
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.2rem 0.6rem; border-radius: 999px;
        background: rgba(8, 8, 10, 0.72); color: #f2f0ec; font-size: 0.72rem; font-weight: 600;
    }
    .fv-card__count svg { width: 0.9rem; height: 0.9rem; }

    .fv-card__head {
        display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 0.75rem;
        padding: 0 0.85rem;
    }
    .fv-card__cell { display: flex; flex-direction: column; gap: 0.15rem; min-width: 0; }
    .fv-card__label {
        font-size: 0.6rem; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase;
        color: var(--gold-soft); opacity: 0.85;
    }
    .fv-card__brand, .fv-card__model {
        font-size: 1.02rem; font-weight: 600; line-height: 1.2;
        color: var(--text-strong, var(--mist));
        overflow-wrap: anywhere;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .fv-card__brand { font-family: "Cormorant Garamond", Georgia, serif; font-size: 1.25rem; letter-spacing: 0.04em; }

    .fv-card__specs {
        margin: 0; padding: 0 0.6rem;
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.45rem;
    }
    .fv-card__spec {
        display: flex; flex-direction: column; gap: 0.2rem; min-width: 0;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--line); border-radius: 16px;
        background: color-mix(in srgb, var(--gold) 5%, transparent);
    }
    .fv-card__spec dt {
        display: flex; align-items: center; gap: 0.35rem;
        font-size: 0.58rem; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: var(--muted);
    }
    .fv-card__spec dt svg { width: 0.85rem; height: 0.85rem; color: var(--gold-soft); flex-shrink: 0; }
    .fv-card__spec dd {
        margin: 0; font-size: 0.88rem; font-weight: 600; color: var(--text-strong, var(--mist));
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .fv-card__cost {
        display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;
        margin: 0 0.6rem; padding: 0.6rem 0.85rem;
        border: 1px solid color-mix(in srgb, #6aa3d4 45%, var(--line)); border-radius: 16px;
        background: color-mix(in srgb, #6aa3d4 10%, transparent);
    }
    .fv-card__cost .fv-card__label { color: #8fbbe0; }
    .fv-card__cost-detail { font-size: 0.66rem; color: var(--muted); }
    .fv-card__cost-value {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 1.02rem; font-weight: 700; color: var(--text-strong, var(--mist)); white-space: nowrap;
    }
    html[data-theme="light"] .fv-card__cost .fv-card__label { color: #2f6a9e; }

    .fv-card__foot {
        display: flex; align-items: flex-end; justify-content: space-between; gap: 0.75rem;
        margin: 0 0.85rem; padding-top: 0.8rem;
        border-top: 1px dashed var(--line);
    }
    .fv-card__price {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        font-size: 1.3rem; font-weight: 700; color: var(--gold-soft); white-space: nowrap;
        font-variant-numeric: tabular-nums lining-nums;
    }
    .fv-card__go {
        display: inline-flex; align-items: center; gap: 0.3rem; flex-shrink: 0;
        padding: 0.35rem 0.8rem; border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--gold) 55%, transparent);
        color: var(--gold-soft); font-size: 0.7rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .fv-card__go svg { width: 0.85rem; height: 0.85rem; }
    .fv-card:hover .fv-card__go { background: var(--gold); color: #121212; }

    /* Panneau détail */
    .fvd-overlay {
        position: fixed; inset: 0; z-index: 80;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem; background: rgba(6, 6, 8, 0.72); backdrop-filter: blur(8px);
    }
    .fvd-overlay[hidden] { display: none !important; }
    .fvd-panel {
        position: relative; width: min(100%, 66rem); max-height: 92vh; overflow-y: auto;
        padding: 2rem 2.2rem; scrollbar-width: none;
        background: var(--panel); border: 1px solid var(--line); border-radius: var(--radius);
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.5);
        color: var(--mist);
    }
    .fvd-panel::-webkit-scrollbar { display: none; }
    .fvd-close {
        position: absolute; top: 1.3rem; right: 1.5rem; z-index: 2;
        width: 2.4rem; height: 2.4rem; display: grid; place-items: center; padding: 0;
        border: 1px solid var(--line); border-radius: 50%; background: transparent; color: var(--muted); cursor: pointer;
    }
    .fvd-close:hover { border-color: var(--gold); color: var(--gold-soft); }
    .fvd-close svg { width: 1.1rem; height: 1.1rem; }
    .fvd-print-brand { display: none; }
    .fvd-body { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr); gap: 1.8rem; }

    .fvd-gallery { display: flex; flex-direction: column; gap: 0.7rem; min-width: 0; }
    .fvd-main {
        position: relative; aspect-ratio: 4 / 3; overflow: hidden;
        border: 1px solid color-mix(in srgb, var(--gold) 40%, var(--line));
        border-radius: 30px; background: #0c0c0e;
    }
    .fvd-main img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .fvd-main__empty { position: absolute; inset: 0; display: grid; place-items: center; color: var(--muted); }
    .fvd-main__empty[hidden], .fvd-main img[hidden] { display: none; }
    .fvd-nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 2.5rem; height: 2.5rem; display: grid; place-items: center; padding: 0;
        border: 1px solid rgba(242, 240, 236, 0.3); border-radius: 50%;
        background: rgba(8, 8, 10, 0.6); color: #f2f0ec; cursor: pointer;
    }
    .fvd-nav:hover { border-color: var(--gold); color: var(--gold-soft); }
    .fvd-nav svg { width: 1.1rem; height: 1.1rem; }
    .fvd-nav--prev { left: 0.9rem; }
    .fvd-nav--next { right: 0.9rem; }
    .fvd-nav[hidden] { display: none; }
    .fvd-count {
        position: absolute; bottom: 0.9rem; left: 50%; transform: translateX(-50%);
        padding: 0.15rem 0.7rem; border-radius: 999px;
        background: rgba(8, 8, 10, 0.65); color: #f2f0ec; font-size: 0.75rem;
    }
    .fvd-thumbs { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 0.45rem; }
    .fvd-thumbs[hidden] { display: none; }
    .fvd-thumb {
        aspect-ratio: 4 / 3; padding: 0; overflow: hidden; cursor: pointer;
        border: 1px solid var(--line); border-radius: 12px; background: #0c0c0e; opacity: 0.6;
        transition: opacity 0.2s ease, border-color 0.2s ease;
    }
    .fvd-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .fvd-thumb:hover { opacity: 1; }
    .fvd-thumb.is-active { opacity: 1; border-color: var(--gold); box-shadow: 0 0 0 1px var(--gold); }

    .fvd-info { display: flex; flex-direction: column; gap: 0.9rem; min-width: 0; padding-top: 0.4rem; }
    .fvd-head { padding-right: 2.8rem; }
    .fvd-eyebrow {
        margin: 0; font-size: 0.66rem; font-weight: 600; letter-spacing: 0.24em; text-transform: uppercase;
        color: var(--gold-soft);
    }
    .fvd-title {
        margin: 0.15rem 0 0; font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 2rem; font-weight: 600; letter-spacing: 0.03em; line-height: 1.1;
        color: var(--text-strong, var(--mist));
    }
    .fvd-price {
        display: flex; flex-direction: column; gap: 0.15rem;
        padding: 0.85rem 1.2rem; border-radius: 22px;
        border: 1px solid color-mix(in srgb, var(--gold) 45%, transparent);
        background: color-mix(in srgb, var(--gold) 10%, transparent);
    }
    .fvd-price span, .fvd-list dt {
        font-size: 0.6rem; font-weight: 600; letter-spacing: 0.16em; text-transform: uppercase; color: var(--muted);
    }
    .fvd-price strong {
        font-family: "Space Grotesk", "Outfit", sans-serif; font-size: 1.75rem; font-weight: 700; color: var(--gold-soft);
    }
    .fvd-list { margin: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.5rem; }
    .fvd-item {
        display: flex; flex-direction: column; gap: 0.2rem; min-width: 0;
        padding: 0.6rem 0.85rem; border: 1px solid var(--line); border-radius: 16px;
    }
    .fvd-item--cost {
        grid-column: 1 / -1;
        border-color: color-mix(in srgb, #6aa3d4 45%, var(--line));
        background: color-mix(in srgb, #6aa3d4 10%, transparent);
    }
    .fvd-list dd {
        margin: 0; font-size: 0.95rem; font-weight: 600; color: var(--text-strong, var(--mist));
        overflow-wrap: anywhere;
    }
    .fvd-actions { display: flex; flex-wrap: wrap; gap: 0.55rem; margin-top: auto; padding-top: 0.4rem; }

    @media (max-width: 860px) {
        .fvd-body { grid-template-columns: 1fr; gap: 1.1rem; }
        .fvd-panel { padding: 1.4rem 1.2rem; }
        .fvd-head { padding-right: 0; }
        .fvd-close { top: 1.6rem; right: 1.6rem; background: rgba(8, 8, 10, 0.6); color: #f2f0ec; }
    }
    @media (max-width: 700px) {
        .fv-grid { grid-template-columns: 1fr; gap: 1rem; }
        .fvd-overlay { padding: 0; align-items: flex-end; }
        .fvd-panel {
            max-height: 94vh; border-radius: 28px 28px 0 0;
            padding-bottom: calc(1.3rem + env(safe-area-inset-bottom));
        }
        .fvd-main { border-radius: 22px; }
        .fvd-thumbs { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        .fvd-title { font-size: 1.55rem; }
        .fvd-price strong { font-size: 1.45rem; }
        .fvd-actions .btn { flex: 1 1 0; }
    }

    @media print {
        body.fvd-printing * { visibility: hidden !important; }
        body.fvd-printing #fvd-overlay, body.fvd-printing #fvd-overlay * { visibility: visible !important; }
        body.fvd-printing #fvd-overlay {
            position: absolute !important; inset: 0 auto auto 0 !important; display: block !important;
            width: 100% !important; background: #fff !important; padding: 0 !important; backdrop-filter: none !important;
        }
        body.fvd-printing .fvd-panel {
            width: 100% !important; max-height: none !important; overflow: visible !important;
            border: none !important; box-shadow: none !important; background: #fff !important; padding: 0 !important; color: #111 !important;
        }
        body.fvd-printing .fvd-print-brand {
            display: flex !important; align-items: center; gap: 0.9rem;
            padding-bottom: 0.7rem; margin-bottom: 1rem; border-bottom: 2px solid #c9a227;
        }
        .fvd-print-brand p { margin: 0; }
        .fvd-print-brand__name { font-size: 1.3rem; font-weight: 700; letter-spacing: 0.12em; color: #111 !important; }
        .fvd-print-brand__tag { font-size: 0.75rem; letter-spacing: 0.16em; text-transform: uppercase; color: #8a7020 !important; }
        body.fvd-printing .fvd-body { grid-template-columns: 1.3fr 1fr !important; }
        body.fvd-printing .fvd-title, body.fvd-printing .fvd-list dd { color: #111 !important; }
        body.fvd-printing .fvd-list dt, body.fvd-printing .fvd-price span { color: #555 !important; }
        body.fvd-printing .fvd-price strong { color: #8a7020 !important; }
        body.fvd-printing .fvd-thumb { opacity: 1 !important; }
        body.fvd-printing .no-print { display: none !important; }
    }
</style>

<script type="application/json" id="fv-data">{!! json_encode((object) collect($payloads)->all(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>

<div class="fvd-overlay" id="fvd-overlay" hidden>
    <div class="fvd-panel" role="dialog" aria-modal="true" aria-labelledby="fvd-title">
        <header class="fvd-print-brand">
            <img src="{{ asset('images/logo-ste-louange-auto.svg') }}" alt="" width="56" height="56">
            <div>
                <p class="fvd-print-brand__name">STE LOUANGE AUTO</p>
                <p class="fvd-print-brand__tag">Fiche véhicule</p>
            </div>
        </header>
        <button type="button" class="fvd-close no-print" data-fvd-close title="Fermer" aria-label="Fermer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
        <div class="fvd-body">
            <div class="fvd-gallery">
                <div class="fvd-main">
                    <img id="fvd-main" alt="">
                    <span class="fvd-main__empty" id="fvd-empty" hidden>Aucune photo</span>
                    <button type="button" class="fvd-nav fvd-nav--prev no-print" id="fvd-prev" aria-label="Photo précédente">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button type="button" class="fvd-nav fvd-nav--next no-print" id="fvd-next" aria-label="Photo suivante">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                    <span class="fvd-count no-print" id="fvd-count"></span>
                </div>
                <div class="fvd-thumbs" id="fvd-thumbs"></div>
            </div>
            <div class="fvd-info">
                <div class="fvd-head">
                    <p class="fvd-eyebrow" id="fvd-brand"></p>
                    <h2 class="fvd-title" id="fvd-title"></h2>
                </div>
                <div class="fvd-price">
                    <span>Prix de vente</span>
                    <strong id="fvd-prix"></strong>
                </div>
                <dl class="fvd-list" id="fvd-list"></dl>
                <div class="fvd-actions no-print">
                    @if ($editable)
                        <button type="button" class="btn btn--gold" id="fvd-edit">Modifier</button>
                        <button type="button" class="btn btn--ghost" id="fvd-print">Imprimer</button>
                    @endif
                    <button type="button" class="btn btn--ghost" data-fvd-close>Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.FvDetail = (function () {
    var fiches = JSON.parse(document.getElementById('fv-data').textContent || '{}');
    var overlay = document.getElementById('fvd-overlay');
    var photoKeys = ['photo_1', 'photo_2', 'photo_3', 'photo_4', 'photo_5', 'photo_6', 'photo_7'];
    var current = null;
    var gallery = [];
    var index = 0;

    function formatInt(n) { return Number(n).toLocaleString('fr-FR', { maximumFractionDigits: 0 }); }
    function formatPrix(v) {
        if (v === '' || v === null || v === undefined || isNaN(Number(v))) return '—';
        return formatInt(v) + ' MAD';
    }

    function show(i) {
        var main = document.getElementById('fvd-main');
        var empty = document.getElementById('fvd-empty');
        var prev = document.getElementById('fvd-prev');
        var next = document.getElementById('fvd-next');
        var count = document.getElementById('fvd-count');
        if (!gallery.length) {
            main.removeAttribute('src');
            main.hidden = true;
            empty.hidden = false;
            prev.hidden = next.hidden = true;
            count.textContent = '';
            return;
        }
        index = (i + gallery.length) % gallery.length;
        main.src = gallery[index];
        main.hidden = false;
        empty.hidden = true;
        prev.hidden = next.hidden = gallery.length < 2;
        count.textContent = (index + 1) + ' / ' + gallery.length;
        document.querySelectorAll('#fvd-thumbs .fvd-thumb').forEach(function (t, k) {
            t.classList.toggle('is-active', k === index);
        });
    }

    function open(id) {
        var f = fiches[id];
        if (!f) return;
        current = f;

        gallery = [];
        photoKeys.forEach(function (k) { if (f.photos && f.photos[k]) gallery.push(f.photos[k]); });
        if (!gallery.length && f.photo_principale) gallery.push(f.photo_principale);

        var label = ((f.marque || '') + ' ' + (f.modele || '')).trim() || 'Véhicule';
        document.getElementById('fvd-main').alt = label;
        document.getElementById('fvd-brand').textContent = f.marque || '';
        document.getElementById('fvd-title').textContent = f.modele || label;
        document.getElementById('fvd-prix').textContent = formatPrix(f.prix);

        var thumbs = document.getElementById('fvd-thumbs');
        thumbs.innerHTML = '';
        thumbs.hidden = gallery.length < 2;
        gallery.forEach(function (url, k) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'fvd-thumb';
            b.setAttribute('aria-label', k === 0 ? 'Photo principale' : 'Photo ' + (k + 1));
            var img = document.createElement('img');
            img.src = url;
            img.alt = '';
            img.loading = 'lazy';
            b.appendChild(img);
            b.addEventListener('click', function () { show(k); });
            thumbs.appendChild(b);
        });
        show(0);

        var rows = [
            ['Marque', f.marque],
            ['Modèle', f.modele],
            ['Année', f.annee],
            ['Année douane', f.annee_douane],
            ['Kilométrage', f.kilometrage !== null && f.kilometrage !== undefined ? formatInt(f.kilometrage) + ' km' : ''],
            ['Puissance fiscale', f.puissance_fiscale ? f.puissance_fiscale + ' CV' : ''],
            ['Carburant', f.carburant_label],
            ['Transmission', f.transmission_label]
        ];
        if (f.cout_total !== undefined) {
            rows.push(
                ['Prix d’achat', formatPrix(f.prix_achat)],
                ['Dépenses', formatPrix(f.depenses)],
                ['Coût total (achat + dépenses)', formatPrix(f.cout_total), true]
            );
        }
        var list = document.getElementById('fvd-list');
        list.innerHTML = '';
        rows.forEach(function (r) {
            var item = document.createElement('div');
            item.className = 'fvd-item' + (r[2] ? ' fvd-item--cost' : '');
            var dt = document.createElement('dt');
            dt.textContent = r[0];
            var dd = document.createElement('dd');
            dd.textContent = (r[1] === null || r[1] === undefined || r[1] === '') ? '—' : String(r[1]);
            item.appendChild(dt);
            item.appendChild(dd);
            list.appendChild(item);
        });

        overlay.querySelector('.fvd-panel').scrollTop = 0;
        overlay.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function close() {
        overlay.hidden = true;
        document.body.style.overflow = '';
    }

    document.getElementById('fvd-prev').addEventListener('click', function () { show(index - 1); });
    document.getElementById('fvd-next').addEventListener('click', function () { show(index + 1); });
    overlay.querySelectorAll('[data-fvd-close]').forEach(function (b) { b.addEventListener('click', close); });
    overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });

    var printBtn = document.getElementById('fvd-print');
    if (printBtn) {
        printBtn.addEventListener('click', function () {
            document.body.classList.add('fvd-printing');
            window.print();
        });
        window.addEventListener('afterprint', function () { document.body.classList.remove('fvd-printing'); });
    }

    document.addEventListener('keydown', function (e) {
        if (overlay.hidden) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show(index - 1);
        if (e.key === 'ArrowRight') show(index + 1);
    });

    document.querySelectorAll('[data-fv-grid]').forEach(function (grid) {
        grid.addEventListener('click', function (e) {
            var card = e.target.closest('.fv-card');
            if (card) open(card.dataset.id);
        });
        grid.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            var card = e.target.closest('.fv-card');
            if (!card) return;
            e.preventDefault();
            open(card.dataset.id);
        });
    });

    return {
        open: open,
        close: close,
        current: function () { return current; }
    };
})();
</script>
