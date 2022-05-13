<?php

namespace App\Models\Tags;

use Illuminate\Database\Eloquent\Model;

class MissionTag extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    public function mission()
    {
        return $this->belongsTo('App\Models\Missions\Mission');
    }

    public function tag()
    {
        return $this->belongsTo('App\Models\Tags\Tag');
    }
}