<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bon_achats', function (Blueprint $table) {
            $table->string('piece_jointe')->nullable()->after('note');
            $table->string('piece_jointe_nom')->nullable()->after('piece_jointe');
        });
    }

    public function down(): void
    {
        Schema::table('bon_achats', function (Blueprint $table) {
            $table->dropColumn(['piece_jointe', 'piece_jointe_nom']);
        });
    }
};
