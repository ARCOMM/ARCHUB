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

    public function modes(Request $request)
    {
        return array_values(Mission::$gamemodes);
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
        if (empty($request->all())) {
            return view('missions.search');
        }

        $mode = $request->query('mode');
        $author_id = $request->query('author_id');
        $whitelist = $request->query('whitelist');
        $blacklist = $request->query('blacklist');

        $results = Mission::
        when($whitelist || $blacklist, function ($query, $a) {
            return $query->leftJoin('mission_tags', 'missions.id', '=', 'mission_tags.mission_id');
        })
        ->selectRaw('missions.id')
        ->when($whitelist, function ($query, $whitelist) {
            $white = Tag::whereIn('name', $whitelist)->get()->pluck('id')->toArray();
            $placeholders = implode(",", array_fill(0, count($white), '?'));
            return $query->selectRaw("count(CASE WHEN (tag_id IN ($placeholders)) THEN 1 END) as whitelisted", $white);
        })
        ->when($blacklist, function ($query, $blacklist) {
            $black = Tag::whereIn('name', $blacklist)->get()->pluck('id')->toArray();
            $placeholders = implode(",", array_fill(0, count($black), '?'));
            return $query->selectRaw("count(CASE WHEN (tag_id IN ($placeholders)) THEN 1 END) as blacklisted", $black);
        })
        ->when($mode, function ($query, $mode) {
            return $query->where('mode', $mode);
        })
        ->when($author_id, function ($query, $author_id) {
            return $query->where('user_id', $author_id);
        })
        ->when($whitelist || $blacklist, function ($query, $a) {
            return $query->groupBy('id');
        })
        ->when($whitelist, function ($query, $whitelist) {
            // Only get results which match *all* tags;
            return $query->having('whitelisted', count($whitelist));
        })
        ->when($blacklist, function($query, $a) {
            return $query->having('blacklisted', 0);
        })
        ->get()
        ->pluck('id')
        ->toArray();
        
        return view('missions.search', compact('results'));
    }
}
