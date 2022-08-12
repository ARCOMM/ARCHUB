<?php

namespace App\Http\Controllers\Missions;

use Illuminate\Http\Request;
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
    }

    public function destroy(Mission $mission)
    {
        $mission->maintainer_id = null;
        $mission->save();
    }
}
