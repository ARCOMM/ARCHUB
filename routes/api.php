<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Portal\User;
use App\Models\Missions\Mission;
use App\Models\Missions\MissionSubscription;
use App\Models\Operations\Operation;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->post('missions/{mission}/subscribe', function (Request $request, Mission $mission) {
    $discord_id = $request->query('discord_id');
    // Check that a user exists with the given ID
    $user = User::where('discord_id', $discord_id)->firstOrFail();
    $existing = MissionSubscription::where('mission_id', $mission->id)->where('discord_id', $discord_id)->first();

    if (is_null($existing)) {
        return MissionSubscription::create([
            "mission_id" => $mission->id,
            "discord_id" => $discord_id
        ]);
    }

    $existing->delete();
    return response()->noContent(204);
});

Route::middleware('auth:sanctum')->get('operations/next', function (Request $request) {
    $operation = Operation::nextWeek();

    if (!is_null($operation)) {
        $opMissions = $operation->missions()->get();
        $missions = [];
        
        foreach ($opMissions as $opMission) {
            $mission = $opMission->mission()->select('id', 'user_id', 'display_name', 'mode', 'summary')->first();
            $maker = User::where('id', $mission->user_id)->select('username')->first();

            $mission['maker'] = $maker['username'];
            unset($mission['user_id']);

            array_push($missions, $mission);
        }
        
        return $missions;
    }

    return response()->noContent(204);
});
