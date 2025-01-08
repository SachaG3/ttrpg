<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlayersTableAddStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('firstname')->after('id'); // Ajout du prénom après l'id
            $table->integer('force')->default(0)->after('firstname');
            $table->integer('dexterity')->default(0)->after('force');
            $table->integer('wisdom')->default(0)->after('dexterity');
            $table->integer('intelligence')->default(0)->after('wisdom');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['firstname', 'force', 'dexterity', 'wisdom', 'intelligence']);
        });
    }
}
