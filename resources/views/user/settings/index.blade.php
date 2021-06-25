@extends('layout')

@section('title')
    Settings
@endsection

@section('header-color')
    primary
@endsection

@section('content')
    <div class="container" id="settings-content">
        @include('user.settings.account')
    </div>
@endsection
