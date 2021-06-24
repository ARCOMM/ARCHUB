<?php

namespace App\Http\Controllers\Operations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Operations\Operation;
use App\Models\Operations\OperationMission;

class OperationMissionController extends Controller
{
    /**
     * Creates an operation item for the given mission and operation.
     *
     * @return integer
     */
    public function store(Request $request, Operation $operation)
    {
        $exists = OperationMission::where('operation_id', $operation->id)
            ->where('play_order', $request->play_order)
            ->first();

        if ($exists) return;

        $item = OperationMission::create([
            'operation_id' => $operation->id,
            'mission_id' => $request->mission_id,
            'play_order' => $request->play_order
        ]);

        return $item->id;
    }

    /**
     * Deletes the given operation item.
     *
     * @return void
     */
    public function destroy(Request $request)
    {
        if ($request->id == -1) return;
        OperationMission::destroy($request->id);
    }
}
