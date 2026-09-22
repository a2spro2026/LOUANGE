<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BonAchat extends Model
{
    protected $fillable = [
        'date_bon',
        'nom_vendeur',
        'ville',
        'type_vehicule',
        'matricule',
        'montant',
        'montant_paye',
        'note',
        'piece_jointe',
        'piece_jointe_nom',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_bon' => 'date',
            'montant' => 'decimal:2',
            'montant_paye' => 'decimal:2',
        ];
    }

    public function getSoldeAttribute(): string
    {
        return number_format((float) $this->montant - (float) $this->montant_paye, 2, '.', '');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(BonAchatPaiement::class);
    }

    public function ficheVehicule(): HasOne
    {
        return $this->hasOne(FicheVehicule::class);
    }
}
