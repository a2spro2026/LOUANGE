<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiche_vehicules', function (Blueprint $table) {
            $table->date('date_achat')->nullable()->change();
            $table->string('nom_proprietaire')->nullable()->change();
            $table->decimal('montant_achat', 12, 2)->nullable()->change();

            $table->unsignedSmallInteger('annee')->nullable()->after('kilometrage');
            $table->unsignedSmallInteger('annee_douane')->nullable()->after('annee');
            $table->string('carburant', 20)->nullable()->after('annee_douane');
            $table->string('transmission', 20)->nullable()->after('carburant');
            $table->unsignedTinyInteger('puissance_fiscale')->nullable()->after('transmission');

            $table->string('photo_4')->nullable()->after('photo_3');
            $table->string('photo_5')->nullable()->after('photo_4');
            $table->string('photo_6')->nullable()->after('photo_5');
            $table->string('photo_7')->nullable()->after('photo_6');

            $table->dropColumn(['titre', 'description', 'en_catalogue']);
        });
    }

    public function down(): void
    {
        Schema::table('fiche_vehicules', function (Blueprint $table) {
            $table->dropColumn([
                'annee', 'annee_douane', 'carburant', 'transmission', 'puissance_fiscale',
                'photo_4', 'photo_5', 'photo_6', 'photo_7',
            ]);
            $table->string('titre')->nullable();
            $table->text('description')->nullable();
            $table->boolean('en_catalogue')->default(false);
        });
    }
};
