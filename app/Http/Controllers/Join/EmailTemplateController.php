<?php

namespace App\Http\Controllers\Join;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JoinRequests\EmailTemplate;

class EmailTemplateController extends Controller
{
    /**
     * Email templates model.
     *
     * @var App\Models\JoinRequests\EmailTemplate
     */
    protected $emails;

    /**
     * Constructor method.
     *
     * @return any
     */
    public function __construct(EmailTemplate $emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emails = $this->emails->all();

        return view('join.admin.emails', compact('emails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('join.admin.emails.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->emails->create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(EmailTemplate $email)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailTemplate $email)
    {
        return view('join.admin.emails.edit', compact('email'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailTemplate $email)
    {
        $email->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailTemplate $email)
    {
        if ($email->locked) {
            return;
        }

        $email->delete();
    }
}
