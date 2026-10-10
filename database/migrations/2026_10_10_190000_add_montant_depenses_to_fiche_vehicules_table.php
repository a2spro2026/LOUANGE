<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_vehicules', function (Blueprint $table) {
            $table->decimal('montant_depenses', 12, 2)->nullable()->after('montant_achat');
        });
    }

    public function down(): void
    {
        Schema::table('fiche_vehicules', function (Blueprint $table) {
            $table->dropColumn('montant_depenses');
        });
    }
};
