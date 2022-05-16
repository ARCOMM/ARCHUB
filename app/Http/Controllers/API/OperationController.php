<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Operations\Operation;
use App\Models\Operations\OperationMission;
use \Carbon\Carbon;

class OperationController extends Controller
{
    /**
     * The operation instance.
     *
     * @var App\Models\Operations\Operation
     */
    protected $operation;

    /**
     * The operation mission instance.
     *
     * @var App\Models\Operations\OperationMission
     */
    protected $operation_mission;

    /**
     * Constructor method.
     *
     * @return any
     */
    public function __construct(Operation $operation, OperationMission $operation_mission)
    {
        $this->operation = $operation;
        $this->operation_mission = $operation_mission;

        return $this;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = $this->operation->all()->map(function ($item) {
            $item->items = $item->missions->map(function ($item) {
                $item->mission = $item->mission;
                return $item;
            });

            return $item;
        });

        return response()->json($list);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get latest operation date
        $latest = $this->operation->orderBy('starts_at', 'desc')->first();

        if ($latest) {
            $op = $this->operation->create(['starts_at' => $latest->starts_at->addWeek()]);
            $op->items = [];
            return response()->json($op);
        } else {
            $op = $this->operation->create([
                'starts_at' => Carbon::now()
                    ->endOfWeek()
                    ->subDay()
                    ->hour(config('operation.hour'))
                    ->minute(config('operation.minute'))
                    ->second(0)
            ]);

            $op->items = [];

            return response()->json($op);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->operation->destroy($id);
    }
}
