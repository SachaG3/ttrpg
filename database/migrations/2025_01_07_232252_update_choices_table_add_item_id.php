<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('choices', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('next_mission_id'); // Lien avec un item

            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null'); // Contrainte de clé étrangère
        });
    }

    public function down()
    {
        Schema::table('choices', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
        });
    }
};
