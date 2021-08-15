<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePreopToArcade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('missions', function (Blueprint $table) {
            DB::statement("ALTER TABLE missions MODIFY COLUMN mode ENUM('coop', 'adversarial', 'preop', 'arcade') DEFAULT 'coop'");
            DB::statement("UPDATE missions SET mode = 'arcade' WHERE mode = 'preop'");
            DB::statement("ALTER TABLE missions MODIFY COLUMN mode ENUM('coop', 'adversarial', 'arcade') DEFAULT 'coop'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            //
    }
}
