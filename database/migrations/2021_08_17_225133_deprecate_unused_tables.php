<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeprecateUnusedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('staff_proxies');
        Schema::dropIfExists('references');
        Schema::dropIfExists('permission_users');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('mentions');
        Schema::dropIfExists('comments');
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
