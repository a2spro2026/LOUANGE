<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FicheVehicule extends Model
{
    public const CARBURANTS = [
        'diesel' => 'Diesel',
        'essence' => 'Essence',
        'hybride' => 'Hybride',
        'electrique' => 'Électrique',
    ];

    public const TRANSMISSIONS = [
        'manuelle' => 'Manuelle',
        'automatique' => 'Automatique',
    ];

    public const MARQUES = [
        'Abarth', 'Alfa Romeo', 'Alpine', 'Aston Martin', 'Audi', 'BAIC', 'Bentley', 'BMW', 'Bugatti', 'BYD',
        'Cadillac', 'Changan', 'Chery', 'Chevrolet', 'Chrysler', 'Citroën', 'Cupra', 'Dacia', 'Daewoo', 'Daihatsu',
        'DFSK', 'Dodge', 'Dongfeng', 'DS', 'Exeed', 'Ferrari', 'Fiat', 'Ford', 'Foton', 'GAC',
        'Geely', 'Genesis', 'GMC', 'Great Wall', 'Haval', 'Honda', 'Hongqi', 'Hummer', 'Hyundai', 'Infiniti',
        'Isuzu', 'Iveco', 'JAC', 'Jaguar', 'Jeep', 'Jetour', 'KGM', 'Kia', 'Lada', 'Lamborghini',
        'Lancia', 'Land Rover', 'Leapmotor', 'Lexus', 'Lincoln', 'Lotus', 'Lynk & Co', 'Mahindra', 'Maserati', 'Maybach',
        'Mazda', 'McLaren', 'Mercedes-Benz', 'MG', 'Mini', 'Mitsubishi', 'Nio', 'Nissan', 'Omoda', 'Opel',
        'Peugeot', 'Polestar', 'Porsche', 'Proton', 'RAM', 'Renault', 'Rolls-Royce', 'Rover', 'Saab', 'Seat',
        'Skoda', 'Smart', 'SsangYong', 'Subaru', 'Suzuki', 'Tata', 'Tesla', 'Toyota', 'Volkswagen', 'Volvo',
        'Xpeng', 'Zeekr',
    ];

    public const PHOTOS = ['photo_1', 'photo_2', 'photo_3', 'photo_4', 'photo_5', 'photo_6', 'photo_7'];

    protected $fillable = [
        'bon_achat_id',
        'date_achat',
        'nom_proprietaire',
        'marque',
        'modele',
        'kilometrage',
        'annee',
        'annee_douane',
        'carburant',
        'transmission',
        'puissance_fiscale',
        'couleur',
        'montant_achat',
        'montant_depenses',
        'montant_vente',
        'photo_1',
        'photo_2',
        'photo_3',
        'photo_4',
        'photo_5',
        'photo_6',
        'photo_7',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_achat' => 'date',
            'montant_achat' => 'decimal:2',
            'montant_depenses' => 'decimal:2',
            'montant_vente' => 'decimal:2',
            'kilometrage' => 'integer',
            'annee' => 'integer',
            'annee_douane' => 'integer',
            'puissance_fiscale' => 'integer',
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

    public function libelle(): string
    {
        $label = trim(($this->marque ?? '').' '.($this->modele ?? ''));

        return $label !== '' ? $label : 'Véhicule';
    }

    public function carburantLabel(): string
    {
        return self::CARBURANTS[$this->carburant] ?? '';
    }

    public function transmissionLabel(): string
    {
        return self::TRANSMISSIONS[$this->transmission] ?? '';
    }

    public function photoPrincipaleUrl(): ?string
    {
        if ($this->photo_1) {
            return asset('storage/'.$this->photo_1);
        }

        if ($this->bonAchat?->piece_jointe) {
            return asset('storage/'.$this->bonAchat->piece_jointe);
        }

        return null;
    }

    public function prixAffiche(): string
    {
        return $this->montant_vente !== null
            ? number_format((float) $this->montant_vente, 0, ',', ' ').' MAD'
            : '—';
    }

    public function kilometrageAffiche(): string
    {
        return $this->kilometrage !== null
            ? number_format((int) $this->kilometrage, 0, ',', ' ').' km'
            : '—';
    }

    public function prixAchat(): ?float
    {
        if ($this->montant_achat !== null) {
            return (float) $this->montant_achat;
        }

        return $this->bonAchat?->montant !== null ? (float) $this->bonAchat->montant : null;
    }

    public function coutTotal(): ?float
    {
        $achat = $this->prixAchat();

        if ($achat === null && $this->montant_depenses === null) {
            return null;
        }

        return round(($achat ?? 0) + (float) ($this->montant_depenses ?? 0), 2);
    }

    public function coutTotalAffiche(): string
    {
        $cout = $this->coutTotal();

        return $cout !== null ? number_format($cout, 0, ',', ' ').' MAD' : '—';
    }

    public function cataloguePayload(bool $interne = false): array
    {
        $payload = $this->publicPayload();

        if ($interne) {
            $achat = $this->prixAchat();
            $payload['prix_achat'] = $achat !== null ? montant_fr($achat) : '';
            $payload['depenses'] = $this->montant_depenses !== null ? montant_fr($this->montant_depenses) : '';
            $payload['cout_total'] = $this->coutTotal() !== null ? montant_fr($this->coutTotal()) : '';
        }

        return $payload;
    }

    private function publicPayload(): array
    {
        return [
            'id' => $this->id,
            'marque' => $this->marque,
            'modele' => $this->modele,
            'kilometrage' => $this->kilometrage,
            'annee' => $this->annee,
            'annee_douane' => $this->annee_douane,
            'carburant' => $this->carburant,
            'carburant_label' => $this->carburantLabel(),
            'transmission' => $this->transmission,
            'transmission_label' => $this->transmissionLabel(),
            'puissance_fiscale' => $this->puissance_fiscale,
            'prix' => $this->montant_vente !== null ? montant_fr($this->montant_vente) : '',
            'photo_principale' => $this->photoPrincipaleUrl(),
            'photos' => $this->photosMap(),
        ];
    }

    /** @return array<string, string|null> */
    public function photosMap(): array
    {
        $map = [];

        foreach (self::PHOTOS as $field) {
            $map[$field] = $this->{$field} ? asset('storage/'.$this->{$field}) : null;
        }

        return $map;
    }
}
