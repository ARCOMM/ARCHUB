<?php

namespace App;

use App\ChannelEnum;
use App\RoleEnum;
use App\Models\Portal\User;
use App\Models\Missions\Mission;
use App\Models\Missions\MissionSubscription;
use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Discord
{
    public static function missionUpdate(string $content, Mission $mission, bool $tagAuthor = false, bool $tagSubscribers = false, string $url = null)
    {
        if ($tagAuthor && ($mission->user->id != auth()->user()->id)) {
            $content = "{$content} <@{$mission->user->discord_id}>";
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

        self::notifyChannel(ChannelEnum::Archub, $content);
    }

    public static function notifyChannel(string $channel, string $content)
    {
        $webhook = self::getWebhookFromChannel($channel);
        $response = HTTP::post($webhook, [
            'content' => $content,
        ]);

        return $response;
    }

    private static function getWebhookFromChannel(int $channel)
    {
        if ($channel == ChannelEnum::Archub) 
        {
            return config('services.discord.archub_webhook');
        }
        else if ($channel == ChannelEnum::Staff)
        {
            return config('services.discord.staff_webhook');
        }
        else
        {
            throw new Exception("Webhook not found");
        }
    }

    private static function getUser(int $discord_id)
    {
        return Cache::remember($discord_id, 10, function() use ($discord_id) {
            $url = "https://discord.com/api/v8/guilds/".config('services.discord.server_id')."/members/{$discord_id}";
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

    public static function hasARole(User $user, int ...$roles)
    {
        if (!auth()->guest() && is_null(auth()->user()->discord_id)) {
            throw new AuthorizationException;
        }

        $usersRoles = self::getRoles($user->discord_id);
        foreach ($roles as $role) {
            $roleId = self::getRoleIdFromRole($role);

            if (in_array($roleId, $usersRoles)) {
                return true;
            }
        }

        return false;
    }

    public static function isMember(int $discord_id)
    {
        $roleId = self::getRoleIdFromRole(RoleEnum::Member);
        $roles = self::getRoles($discord_id);

        return in_array($roleId, $roles);
    }

    private static function getRoleIdFromRole(int $role)
    {
        if ($role == RoleEnum::Recruit)
        {
            return config('services.discord.recruit_role');
        }
        else if ($role == RoleEnum::Member)
        {
            return config('services.discord.member_role');
        }
        else if ($role == RoleEnum::Retired)
        {
            return config('services.discord.retired_role');
        }
        else if ($role == RoleEnum::Tester)
        {
            return config('services.discord.tester_role');
        }
        else if ($role == RoleEnum::SeniorTester)
        {
            return config('services.discord.senior_tester_role');
        }
        else if ($role == RoleEnum::Operations)
        {
            return config('services.discord.operations_role');
        }
        else if ($role == RoleEnum::Recruiter)
        {
            return config('services.discord.recruiter_role');
        }
        else if ($role == RoleEnum::Staff) 
        {
            return config('services.discord.staff_role');
        }
        else if ($role == RoleEnum::Admin)
        {
            return config('services.discord.admin_role');
        }
        else
        {
            throw new Exception("RoleId not found");
        }
    }
}
