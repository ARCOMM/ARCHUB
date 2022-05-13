<?php

namespace App\Http\Controllers\Missions;

use App\Discord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Missions\Mission;
use App\Models\Missions\MissionComment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Mission $mission)
    {
        $comments = $mission->comments;
        return view('missions.comments.list', compact('comments'));
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
            abort(403, 'No comment text provided');
            return;
        }

        if ($request->id == -1) {
            // Create a new comment
            $comment = new MissionComment();
            $comment->mission_id = $mission->id;
            $comment->user_id = auth()->user()->id;
            $comment->text = $request->text;
            $comment->published = $request->published;
            $comment->save();

            if ($comment->published) {
                static::discordNotify($comment);

                $comment->update([
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            // Update an existing one
            $comment = MissionComment::find($request->id);

            $shouldNotify = !$comment->published && $request->published;

            $comment->text = $request->text;
            $comment->published = $request->published;
            $comment->save();

            if ($shouldNotify) {
                static::discordNotify($comment);

                $comment->update([
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($comment->published) {
            return view('missions.comments.item', compact('comment'));
        } else {
            return $comment->id;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, MissionComment $comment)
    {
        return json_encode([
            'text' => $comment->text
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, MissionComment $comment)
    {
        $comment->delete();
    }

    private static function discordNotify(MissionComment $comment)
    {
        $url = "{$comment->mission->url()}/aar#comment-{$comment->id}";
        $message = "**{$comment->user->username}** commented on **{$comment->mission->display_name}**";
        Discord::missionUpdate($message, $comment->mission, true, false, $url);
    }
}
