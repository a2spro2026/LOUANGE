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
        'kilometrage',
        'couleur',
        'montant_achat',
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
            'kilometrage' => 'integer',
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

    public function photoPrincipaleUrl(): ?string
    {
        foreach (['photo_1', 'photo_2', 'photo_3'] as $field) {
            if ($this->{$field}) {
                return asset('storage/'.$this->{$field});
            }
        }

        if ($this->relationLoaded('bonAchat') && $this->bonAchat?->piece_jointe) {
            return asset('storage/'.$this->bonAchat->piece_jointe);
        }

        return null;
    }
}
