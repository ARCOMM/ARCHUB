<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->string('file_name')->nullable()->change();
        });

        Schema::table('mission_notes', function (Blueprint $table) {
            $table->boolean('published');
        });
    }

    public function down()
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->string('file_name')->nullable(false)->change();
        });

        Schema::table('mission_notes', function (Blueprint $table) {
            $table->dropColumn('published');
        });
    }
};
