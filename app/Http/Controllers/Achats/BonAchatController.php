<?php

namespace App\Http\Controllers\Achats;

use App\Http\Controllers\Controller;
use App\Models\BonAchat;
use App\Models\BonAchatPaiement;
use App\Support\DateFormat;
use App\Support\MoneyFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BonAchatController extends Controller
{
    public function index(Request $request): View
    {
        $query = BonAchat::query()->latest('date_bon')->latest('id');

        if ($request->filled('mois')) {
            $query->whereMonth('date_bon', (int) $request->mois);
        }

        if ($request->filled('vehicule')) {
            $term = $request->vehicule;
            $query->where(function ($q) use ($term) {
                $q->where('type_vehicule', 'like', "%{$term}%")
                    ->orWhere('matricule', 'like', "%{$term}%");
            });
        }

        if ($request->filled('montant')) {
            $query->where('montant', MoneyFormat::parse($request->montant));
        }

        return view('achats.bon-achat', [
            'bons' => $query->with(['paiements' => fn ($q) => $q->orderBy('date_paiement')->orderBy('id')])
                ->paginate(15)
                ->withQueryString(),
            'filters' => $request->only(['mois', 'vehicule', 'montant']),
            'keepPanel' => session('keep_panel'),
            'savedBon' => session('saved_bon'),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $this->validatedBon($request);
        $data['montant_paye'] = 0;
        $data['created_by'] = $request->user()->id;

        $this->storePieceJointe($request, $data);

        $bon = BonAchat::create($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Bon d’achat validé. Vous pouvez payer ou fermer.',
                'bon' => $this->bonPayload($bon),
            ]);
        }

        return redirect()
            ->route('achats.bon-achat')
            ->with('success', 'Bon d’achat validé.')
            ->with('keep_panel', true)
            ->with('saved_bon', $this->bonPayload($bon));
    }

    public function update(Request $request, BonAchat $bonAchat): JsonResponse|RedirectResponse
    {
        $data = $this->validatedBon($request);

        if ((float) $data['montant'] < (float) $bonAchat->montant_paye) {
            $message = 'Le montant ne peut pas être inférieur au montant déjà payé.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['montant' => $message]);
        }

        $this->storePieceJointe($request, $data, $bonAchat);

        $bonAchat->update($data);
        $bonAchat->refresh();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Bon d’achat mis à jour. Vous pouvez payer ou fermer.',
                'bon' => $this->bonPayload($bonAchat),
            ]);
        }

        return redirect()
            ->route('achats.bon-achat')
            ->with('success', 'Bon d’achat modifié.')
            ->with('keep_panel', true)
            ->with('saved_bon', $this->bonPayload($bonAchat));
    }

    public function payer(Request $request, BonAchat $bonAchat): RedirectResponse
    {
        $data = $request->validate([
            'date_paiement' => ['required', 'string'],
            'montant_paiement' => ['required'],
            'mode_paiement' => ['required', 'in:especes,virement,cheque,traite,autre'],
            'reference' => ['nullable', 'string', 'max:255'],
            'nom_tire' => ['nullable', 'string', 'max:255'],
            'date_decaissement' => ['nullable', 'string'],
        ]);

        $datePaiement = DateFormat::toDatabase($data['date_paiement']);
        if (! $datePaiement) {
            throw ValidationException::withMessages([
                'date_paiement' => 'La date doit être au format jj/mm/aaaa.',
            ]);
        }

        $needsTire = in_array($data['mode_paiement'], ['cheque', 'traite'], true);
        $dateDecaissement = null;

        if ($needsTire) {
            if (blank($data['nom_tire'] ?? null)) {
                throw ValidationException::withMessages([
                    'nom_tire' => 'Le nom tiré est obligatoire pour un chèque ou une traite.',
                ]);
            }

            $dateDecaissement = DateFormat::toDatabase($data['date_decaissement'] ?? null);
            if (! $dateDecaissement) {
                throw ValidationException::withMessages([
                    'date_decaissement' => 'La date de décaissement est obligatoire (jj/mm/aaaa).',
                ]);
            }
        }

        $montantPaiement = MoneyFormat::parse($data['montant_paiement']);
        if ($montantPaiement < 0.01) {
            throw ValidationException::withMessages([
                'montant_paiement' => 'Le montant du paiement est invalide.',
            ]);
        }

        $reste = (float) $bonAchat->montant - (float) $bonAchat->montant_paye;

        if ($reste <= 0) {
            return back()->withErrors(['montant_paiement' => 'Ce bon est déjà soldé.']);
        }

        if ($montantPaiement > $reste + 0.001) {
            return back()->withErrors(['montant_paiement' => 'Le paiement dépasse le solde restant ('.$reste.').']);
        }

        BonAchatPaiement::create([
            'bon_achat_id' => $bonAchat->id,
            'date_paiement' => $datePaiement,
            'montant' => MoneyFormat::format($montantPaiement),
            'mode_paiement' => $data['mode_paiement'],
            'reference' => $data['reference'] ?? null,
            'nom_tire' => $needsTire ? $data['nom_tire'] : null,
            'date_decaissement' => $dateDecaissement,
            'created_by' => $request->user()->id,
        ]);

        $bonAchat->montant_paye = MoneyFormat::parse((float) $bonAchat->montant_paye + $montantPaiement);
        $bonAchat->save();

        return redirect()
            ->route('achats.bon-achat')
            ->with('success', 'Paiement enregistré. Solde restant : '.montant_fr($bonAchat->fresh()->solde));
    }

    private function validatedBon(Request $request): array
    {
        $data = $request->validate([
            'date_bon' => ['required', 'string'],
            'nom_vendeur' => ['required', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'type_vehicule' => ['required', 'string', 'max:255'],
            'matricule' => ['required', 'string', 'max:255'],
            'montant' => ['required'],
            'piece' => ['nullable', 'image', 'max:10240'],
        ]);

        $dateBon = DateFormat::toDatabase($data['date_bon']);
        if (! $dateBon) {
            throw ValidationException::withMessages([
                'date_bon' => 'La date doit être au format jj/mm/aaaa.',
            ]);
        }

        $data['date_bon'] = $dateBon;
        $data['montant'] = MoneyFormat::format(MoneyFormat::parse($data['montant']));
        unset($data['piece']);

        return $data;
    }

    private function storePieceJointe(Request $request, array &$data, ?BonAchat $existing = null): void
    {
        if (! $request->hasFile('piece')) {
            return;
        }

        $file = $request->file('piece');
        $path = $file->store('bons-achat', 'public');

        if ($existing?->piece_jointe) {
            Storage::disk('public')->delete($existing->piece_jointe);
        }

        $data['piece_jointe'] = $path;
        $data['piece_jointe_nom'] = $file->getClientOriginalName();
    }

    private function bonPayload(BonAchat $bon): array
    {
        return [
            'id' => $bon->id,
            'date' => date_fr($bon->date_bon),
            'vendeur' => $bon->nom_vendeur,
            'ville' => $bon->ville,
            'type' => $bon->type_vehicule,
            'matricule' => $bon->matricule,
            'montant' => montant_fr($bon->montant),
            'paye' => montant_fr($bon->montant_paye),
            'solde' => montant_fr($bon->solde),
            'piece_nom' => $bon->piece_jointe_nom,
            'piece_url' => $bon->piece_jointe ? asset('storage/'.$bon->piece_jointe) : null,
        ];
    }
}
