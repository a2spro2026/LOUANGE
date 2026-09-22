<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonAchatPaiement extends Model
{
    protected $fillable = [
        'bon_achat_id',
        'date_paiement',
        'montant',
        'mode_paiement',
        'reference',
        'nom_tire',
        'date_decaissement',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_paiement' => 'date',
            'date_decaissement' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function bonAchat(): BelongsTo
    {
        return $this->belongsTo(BonAchat::class);
    }
}
