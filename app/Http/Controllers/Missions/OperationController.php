<?php

namespace App\Http\Controllers\Missions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Operations\Operation;
use App\Models\Operations\OperationMission;
use \Carbon\Carbon;

class OperationController extends Controller
{
    /**
     * Shows the operations page.
     *
     * @return any
     */
    public function index()
    {
        return view('missions.operations');
    }

    /**
     * Deletes the given operation item.
     *
     * @return void
     */
    public function removeMission(Request $request)
    {
        if ($request->id == -1) {
            return;
        }
        OperationMission::destroy($request->id);
    }

    /**
     * Creates an operation item for the given mission and operation.
     *
     * @return integer
     */
    public function addMission(Request $request)
    {
        $operation = Operation::findOrFail($request->operation_id);

        $exists = OperationMission::where('operation_id', $operation->id)
            ->where('play_order', $request->play_order)
            ->first();

        if ($exists) {
            return;
        }

        $item = OperationMission::create([
            'operation_id' => $operation->id,
            'mission_id' => $request->mission_id,
            'play_order' => $request->play_order
        ]);

        return $item->id;
    }

    /**
     * Creates an operation header.
     * Returns the operation item view.
     *
     * @return any
     */
    public function create(Request $request, Operation $operation)
    {
        // Get latest operation date
        $latest = $operation->orderBy('starts_at', 'desc')->first();
        $header = null;

        if ($latest) {
            $nextDate = $latest->starts_at->addWeek();
            $nextDate->hour = config('operation.hour');
            $header = $operation->create(['starts_at' => $nextDate]);
        } else {
            $header = $operation->create([
                'starts_at' => Carbon::now()
                    ->endOfWeek()
                    ->subDay()
                    ->hour(config('operation.hour'))
                    ->minute(config('operation.minute'))
                    ->second(0)
            ]);
        }

        if (isset($request->starts_at) && strlen($request->starts_at) != 0) {
            $header->starts_at = Carbon::parse($request->starts_at);
            $header->save();
        }

        return view('missions.operations.item', ['operation' => $header]);
    }

    /**
     * Destroys the given operation record.
     *
     * @return any
     */
    public function destroy(Request $request, Operation $operation)
    {
        $operation->destroy($request->operation_id);
    }
}
