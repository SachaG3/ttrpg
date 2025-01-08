<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // ID de l'item
            $table->json('name'); // Nom de l'item (multilingue)
            $table->json('description')->nullable(); // Description multilingue de l'item (JSON)
            $table->json('attributes')->nullable(); // Attributs optionnels (e.g., force, dexterity)
            $table->timestamps(); // Timestamps pour created_at et updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
