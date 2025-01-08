<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChoicesTable extends Migration
{
    public function up()
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mission_id'); // Lien avec la mission
            $table->string('option_text'); // Texte de l'option
            $table->string('consequence_type'); // Statistique impactée (force, dexterity, etc.)
            $table->integer('consequence_value'); // Valeur ajoutée ou enlevée à la statistique
            $table->unsignedBigInteger('next_mission_id')->nullable(); // Mission suivante si choix effectué
            $table->timestamps();

            $table->foreign('mission_id')->references('id')->on('missions')->onDelete('cascade');
            $table->foreign('next_mission_id')->references('id')->on('missions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('choices');
    }
}
