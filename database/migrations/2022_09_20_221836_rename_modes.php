<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('missions', function (Blueprint $table) {
            DB::statement("ALTER TABLE missions MODIFY COLUMN mode ENUM('coop', 'adversarial', 'arcade', 'ade', 'tvt') DEFAULT 'coop'");
            DB::statement("UPDATE missions SET mode = 'ade' WHERE mode = 'arcade'");
            DB::statement("UPDATE missions SET mode = 'tvt' WHERE mode = 'adversarial'");
            DB::statement("ALTER TABLE missions MODIFY COLUMN mode ENUM('coop', 'tvt', 'ade') DEFAULT 'coop'");
        });
    }

    public function down()
    {
            //
    }
};
