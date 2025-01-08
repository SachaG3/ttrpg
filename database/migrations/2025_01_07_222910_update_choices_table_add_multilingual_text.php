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
        Schema::table('choices', function (Blueprint $table) {
            $table->json('option_text')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('choices', function (Blueprint $table) {
            $table->dropColumn(['option_text']);
        });
    }
};
