<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bon_achat_paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_achat_id')->constrained('bon_achats')->cascadeOnDelete();
            $table->date('date_paiement');
            $table->decimal('montant', 12, 2);
            $table->string('mode_paiement', 50);
            $table->string('reference')->nullable();
            $table->string('nom_tire')->nullable();
            $table->date('date_decaissement')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_achat_paiements');
    }
};
