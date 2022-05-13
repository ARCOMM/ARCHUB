<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Discord;
use App\Http\Controllers\Controller;
use App\Models\Portal\User;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('user.settings.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Update user's username
        if (strlen($request->username) != 0) {
            auth()->user()->username = $request->username;
            auth()->user()->save();
        }
    }

    public function avatarSync()
    {
        $user = auth()->user();
        $user->avatar = Discord::getAvatar($user->discord_id);
        $user->save();

        return $user->avatar;
    }
}
