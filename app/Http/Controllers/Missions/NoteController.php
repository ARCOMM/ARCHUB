<?php

namespace App\Http\Controllers\Missions;

use App\Discord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Missions\MissionNote;
use App\Models\Missions\Mission;

class NoteController extends Controller
{
    /**
     * The note instance.
     *
     * @var App\Models\Missions\MissionNote
     */
    protected $note;

    /**
     * The mission instance.
     *
     * @var App\Models\Missions\Mission
     */
    protected $mission;

    /**
     * Constructor method.
     *
     * @return any
     */
    public function __construct(Mission $mission, MissionNote $note)
    {
        $this->mission = $mission;
        $this->note = $note;

        return $this;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Mission $mission)
    {
        if (!auth()->user()->can('test-missions') && !$mission->isMine()) {
            return redirect($mission->url());
        }

        if (!$request->ajax()) {
            $panel = 'notes';
            return view('missions.show', compact('mission', 'panel'));
        } else {
            return view('missions.show.notes', compact('mission'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Mission $mission)
    {
        if (strlen(trim($request->text)) == 0) {
            abort(403, 'No note text provided');
            return;
        }

        $note = new MissionNote;
        $note->user_id = auth()->user()->id;
        $note->mission_id = $mission->id;
        $note->text = $request->text;
        $note->save();

        $url = "{$note->mission->url()}/notes#note-{$note->id}";
        $content = "**{$note->user->username}** added a note to **{$note->mission->display_name}**";
        Discord::missionUpdate($content, $mission, true, $url);

        return view('missions.notes.item', compact('note'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MissionNote $note)
    {
        if (!$note->isMine()) {
            return;
        }

        $note->delete();
    }
}
