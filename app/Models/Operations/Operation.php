<?php

namespace App\Models\Operations;

use \Carbon\Carbon;
use App\Models\Portal\User;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'starts_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'starts_at'
    ];

    /**
     * Gets the number of seconds away from the operation start time.
     *
     * @return numeric
     */
    public function startsIn()
    {
        $now = Carbon::now();
        return $this->starts_at->diffForHumans();
    }

    /**
     * Gets the latest operation.
     * Will be the operation to next run.
     *
     * @return App\Models\Operations\Operation
     */
    public static function latest()
    {
        $target = Carbon::now()->addDay();
        return Operation::whereRaw('DATE_ADD(starts_at, INTERVAL 2 DAY) > "' . $target->toDateTimeString() . '"')->first();
    }

    /**
     * Gets next week's operation.
     *
     * @return App\Models\Operations\Operation
     */
    public static function nextWeek()
    {
        return Operation::where('starts_at', '>', Carbon::now()->toDateTimeString())->orderBy('starts_at', 'asc')->first();
    }

    /**
     * Gets last week's operation.
     *
     * @return App\Models\Operations\Operation
     */
    public static function lastWeek()
    {
        return Operation::where('starts_at', '<', Carbon::now()->toDateTimeString())->orderBy('starts_at', 'desc')->first();
    }

    /**
     * Gets all future operations.
     *
     * @return Collection App\Models\Operations\Operation
     */
    public static function future()
    {
        return Operation::where('starts_at', '>', Carbon::now()->toDateTimeString())->orderBy('starts_at', 'asc')->get();
    }

    /**
     * Gets all missions for the operation in ascending order.
     *
     * @return Collection App\Models\Operations\OperationMission
     */
    public function missions()
    {
        return $this->hasMany('App\Models\Operations\OperationMission')->orderBy('play_order');
    }

    /**
     * Gets all missions for the operation in ascending order.
     * Resolved to the actual mission model.
     *
     * @return Collection App\Models\Missions\Mission
     */
    public function missionsResolved()
    {
        return $this->missions->map(function ($mission) {
            return $mission->mission;
        });
    }

    /**
     * Checks whether the operation is the next one to run.
     *
     * @return boolean
     */
    public function isNextToRun()
    {
        return $this == $this->nextWeek();
    }
}
