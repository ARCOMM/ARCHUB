<?php

namespace App\Http\Controllers\Missions;

use App\Discord;
use App\Http\Controllers\Controller;
use App\Models\Missions\Mission;
use App\Models\Missions\MissionRevision;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->exists('panel')) {
            return view('missions.' . $request->panel);
        }

        return view('missions.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($request->hasFile('file')) {
            $details = Mission::getDetailsFromName($request->file->getClientOriginalName());

            $mission = new Mission();
            $mission->user_id = $user->id;
            $mission->file_name = $request->file->getClientOriginalName();
            $mission->display_name = $request->file->getClientOriginalName();
            $mission->summary = '';
            $mission->mode = $details->mode;
            $mission->map_id = $details->map->id;
            $mission->briefings = '';
            $mission->save();

            // Store locally temporarily
            $path = $request->file->storeAs(
                "missions/{$user->id}/{$mission->id}",
                'original.pbo'
            );

            $mission->save();

            // Unpack PBO and store configs in mission record as JSON objects
            $configs = $mission->storeConfigs($path);

            // If errors in configs, return message
            if (get_class($configs) == 'App\Helpers\ArmaConfigError') {
                $mission->delete();
                abort(400, $configs->message);
                return;
            }

            // Delete local temp files
            Storage::deleteDirectory("missions/{$user->id}");

            $content = "**{$mission->user->username}** submitted a mission named **{$mission->display_name}**";
            Discord::missionUpdate($content, $mission, false, false, $mission->url());

            return $mission->url();
        } else {
            abort(400, $request->file->getErrorMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Mission $mission)
    {
        if (!($mission->verified || $mission->existsInOperation() || Gate::allows('test-mission', $mission))) {
            return redirect('/hub/missions?403=1');
        }

        if (!$request->ajax()) {
            return view('missions.show', compact('mission'));
        } else {
            return view('missions.show', compact('mission'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mission $mission)
    {
        $user = auth()->user();

        if ($request->hasFile('file')) {
            $details = Mission::getDetailsFromName($request->file->getClientOriginalName());

            if ($mission) {
                $old_mission = clone $mission;
                $old_mission_displayname = $mission->display_name;
                $old_mission_summary = $mission->summary;

                $old_mission_cloud_pbo_dir = $mission->cloud_pbo;

                Storage::cloud()->move($old_mission_cloud_pbo_dir, "x{$old_mission_cloud_pbo_dir}");

                if ($mission->verified && $mission->user_id == $user->id) {
                    $mission->verified = 0;
                    $mission->verified_by = null;
                }

                $mission->file_name = $request->file->getClientOriginalName();
                $mission->display_name = $request->file->getClientOriginalName();
                $mission->summary = '';
                $mission->mode = $details->mode;
                $mission->map_id = $details->map->id;
                $mission->briefings = '';
                $mission->save();

                // Store locally temporarily
                $path = $request->file->storeAs(
                    "missions/{$user->id}/{$mission->id}",
                    'original.pbo'
                );

                $mission->save();

                // Create revision item
                $revision = MissionRevision::create([
                    'mission_id' => $mission->id,
                    'user_id' => auth()->user()->id
                ]);

                // Unpack PBO and store configs in mission record as JSON objects
                $configs = $mission->storeConfigs($path);

                $path = "missions/{$mission->user_id}/{$mission->id}";

                // If errors in configs, return message
                if (get_class($configs) == 'App\Helpers\ArmaConfigError') {
                    Storage::deleteDirectory($path);

                    Storage::cloud()->move("x{$old_mission_cloud_pbo_dir}", $old_mission_cloud_pbo_dir);

                    // Update the record with the old data
                    $mission->file_name = $old_mission->file_name;
                    $mission->display_name = $old_mission->display_name;
                    $mission->mode = $old_mission->mode;
                    $mission->map_id = $old_mission->map_id;
                    $mission->display_name = $old_mission_displayname;
                    $mission->summary = $old_mission_summary;
                    $mission->briefings = $old_mission->briefings;
                    $mission->weather = $old_mission->weather;
                    if (isset($old_mission->date)) {
                        $mission->date = $old_mission->date;
                    }
                    $mission->time = $old_mission->time;
                    $mission->save();

                    $revision->delete();

                    abort(400, $configs->message);
                    return;
                }

                // Delete local temp files
                Storage::deleteDirectory("missions/{$user->id}");

                // Delete old cloud files
                Storage::cloud()->delete("x{$old_mission_cloud_pbo_dir}");
                
                $content = "**{$revision->user->username}** has updated **{$revision->mission->display_name}**";
                Discord::missionUpdate($content, $revision->mission, false, true);

                return view('missions.show', compact('mission'));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy(Mission $mission)
    {
        if (Gate::allows('delete-mission', $mission)) {
            return redirect('/hub/missions/' . $mission->id);
        }

        $mission->delete();

        return redirect('/hub/missions');
    }

    /**
     * Shows the given mission's briefing.
     *
     * @return \Illuminate\Http\Response
     */
    public function briefing(Request $request)
    {
        $mission = Mission::find($request->mission_id);
        $faction = $request->faction;
        return view('missions.briefing', compact('mission', 'faction'));
    }

    /**
     * Locks/unlocks the given briefing faction for the given mission.
     *
     * @return void
     */
    public function setBriefingLock(Request $request)
    {
        $mission = Mission::find($request->mission_id);

        if (!Gate::allows('manage-mission', $mission)) {
            abort(403, 'You are not authorised to edit this mission');
            return;
        }

        $mission->lockBriefing($request->faction, $request->locked);
    }

    public function orbat(Request $request)
    {
        $mission = Mission::find($request->mission_id);
        $faction = $request->faction;
        return view('missions.orbat', compact('mission', 'faction'));
    }

    /**
     * Updates the mission verification boolean.
     *
     * @return any
     */
    public function updateVerification(Request $request, Mission $mission)
    {
        $mission->verified = !$mission->verified;

        if ($mission->verified) {
            $mission->verified_by = auth()->user()->id;
        } else {
            $mission->verified_by = null;
        }

        $mission->save();

        if ($mission->verified) {
            $content = "**{$mission->verifiedByUser()->username}** has verified **{$mission->display_name}**";
            Discord::missionUpdate($content, $mission, true);
        }

        $updated_by = auth()->user()->username;

        return "Verified by {$updated_by}";
    }

    /**
     * Gets the full download URL for the given mission and format.
     *
     * @return string
     */
    public function download(Mission $mission)
    {
        return $mission->download();
    }

    /**
     * Deploy mission .pbo to arma server
     * Uses https://github.com/Dahlgren/arma-server-web-admin/blob/master/routes/missions.js
     */
    public function deploy(Mission $mission)
    {
        $url = config('services.missions.url');
        $headers = [
            'Authorization' => "Basic " . 
            base64_encode(config('services.missions.user') . ":" . config('services.missions.pass'))
        ];
        $userId = auth()->user()->id;

        // Temp download .pbo locally
        Storage::put("mission_deploy/{$userId}/{$mission->id}/{$mission->file_name}", 
            Storage::cloud()->get($mission->cloud_pbo)
        );

        // Deploy .pbo to arma server
        $response = HTTP::withHeaders($headers)->attach(
            'missions', 
            file_get_contents(storage_path("app/mission_deploy/{$userId}/{$mission->id}/{$mission->file_name}")), 
            $mission->file_name
        )->post($url);

        // Refresh server mission .pbo list
        HTTP::withHeaders($headers)->post("{$url}/refresh");

        // Delete local temp files
        Storage::deleteDirectory("mission_deploy/{$userId}");
    }

    /**
     * Shows the given panel for the given mission.
     *
     * @return any
     */
    public function panel(Request $request, Mission $mission, $panel)
    {
        if (!($mission->verified || $mission->existsInOperation() || Gate::allows('test-mission', $mission))) {
            return redirect('/hub/missions?403=1');
        }

        if (!$request->ajax()) {
            return view('missions.show', compact('mission', 'panel'));
        } else {
            return view('missions.show.' . strtolower($panel), compact('mission'));
        }
    }
}
