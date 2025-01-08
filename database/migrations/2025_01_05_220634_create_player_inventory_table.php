<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerInventoryTable extends Migration
{
    public function up()
    {
        Schema::create('player_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id'); // ID du joueur
            $table->unsignedBigInteger('item_id'); // ID de l'item
            $table->integer('quantity')->default(1); // Quantité de l'item
            $table->boolean('shared_with_faction')->default(false); // Si l'item est partagé avec la faction
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_inventory');
    }
}

