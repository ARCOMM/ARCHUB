<?php

namespace App\Http\Controllers\Auth;

use App\Models\Portal\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;

class SteamController extends Controller
{
    /**
     * Redirect the user to the Discord login page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect()
    {
        return Socialite::driver('steam')->redirect();
    }

    /**
     * Handles the response from Discord.
     *
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        $user = Socialite::driver('steam')->user();

        return $this->login($user);
    }

    public function login($data)
    {
        $user = User::where('steam_id', $data->id)->first();

        if (is_null($user)) {
            return [
                'error' => 'Account not found.'
            ];
        }

        auth()->login($user, true);
        
        if (is_null($user->discord_id)) {
            return redirect('/auth/redirect');
        }
        return redirect('/hub');
    }
}
