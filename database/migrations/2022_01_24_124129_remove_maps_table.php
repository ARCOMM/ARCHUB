<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->string('map_name');
        });

        DB::statement('UPDATE missions Mi, maps Ma SET Mi.map_name = Ma.class_name WHERE Mi.map_id = Ma.id');
        DB::statement('ALTER TABLE missions DROP FOREIGN KEY missions_map_id_foreign');

        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn('map_id');
        });
        Schema::drop('maps');
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
