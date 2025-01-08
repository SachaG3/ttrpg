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
        Schema::table('players', function (Blueprint $table) {
            $table->unsignedBigInteger('game_id')->nullable()->after('id'); // ID de la partie
            $table->unsignedTinyInteger('role')->default(0)->after('game_id'); // Rôle (0 = user, 1 = admin)

            $table->foreign('game_id')->references('id')->on('games')->onDelete('set null'); // Clé étrangère
        });
    }

    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropColumn(['game_id', 'role']);
        });
    }
};
