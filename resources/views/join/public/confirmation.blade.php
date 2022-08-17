@extends('layouts.public')

@section('title')
    Join
@endsection

@section('content')
    <div class="content container">
        <h3>Your application has been sent</h3>
        <p>Please join <a href="{{ config('services.discord.invite_link') }}" target="_newtab">our Discord server</a> so that we can contact you about your application</p>
    </div>
@endsection
