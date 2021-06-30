<?php

namespace App\Http\Controllers\Missions;

use App\Http\Controllers\Controller;
use App\Models\Missions\Mission;

use Illuminate\Http\Request;

class BriefingController extends Controller
{
    /**
     * Shows the given mission's briefing.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Mission $mission, int $faction)
    {
        return view('missions.briefing', compact('mission', 'faction'));
    }

    /**
     * Locks/unlocks the given briefing faction for the given mission.
     *
     * @return void
     */
    public function setLock(Request $request, Mission $mission, int $faction)
    {
        if (!$mission->isMine() && !auth()->user()->can('manage-missions')) {
            abort(403, 'You are not authorised to edit this mission');
            return;
        }

        $mission->lockBriefing($faction, $request->locked);
    }
}
