<?php

namespace App\Http\Controllers\Missions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tags\Tag;
use App\Models\Tags\MissionTag;
use App\Models\Missions\Mission;
use App\Models\Portal\User;

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
        $author = $request->query('author');
        $activeTags = $request->query('tags');
        if (!$activeTags && !$author) {
            return view('missions.search');
        }

        $results = MissionTag::join('missions', 'mission_tags.mission_id', '=', 'missions.id')
        ->selectRaw('mission_tags.mission_id, missions.user_id, count(*) as total')
        ->when($author, function ($query, $author) {
            $user = User::where('username', $author)->first();
            return $query->where('user_id', $user->id);
        })
        ->when($activeTags, function ($query, $activeTags) {
            $tags = Tag::whereIn('name', $activeTags)->get()
            ->pluck('id')
            ->toArray();

            return $query->whereIn('tag_id', $tags);
        })
        ->groupBy('mission_id')
        ->when($activeTags, function ($query, $activeTags) {
            return $query->having('total', count($activeTags)); // Only get results which match *all* tags;
        })
        ->get()
        ->pluck('mission_id')
        ->toArray();
        
        return view('missions.search', compact('results'));
    }
}
