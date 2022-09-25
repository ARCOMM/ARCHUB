<?php

use App\Models\Missions\Mission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::create('mission_briefings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Mission::class);
            $table->string('name');
            $table->boolean('locked')->default(false);
            $table->json('sections');

            $table->unique(['mission_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mission_briefings');
    }
};
