<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactionsTable extends Migration
{
    public function up()
    {
        Schema::create('factions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de la faction
            $table->integer('score')->default(0); // Score de la faction
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('factions');
    }
}
