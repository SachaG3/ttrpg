<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom ou pseudonyme du joueur
            $table->unsignedBigInteger('faction_id')->nullable(); // Lien avec la faction
            $table->boolean('is_spy')->default(false); // Indique si le joueur est un espion
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
}
