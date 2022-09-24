<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mission_notes', function (Blueprint $table) {
            DB::statement("UPDATE mission_notes SET published = true");
            $table->boolean('published')->default(true)->nullable(false)->change();
        });
    }

    public function down()
    {
    }
};
