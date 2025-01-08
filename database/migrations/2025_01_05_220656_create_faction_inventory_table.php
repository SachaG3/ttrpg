<?php

// database/migrations/xxxx_xx_xx_create_faction_inventory_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactionInventoryTable extends Migration
{
    public function up()
    {
        Schema::create('faction_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faction_id'); // ID de la faction
            $table->unsignedBigInteger('item_id'); // ID de l'item
            $table->integer('quantity')->default(1); // Quantité de l'item
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('faction_inventory');
    }
}
