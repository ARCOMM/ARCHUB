<?php

namespace App;

use App\ChannelEnum;
use App\RoleEnum;
use App\Models\Missions\Mission;
use App\Models\Missions\MissionSubscription;
use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Discord
{
    public static function missionUpdate(string $content, Mission $mission, bool $tagAuthor = false, bool $tagSubscribers = false, string $url = null)
    {
        if ($tagAuthor && (!$mission->isMine())) {
            $user = $mission->hasMaintainer() ? $mission->maintainer : $mission->user;
            $content = "{$content} <@{$user->discord_id}>";
        }

        if ($tagSubscribers) {
            foreach (MissionSubscription::where("mission_id", $mission->id)->cursor() as $subscriber) {
                if ($subscriber->discord_id != auth()->user()->discord_id) {
                    $content = "{$content} <@{$subscriber->discord_id}>";
                }
            }
        }

        if (!is_null($url)) {
            $content = "{$content}\n{$url}";
        }

        self::notifyChannel(ChannelEnum::ARCHUB, $content);
    }

    public static function notifyChannel(ChannelEnum $channel, string $content)
    {
        $response = HTTP::post($channel->id(), [
            'content' => $content,
        ]);

        return $response;
    }

    private static function getUser(int $discord_id)
    {
        return Cache::remember($discord_id, 10, function () use ($discord_id) {
            $url = "https://discord.com/api/v8/guilds/" .
            config('services.discord.server_id') . "/members/{$discord_id}";

            $response = HTTP::withHeaders([
                'Authorization' => "Bot ".config('services.discord.token')
            ])->get($url);
            
            if ($response->successful()) {
                return (array)$response->json();
            }

            throw new AuthorizationException("Error getting user from discord ". $response->status());
        });
    }

    public static function getAvatar(int $discord_id)
    {
        $avatarHash = ((array)self::getUser($discord_id)["user"])["avatar"];
        return "https://cdn.discordapp.com/avatars/{$discord_id}/{$avatarHash}.jpg";
    }

    private static function getRoles(int $discord_id)
    {
        return self::getUser($discord_id)["roles"];
    }

    public static function hasARole(int $discord_id, RoleEnum ...$roles)
    {
        $usersRoles = self::getRoles($discord_id);
        foreach ($roles as $role) {
            if (in_array($role->id(), $usersRoles)) {
                return true;
            }
        }
        return false;
    }
}
