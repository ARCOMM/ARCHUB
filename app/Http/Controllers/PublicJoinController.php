<?php

namespace App\Http\Controllers;

use App\Discord;
use App\ChannelEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\JoinRequestAcknowledged;
use App\Models\JoinRequests\JoinSource;
use App\Models\JoinRequests\JoinRequest;

class PublicJoinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $jrUrl = url('/hub/applications/show/'.$jr->id);

        Discord::notifyChannel(ChannelEnum::Staff, "**{$jr->name}** submitted an application\n{$jrUrl}");

        Mail::to($jr->email)->send(new JoinRequestAcknowledged);

        $email = $jr->email;
        return view('join.public.confirmation', compact('email'));
    }
}
