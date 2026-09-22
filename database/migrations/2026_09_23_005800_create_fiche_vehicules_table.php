<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiche_vehicules', function (Blueprint $table) {
            $table->id();
            $table->date('date_achat');
            $table->string('nom_proprietaire');
            $table->string('marque');
            $table->string('modele');
            $table->unsignedInteger('kilometrage')->default(0);
            $table->string('couleur')->nullable();
            $table->decimal('montant_achat', 12, 2);
            $table->string('photo_1')->nullable();
            $table->string('photo_2')->nullable();
            $table->string('photo_3')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiche_vehicules');
    }
};
