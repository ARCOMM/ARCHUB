<?php

namespace App\Http\Controllers\Missions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Missions\Mission;
use App\Models\Portal\User;

class MissionMaintainerController extends Controller
{
    public function index(Mission $mission)
    {
        if (!is_null($mission->maintainer)) {
            return $mission->maintainer->username;
        }
    }

    public function store(Request $request, Mission $mission)
    {
        $user = User::where('username', $request->username)->first();

        if ($mission->user->id == $user->id) {
            return $this->destroy($mission);
        }

        $mission->maintainer_id = $user->id;
        $mission->save();
    }

    public function destroy(Mission $mission)
    {
        $mission->maintainer_id = null;
        $mission->save();
    }
}
