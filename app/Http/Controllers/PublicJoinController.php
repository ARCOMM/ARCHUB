<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\JoinRequestAcknowledged;
use App\Models\JoinRequests\JoinSource;
use App\Models\JoinRequests\JoinRequest;
use App\Notifications\JoinRequestReceived;

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
            'age' => 'required|integer|min:' . config('app.min_age'),
            'location' => 'required',
            'email' => 'required|email',
            'steam' => 'required|url',
            'available' => 'required|integer',
            'apex' => 'required|integer',
            'groups' => 'required|integer',
            'experience' => 'required',
            'bio' => 'required',
            'source_id' => 'required'
        ]);

        // Create the join request if there are no form errors
        $jr = JoinRequest::create($form);

        // Discord message
        $jr->notify(new JoinRequestReceived($jr));

        Mail::to($jr->email)->send(new JoinRequestAcknowledged);

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
