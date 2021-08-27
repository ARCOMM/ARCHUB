<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeprecateUnusedRows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mission_revisions', function (Blueprint $table) {
            $table->dropColumn('text');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('access_level');
        });

        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn('published');
            $table->dropColumn('loadout_addons');
            $table->dropColumn('pbo_path');
            $table->dropColumn('version');
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
