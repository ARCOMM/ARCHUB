<?php

namespace App\Http\Controllers\Missions;

use Illuminate\Http\Request;
use App\Discord;
use App\Http\Controllers\Controller;
use App\Models\Missions\Mission;

class MissionMaintainerController extends Controller
{
    public function index(Mission $mission)
    {
        return $mission->maintainer;
    }

    public function store(Request $request, Mission $mission)
    {
        if ($mission->user->id == $request->user_id) {
            return $this->destroy($mission);
        }

        $mission->maintainer_id = $request->user_id;
        $mission->save();

        $message = "<@{$mission->maintainer->discord_id}> is now maintaining **{$mission->display_name}** <@{$mission->user->discord_id}>";
        Discord::missionUpdate($message, $mission, false, false, $mission->url());
    }

    public function destroy(Mission $mission)
    {
        $mission->maintainer_id = null;
        $mission->save();
    }
}
