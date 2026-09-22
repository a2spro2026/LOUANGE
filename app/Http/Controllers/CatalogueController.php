<?php

namespace App\Http\Controllers;

use App\Models\FicheVehicule;
use Illuminate\View\View;

class CatalogueController extends Controller
{
    public function index(): View
    {
        $vehicules = FicheVehicule::query()
            ->with('bonAchat')
            ->where('en_catalogue', true)
            ->whereNotNull('montant_vente')
            ->latest('id')
            ->get();

        return view('catalogue', [
            'vehicules' => $vehicules,
        ]);
    }
}
