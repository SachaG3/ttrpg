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
        Schema::table('missions', function (Blueprint $table) {
            $table->boolean('start_mission')->default(false)->after('description'); // Ajouter la colonne avec une valeur par défaut
        });
    }

    public function down()
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn('start_mission'); // Supprimer la colonne en cas de rollback
        });
    }
};
