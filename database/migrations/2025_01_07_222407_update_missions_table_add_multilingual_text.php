<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMissionsTableAddMultilingualText extends Migration
{
    public function up()
    {
        Schema::table('missions', function (Blueprint $table) {
            // Modifier les colonnes existantes pour qu'elles soient de type JSON
            $table->json('title')->nullable()->change();
            $table->json('description')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('missions', function (Blueprint $table) {
            // Revenir au type string ou text si nécessaire
            $table->string('title')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
}
