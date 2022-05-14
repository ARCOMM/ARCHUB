<?php

namespace App\Http\Controllers\Missions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tags\Tag;
use App\Models\Tags\MissionTag;
use App\Models\Missions\Mission;

class MissionTagController extends Controller
{
    public function allTags(Request $request)
    {
        return Tag::orderBy('name', 'ASC')->get()
        ->all();
    }

    public function index(Request $request, Mission $mission)
    {
        return MissionTag::where('mission_id', $mission->id)->get()
        ->pluck('tag_id')
        ->toArray();
    }

    public function store(Request $request, Mission $mission)
    {
        $tag = Tag::firstOrCreate(['name' => $request->tag]);

        MissionTag::firstOrCreate([
            'mission_id' => $mission->id, 
            'tag_id' => $tag->id
        ]);
    }

    public function destroy(Request $request, Mission $mission)
    {
        $tag = Tag::where('name', $request->tag)->first();

        MissionTag::where('mission_id', $mission->id)->where('tag_id', $tag->id)
        ->delete();

        if ($request->user()->can('manage-tags')) {
            $tagUsed = MissionTag::where('tag_id', $tag->id)->first();
            if (is_null($tagUsed)) {
                $tag->delete();
            }
        }
    }

    public function search(Request $request)
    {
        $activeTags = json_decode($request->tags, true);
        if (!$activeTags) {
            return view('missions.search');
        }

        $activeTagNames = array();
        foreach ($activeTags as $tag) {
            array_push($activeTagNames, $tag["text"]);
        }

        $tags = Tag::whereIn('name', $activeTagNames)->get()
        ->pluck('id')
        ->toArray();

        $results = MissionTag::selectRaw('mission_id, count(*) as total')
        ->whereIn('tag_id', $tags)
        ->groupBy('mission_id')
        ->get()
        ->where('total', count($tags)) // Only get results which match *all* tags
        ->pluck('mission_id')
        ->toArray();
        
        return view('missions.search', compact('results'));
    }
}
