<?php

namespace App\Http\Controllers\Achats;

use App\Http\Controllers\Controller;
use App\Models\FicheVehicule;
use App\Support\MoneyFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FicheVehiculeController extends Controller
{
    public function index(): View
    {
        $fiches = FicheVehicule::query()
            ->with('bonAchat')
            ->latest('id')
            ->paginate(24);

        return view('achats.fiche-vehicule', [
            'fiches' => $fiches,
            'payloads' => $fiches->getCollection()->mapWithKeys(fn (FicheVehicule $f) => [$f->id => $f->cataloguePayload(true)]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedFiche($request);
        $data['created_by'] = $request->user()->id;
        $this->storePhotos($request, $data);

        $fiche = FicheVehicule::create($data);

        return response()->json([
            'ok' => true,
            'message' => 'Fiche véhicule ajoutée.',
            'fiche' => $fiche->cataloguePayload(true),
        ]);
    }

    public function update(Request $request, FicheVehicule $ficheVehicule): JsonResponse
    {
        $data = $this->validatedFiche($request);
        $this->storePhotos($request, $data, $ficheVehicule);
        $ficheVehicule->update($data);

        return response()->json([
            'ok' => true,
            'message' => 'Fiche véhicule mise à jour.',
            'fiche' => $ficheVehicule->fresh()->cataloguePayload(true),
        ]);
    }

    private function validatedFiche(Request $request): array
    {
        $maxYear = (int) now()->year + 1;

        $rules = [
            'marque' => ['required', 'string', 'max:255'],
            'modele' => ['required', 'string', 'max:255'],
            'kilometrage' => ['required', 'integer', 'min:0'],
            'annee' => ['required', 'integer', 'between:1950,'.$maxYear],
            'annee_douane' => ['nullable', 'integer', 'between:1950,'.$maxYear],
            'carburant' => ['required', Rule::in(array_keys(FicheVehicule::CARBURANTS))],
            'transmission' => ['required', Rule::in(array_keys(FicheVehicule::TRANSMISSIONS))],
            'puissance_fiscale' => ['nullable', 'integer', 'between:1,99'],
            'montant_vente' => ['required'],
            'montant_achat' => ['nullable'],
            'montant_depenses' => ['nullable'],
        ];

        foreach (FicheVehicule::PHOTOS as $field) {
            $rules[$field] = ['nullable', 'image', 'max:10240'];
            $rules['remove_'.$field] = ['nullable', 'boolean'];
        }

        $data = $request->validate($rules, [
            'required' => 'Le champ :attribute est obligatoire.',
            'integer' => 'Le champ :attribute doit être un nombre entier.',
            'min' => ['numeric' => 'Le champ :attribute doit être au moins :min.'],
            'between' => ['numeric' => 'Le champ :attribute doit être entre :min et :max.'],
            'in' => 'La valeur du champ :attribute est invalide.',
            'image' => 'Le fichier :attribute doit être une image.',
            'max' => [
                'file' => 'La :attribute ne doit pas dépasser 10 Mo.',
                'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            ],
            'string' => 'Le champ :attribute est invalide.',
        ], [
            'modele' => 'modèle',
            'kilometrage' => 'kilométrage',
            'annee' => 'année',
            'annee_douane' => 'année douane',
            'puissance_fiscale' => 'puissance fiscale',
            'montant_vente' => 'prix',
            'photo_1' => 'photo principale',
            'photo_2' => 'photo 1',
            'photo_3' => 'photo 2',
            'photo_4' => 'photo 3',
            'photo_5' => 'photo 4',
            'photo_6' => 'photo 5',
            'photo_7' => 'photo 6',
        ]);

        $data['marque'] = trim($data['marque']);
        $data['modele'] = trim($data['modele']);
        $data['montant_vente'] = MoneyFormat::format(MoneyFormat::parse($data['montant_vente']));
        foreach (['montant_achat', 'montant_depenses'] as $field) {
            $data[$field] = filled($data[$field] ?? null)
                ? MoneyFormat::format(MoneyFormat::parse($data[$field]))
                : null;
        }

        foreach (FicheVehicule::PHOTOS as $field) {
            unset($data[$field], $data['remove_'.$field]);
        }

        return $data;
    }

    private function storePhotos(Request $request, array &$data, ?FicheVehicule $existing = null): void
    {
        foreach (FicheVehicule::PHOTOS as $field) {
            $old = $existing?->{$field};

            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('fiches-vehicules', 'public');
            } elseif ($request->boolean('remove_'.$field)) {
                $data[$field] = null;
            } else {
                continue;
            }

            if ($old) {
                Storage::disk('public')->delete($old);
            }
        }
    }
}
