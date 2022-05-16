<?php

namespace App\Http\Controllers\Join;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JoinRequests\JoinRequest;
use App\Models\JoinRequests\JoinStatus;

class JoinStatusController extends Controller
{
    /**
     * Join request model.
     *
     * @var App\Models\JoinRequests\JoinRequest
     */
    protected $joinRequests;

    /**
     * Join status model.
     *
     * @var App\Models\JoinRequests\JoinStatus
     */
    protected $joinStatuses;

    /**
     * Constructor method.
     *
     * @return any
     */
    public function __construct(JoinRequest $joinRequests, JoinStatus $joinStatuses)
    {
        $this->joinRequests = $joinRequests;
        $this->joinStatuses = $joinStatuses;

        return $this;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        if (empty($request->text) || strlen($request->text) > 12) {
            return;
        }

        $permalink = Str::slug($request->text, '-');

        if (!$this->joinStatuses->where('permalink', $permalink)->first()) {
            $status = $this->joinStatuses->create([
                'text' => $request->text,
                'permalink' => $permalink
            ]);

            return $status->id;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(JoinRequest $jr)
    {
        $joinStatuses = $this->joinStatuses->orderBy('position', 'asc')->get();

        return view('join.admin.status', compact('jr', 'joinStatuses'));
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
    public function update(Request $request, JoinRequest $jr)
    {
        if (!isset($request->join_request_id) || !isset($request->new_status_id)) {
            return;
        }

        $jr->status_id = $request->new_status_id;
        $jr->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
