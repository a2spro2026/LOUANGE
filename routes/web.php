<?php

use App\Http\Controllers\Achats\BonAchatController;
use App\Http\Controllers\Achats\FicheVehiculeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Parametres\UtilisateurController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

Route::get('/catalogue', [CatalogueController::class, 'index'])->name('catalogue');

Route::post('/connexion', [AuthController::class, 'login'])->name('connexion');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('deconnexion');

    Route::get('/achats/bon-achat', [BonAchatController::class, 'index'])->name('achats.bon-achat');
    Route::post('/achats/bon-achat', [BonAchatController::class, 'store'])->name('achats.bon-achat.store');
    Route::put('/achats/bon-achat/{bonAchat}', [BonAchatController::class, 'update'])->name('achats.bon-achat.update');
    Route::post('/achats/bon-achat/{bonAchat}/payer', [BonAchatController::class, 'payer'])->name('achats.bon-achat.payer');

    Route::get('/achats/fiche-vehicule', [FicheVehiculeController::class, 'index'])->name('achats.fiche-vehicule');
    Route::post('/achats/fiche-vehicule', [FicheVehiculeController::class, 'store'])->name('achats.fiche-vehicule.store');
    Route::put('/achats/fiche-vehicule/{ficheVehicule}', [FicheVehiculeController::class, 'update'])->name('achats.fiche-vehicule.update');

    Route::get('/parametres/utilisateurs', [UtilisateurController::class, 'index'])->name('parametres.utilisateurs');
    Route::post('/parametres/utilisateurs', [UtilisateurController::class, 'store'])->name('parametres.utilisateurs.store');
    Route::put('/parametres/utilisateurs/{utilisateur}', [UtilisateurController::class, 'update'])->name('parametres.utilisateurs.update');
    Route::post('/parametres/utilisateurs/{utilisateur}/toggle-actif', [UtilisateurController::class, 'toggleActif'])->name('parametres.utilisateurs.toggle');
});
