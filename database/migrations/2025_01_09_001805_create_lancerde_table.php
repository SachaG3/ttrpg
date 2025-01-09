<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLancerdeTable extends Migration
{
    public function up()
    {
        Schema::create('lancerdé', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gamede_id');
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('hero_id');
            $table->unsignedInteger('dice_result');
            $table->timestamps();

            $table->foreign('gamede_id')->references('id')->on('gamedé')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('hero_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lancerdé');
    }
}
