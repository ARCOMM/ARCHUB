<?php

namespace App\Models\Portal;

use Auth;
use Carbon\Carbon;
use App\Discord;
use App\Models\Missions\Mission;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discord_id',
        'username',
        'email',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Media library image conversions.
     *
     * @return void
     */
    public function registerMediaConversions(Media $media = null): void {}

    public function hasARole(int ...$roles)
    {
        return Discord::hasARole($this, ...$roles);
    }

    /**
     * Gets the user's missions.
     * Ordered from latest to oldest.
     *
     * @return Collection App\Models\Missions\Mission
     */
    public function missions()
    {
        return Mission::where('user_id', $this->id)->orderBy('created_at', 'desc')->get();
    }
}
