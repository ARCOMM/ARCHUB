<?php

namespace App\Models\Missions;

use Illuminate\Database\Eloquent\Model;
use App\Support\BelongsToUser;
use App\Support\BelongsToMission;

class MissionRevision extends Model
{
    use BelongsToUser,
        BelongsToMission;

    protected $fillable = [
        'mission_id',
        'user_id',
    ];
}
