<?php

namespace App\Http\Controllers\Join;

use App\ChannelEnum;
use App\Discord;
use App\Http\Controllers\Controller;
use App\Mail\JoinRequestAcknowledged;
use App\Models\JoinRequests\JoinRequest;
use App\Models\JoinRequests\JoinSource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PublicJoinController extends Controller
{
    /**
     * Constructor method for PublicJoinController.
     *
     * @return type
     */
    public function __construct()
    {
        // TODO Middleware
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->create();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sources = JoinSource::all();

        return view('join.public.form', compact('sources'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $request->all();

        $this->validate($request, [
            'name' => 'required|max:255',
            'age' => 'required|integer',
            'location' => 'required',
            'discord' => 'required|max:255',
            'steam' => 'required|url',
            'available' => 'required|integer',
            'experience' => 'required',
            'bio' => 'required',
            'source_id' => 'required'
        ]);

        // Create the join request if there are no form errors
        $jr = JoinRequest::create($form);
        $jrUrl = url('/hub/applications/show/'.$jr->id);

        Discord::notifyChannel(ChannelEnum::STAFF, "**{$jr->name}** submitted an application\n{$jrUrl}");

        //Mail::to($jr->email)->send(new JoinRequestAcknowledged);

        return redirect('/join/acknowledged?email=' . $jr->email);
    }

    /**
     * Shows the acknowledged view.
     *
     * @return view
     */
    public function acknowledged(Request $request)
    {
        $email = $request->email;

        return view('join.public.confirmation', compact('email'));
    }
}
