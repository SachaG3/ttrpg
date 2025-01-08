<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPlayerTable extends Migration
{
    public function up()
    {
        Schema::create('group_player', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id'); // ID du groupe
            $table->unsignedBigInteger('player_id'); // ID du joueur
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_player');
    }
}
