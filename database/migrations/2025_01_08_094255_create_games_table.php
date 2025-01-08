<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id(); // ID de la partie
            $table->string('name'); // Nom de la partie
            $table->timestamp('date_start')->nullable(); // Date de début
            $table->timestamp('date_end')->nullable(); // Date de fin
            $table->timestamps(); // created_at et updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
};
