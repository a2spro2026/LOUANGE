<?php

namespace App\Http\Controllers\Achats;

use App\Http\Controllers\Controller;
use App\Models\BonAchat;
use App\Models\FicheVehicule;
use App\Support\DateFormat;
use App\Support\MoneyFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FicheVehiculeController extends Controller
{
    public function index(): View
    {
        $vehicules = BonAchat::query()
            ->with('ficheVehicule')
            ->whereNotNull('piece_jointe')
            ->where('piece_jointe', '!=', '')
            ->latest('date_bon')
            ->latest('id')
            ->paginate(24);

        return view('achats.fiche-vehicule', [
            'vehicules' => $vehicules,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedFiche($request);
        $bon = BonAchat::query()->findOrFail($data['bon_achat_id']);

        if ($bon->ficheVehicule) {
            return $this->update($request, $bon->ficheVehicule);
        }

        $data['created_by'] = $request->user()->id;
        $this->storePhotos($request, $data);

        $fiche = FicheVehicule::create($data);

        return response()->json([
            'ok' => true,
            'message' => 'Fiche véhicule enregistrée.',
            'fiche' => $this->fichePayload($fiche->load('bonAchat')),
        ]);
    }

    public function update(Request $request, FicheVehicule $ficheVehicule): JsonResponse
    {
        $data = $this->validatedFiche($request, $ficheVehicule);
        $this->storePhotos($request, $data, $ficheVehicule);
        $ficheVehicule->update($data);

        return response()->json([
            'ok' => true,
            'message' => 'Fiche véhicule mise à jour.',
            'fiche' => $this->fichePayload($ficheVehicule->fresh()->load('bonAchat')),
        ]);
    }

    private function validatedFiche(Request $request, ?FicheVehicule $existing = null): array
    {
        $data = $request->validate([
            'bon_achat_id' => ['required', 'integer', 'exists:bon_achats,id'],
            'date_achat' => ['required', 'string'],
            'nom_proprietaire' => ['required', 'string', 'max:255'],
            'marque' => ['required', 'string', 'max:255'],
            'modele' => ['required', 'string', 'max:255'],
            'kilometrage' => ['required', 'integer', 'min:0'],
            'couleur' => ['nullable', 'string', 'max:255'],
            'montant_achat' => ['required'],
            'titre' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'montant_vente' => ['nullable'],
            'en_catalogue' => ['nullable', 'boolean'],
            'photo_1' => ['nullable', 'image', 'max:10240'],
            'photo_2' => ['nullable', 'image', 'max:10240'],
            'photo_3' => ['nullable', 'image', 'max:10240'],
        ]);

        if ($existing && (int) $existing->bon_achat_id !== (int) $data['bon_achat_id']) {
            throw ValidationException::withMessages([
                'bon_achat_id' => 'Le véhicule lié ne peut pas être modifié.',
            ]);
        }

        $taken = FicheVehicule::query()
            ->where('bon_achat_id', $data['bon_achat_id'])
            ->when($existing, fn ($q) => $q->where('id', '!=', $existing->id))
            ->exists();

        if ($taken) {
            throw ValidationException::withMessages([
                'bon_achat_id' => 'Une fiche existe déjà pour ce bon d’achat.',
            ]);
        }

        $dateAchat = DateFormat::toDatabase($data['date_achat']);
        if (! $dateAchat) {
            throw ValidationException::withMessages([
                'date_achat' => 'La date doit être au format jj/mm/aaaa.',
            ]);
        }

        $data['date_achat'] = $dateAchat;
        $data['montant_achat'] = MoneyFormat::format(MoneyFormat::parse($data['montant_achat']));

        if (! empty($data['montant_vente'])) {
            $data['montant_vente'] = MoneyFormat::format(MoneyFormat::parse($data['montant_vente']));
        } else {
            $data['montant_vente'] = null;
        }

        $data['en_catalogue'] = $request->boolean('en_catalogue');
        $data['titre'] = isset($data['titre']) ? trim((string) $data['titre']) : null;
        $data['description'] = isset($data['description']) ? trim((string) $data['description']) : null;
        if ($data['titre'] === '') {
            $data['titre'] = null;
        }
        if ($data['description'] === '') {
            $data['description'] = null;
        }

        unset($data['photo_1'], $data['photo_2'], $data['photo_3']);

        return $data;
    }

    private function storePhotos(Request $request, array &$data, ?FicheVehicule $existing = null): void
    {
        foreach ([1, 2, 3] as $n) {
            $key = 'photo_'.$n;
            if (! $request->hasFile($key)) {
                continue;
            }

            $path = $request->file($key)->store('fiches-vehicules', 'public');

            if ($existing?->{$key}) {
                Storage::disk('public')->delete($existing->{$key});
            }

            $data[$key] = $path;
        }
    }

    private function fichePayload(FicheVehicule $fiche): array
    {
        return [
            'id' => $fiche->id,
            'bon_achat_id' => $fiche->bon_achat_id,
            'date_achat' => date_fr($fiche->date_achat),
            'nom_proprietaire' => $fiche->nom_proprietaire,
            'marque' => $fiche->marque,
            'modele' => $fiche->modele,
            'kilometrage' => (string) $fiche->kilometrage,
            'couleur' => $fiche->couleur,
            'montant_achat' => montant_fr($fiche->montant_achat),
            'titre' => $fiche->titre,
            'description' => $fiche->description,
            'montant_vente' => $fiche->montant_vente !== null ? montant_fr($fiche->montant_vente) : '',
            'en_catalogue' => (bool) $fiche->en_catalogue,
            'photo_1' => $fiche->photo_1 ? asset('storage/'.$fiche->photo_1) : null,
            'photo_2' => $fiche->photo_2 ? asset('storage/'.$fiche->photo_2) : null,
            'photo_3' => $fiche->photo_3 ? asset('storage/'.$fiche->photo_3) : null,
            'photo_principale' => $fiche->photoPrincipaleUrl(),
            'photo_bon' => $fiche->bonAchat?->piece_jointe
                ? asset('storage/'.$fiche->bonAchat->piece_jointe)
                : null,
        ];
    }
}
