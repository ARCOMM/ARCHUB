<?php

use App\Models\Missions\Mission;
use App\Models\Operations\Operation;
use App\Models\Operations\OperationMission;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('check-missions', function () {
    $missions = Mission::all();

    foreach ($missions as $mission) {
        $item = OperationMission::where('mission_id', $mission->id)->orderBy('created_at', 'desc')->first();

        if (!$item) {
            $this->comment("Never slotted [{$mission->id}] {$mission->display_name}");
            continue;
        }

        $header = $item->operation;
        $now = Carbon::now();

        if (!$header->starts_at->lt($now)) {
            $this->comment("Currently slotted [{$mission->id}] {$mission->display_name}");
            continue;
        }

        $wasPlayed = !is_null($mission->last_played);
        if (!$wasPlayed || ($wasPlayed && ($mission->last_played < $header->starts_at))) {
            $prev = $wasPlayed ? $mission->last_played->toDateTimeString() : "null";
            $mission->last_played = $header->starts_at;
            $mission->save();
            $this->comment(
                "Updated [{$mission->id}] {$mission->display_name}" .
                " from {$prev} to {$mission->last_played->toDateTimeString()}"
            );
        }
    }
})->describe('Handles the last played timestamp for all missions');
