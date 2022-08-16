<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('join_requests', function (Blueprint $table) {
            $table->string('discord');
            $table->string('email')->nullable()->change();

            $table->dropColumn('apex');
            $table->dropColumn('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('join_requests', function (Blueprint $table) {
            $table->boolean('apex');
            $table->boolean('groups');

            $table->string('email')->nullable(false)->change();
            $table->dropColumn('discord');
        });
    }
};
