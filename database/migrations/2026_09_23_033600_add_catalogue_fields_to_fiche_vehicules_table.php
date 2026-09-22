<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_vehicules', function (Blueprint $table) {
            $table->string('titre')->nullable()->after('modele');
            $table->text('description')->nullable()->after('titre');
            $table->decimal('montant_vente', 12, 2)->nullable()->after('montant_achat');
            $table->boolean('en_catalogue')->default(false)->after('montant_vente');
        });
    }

    public function down(): void
    {
        Schema::table('fiche_vehicules', function (Blueprint $table) {
            $table->dropColumn(['titre', 'description', 'montant_vente', 'en_catalogue']);
        });
    }
};
