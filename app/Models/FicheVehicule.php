<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FicheVehicule extends Model
{
    protected $fillable = [
        'bon_achat_id',
        'date_achat',
        'nom_proprietaire',
        'marque',
        'modele',
        'titre',
        'description',
        'kilometrage',
        'couleur',
        'montant_achat',
        'montant_vente',
        'en_catalogue',
        'photo_1',
        'photo_2',
        'photo_3',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_achat' => 'date',
            'montant_achat' => 'decimal:2',
            'montant_vente' => 'decimal:2',
            'kilometrage' => 'integer',
            'en_catalogue' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bonAchat(): BelongsTo
    {
        return $this->belongsTo(BonAchat::class);
    }

    public function titreAffiche(): string
    {
        if (filled($this->titre)) {
            return (string) $this->titre;
        }

        $label = trim(($this->marque ?? '').' '.($this->modele ?? ''));

        return $label !== '' ? $label : (string) ($this->bonAchat?->type_vehicule ?? 'Véhicule');
    }

    public function photoPrincipaleUrl(): ?string
    {
        foreach ($this->photosUrls() as $url) {
            return $url;
        }

        return null;
    }

    /** @return list<string> */
    public function photosUrls(): array
    {
        $urls = [];

        foreach (['photo_1', 'photo_2', 'photo_3'] as $field) {
            if ($this->{$field}) {
                $urls[] = asset('storage/'.$this->{$field});
            }
        }

        if ($urls === [] && $this->bonAchat?->piece_jointe) {
            $urls[] = asset('storage/'.$this->bonAchat->piece_jointe);
        }

        return $urls;
    }
}
