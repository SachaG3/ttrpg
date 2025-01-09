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
            $table->unsignedBigInteger('hero_id')->nullable()->after('mission_id');
            $table->foreign('hero_id')->references('id')->on('players')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['hero_id']);
            $table->dropColumn('hero_id');
        });
    }
};
