@php
    $photo = $fiche->photoPrincipaleUrl();
    $nbPhotos = collect($fiche->photosMap())->filter()->count();
@endphp
<article class="fv-card" tabindex="0" role="button" data-id="{{ $fiche->id }}" aria-label="Voir {{ $fiche->libelle() }}">
    <div class="fv-card__media">
        @if ($photo)
            <img src="{{ $photo }}" alt="{{ $fiche->libelle() }}" loading="lazy">
        @else
            <span class="fv-card__noimg">Aucune photo</span>
        @endif
        @if ($nbPhotos > 1)
            <span class="fv-card__count">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.6"/><path d="M21 16l-5-5-8 8"/></svg>
                {{ $nbPhotos }}
            </span>
        @endif
    </div>

    <div class="fv-card__head">
        <div class="fv-card__cell">
            <span class="fv-card__label">Marque</span>
            <strong class="fv-card__brand">{{ $fiche->marque ?: '—' }}</strong>
        </div>
        <div class="fv-card__cell">
            <span class="fv-card__label">Modèle</span>
            <strong class="fv-card__model">{{ $fiche->modele ?: '—' }}</strong>
        </div>
    </div>

    <dl class="fv-card__specs">
        <div class="fv-card__spec">
            <dt>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
                Année
            </dt>
            <dd>{{ $fiche->annee ?: '—' }}</dd>
        </div>
        <div class="fv-card__spec">
            <dt>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 15a8 8 0 1 1 16 0"/><path d="M12 15l4-5"/><circle cx="12" cy="15" r="1.2"/></svg>
                Kilométrage
            </dt>
            <dd>{{ $fiche->kilometrageAffiche() }}</dd>
        </div>
        <div class="fv-card__spec">
            <dt>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 21V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16M3 21h12M4 10h10"/><path d="M14 8h2a2 2 0 0 1 2 2v6a1.5 1.5 0 0 0 3 0V9l-3-3"/></svg>
                Carburant
            </dt>
            <dd>{{ $fiche->carburantLabel() ?: '—' }}</dd>
        </div>
        <div class="fv-card__spec">
            <dt>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="6" cy="6" r="2"/><circle cx="12" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="12" cy="18" r="2"/><path d="M6 8v8M12 8v8M18 8v4H6"/></svg>
                Transmission
            </dt>
            <dd>{{ $fiche->transmissionLabel() ?: '—' }}</dd>
        </div>
    </dl>

    @if ($interne ?? false)
        <div class="fv-card__cost">
            <div class="fv-card__cell">
                <span class="fv-card__label">Coût total</span>
                <span class="fv-card__cost-detail">Achat + dépenses</span>
            </div>
            <strong class="fv-card__cost-value">{{ $fiche->coutTotalAffiche() }}</strong>
        </div>
    @endif

    <div class="fv-card__foot">
        <div class="fv-card__cell">
            <span class="fv-card__label">Prix de vente</span>
            <strong class="fv-card__price">{{ $fiche->prixAffiche() }}</strong>
        </div>
        <span class="fv-card__go" aria-hidden="true">
            Voir
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </span>
    </div>
</article>
