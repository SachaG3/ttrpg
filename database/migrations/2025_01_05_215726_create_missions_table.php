<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionsTable extends Migration
{
    public function up()
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Nom de la mission
            $table->text('description'); // Description de la mission
            $table->string('status')->default('pending'); // État de la mission
            $table->unsignedTinyInteger('assigned_type'); // 1 = Joueur, 2 = Groupe, 3 = Faction
            $table->unsignedBigInteger('assigned_id'); // ID cible (Joueur, Groupe ou Faction)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('missions');
    }
}
