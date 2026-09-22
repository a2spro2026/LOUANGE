<?php

namespace Database\Seeders;

use App\Models\BonAchat;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestVoituresSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');

        $cars = [
            [
                'file' => 'test-voiture-berline-noire.png',
                'type' => 'Berline Noire',
                'matricule' => '12345-A-6',
                'vendeur' => 'Garage Atlas',
                'ville' => 'Casablanca',
                'montant' => 185000,
            ],
            [
                'file' => 'test-voiture-suv-blanc.png',
                'type' => 'SUV Blanc',
                'matricule' => '67890-B-7',
                'vendeur' => 'Auto Prestige',
                'ville' => 'Rabat',
                'montant' => 245000,
            ],
            [
                'file' => 'test-voiture-hatch-rouge.png',
                'type' => 'Hatch Rouge',
                'matricule' => '11223-C-1',
                'vendeur' => 'Salam Motors',
                'ville' => 'Marrakech',
                'montant' => 98000,
            ],
            [
                'file' => 'test-voiture-berline-argent.png',
                'type' => 'Berline Argent',
                'matricule' => '44556-D-8',
                'vendeur' => 'Louange Import',
                'ville' => 'Tanger',
                'montant' => 162500,
            ],
        ];

        foreach ($cars as $i => $car) {
            BonAchat::query()->updateOrCreate(
                ['matricule' => $car['matricule']],
                [
                    'date_bon' => now()->subDays(10 - $i)->toDateString(),
                    'nom_vendeur' => $car['vendeur'],
                    'ville' => $car['ville'],
                    'type_vehicule' => $car['type'],
                    'montant' => $car['montant'],
                    'montant_paye' => 0,
                    'piece_jointe' => 'bons-achat/'.$car['file'],
                    'piece_jointe_nom' => $car['file'],
                    'created_by' => $userId,
                ]
            );
        }
    }
}
