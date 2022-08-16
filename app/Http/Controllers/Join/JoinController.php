<?php

namespace App\Http\Controllers\Join;

use App\Http\Controllers\Controller;
use App\Models\JoinRequests\JoinStatus;
use App\Models\JoinRequests\JoinRequest;

use Illuminate\Http\Request;

class JoinController extends Controller
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
    public function __construct(
        JoinRequest $joinRequests,
        JoinStatus $joinStatuses,
    ) {
        $this->joinRequests = $joinRequests;
        $this->joinStatuses = $joinStatuses;

        return $this;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $status = '')
    {
        if ($status == '') {
            return redirect('/hub/applications/pending');
        }

        $joinRequests = $this->joinRequests->items($status);
        $joinStatuses = $this->joinStatuses->orderBy('position', 'asc')->get();

        return view('join.admin.index', compact('joinRequests', 'joinStatuses'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, JoinRequest $jr)
    {
        $joinStatuses = $this->joinStatuses->orderBy('position', 'asc')->get();

        return view('join.admin.show', compact('jr', 'joinStatuses'));
    }

    /**
     * Shows join request items with the given status and order.
     *
     * @return view
     */
    public function items(Request $request)
    {
        $joinRequests = $this->joinRequests->items(
            $request->get('status', 'pending'),
            $request->get('order', 'desc')
        );

        return view('join.admin.items', compact('joinRequests'));
    }
}
