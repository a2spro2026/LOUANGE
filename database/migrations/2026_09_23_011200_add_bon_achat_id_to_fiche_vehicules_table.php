<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_vehicules', function (Blueprint $table) {
            $table->foreignId('bon_achat_id')
                ->nullable()
                ->after('id')
                ->constrained('bon_achats')
                ->nullOnDelete();
            $table->unique('bon_achat_id');
        });
    }

    public function down(): void
    {
        Schema::table('fiche_vehicules', function (Blueprint $table) {
            $table->dropUnique(['bon_achat_id']);
            $table->dropConstrainedForeignId('bon_achat_id');
        });
    }
};
