<?php

namespace App\Models\Missions;

use Illuminate\Database\Eloquent\Model;

class MissionBriefing extends Model
{
    protected $fillable = [
        'mission_id',
        'name',
        'locked',
        'sections',
    ];

    protected $casts = [
        'sections' => 'array',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}
