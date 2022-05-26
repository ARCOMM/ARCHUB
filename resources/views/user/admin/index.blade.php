@extends('layout')

@section('title')
    Users
@endsection

@section('header-color')
    primary
@endsection

@section('content')
    <div class="container" id="users-content">
        @if ($users->isNotEmpty())
            <h5 class="my-2">{{ $users->count() }} Users</h5>

            <div style="margin-bottom: 5rem" class="card">
                <div class="list-group">
                    @foreach ($users as $user)
                        <li class="list-group-item list-group-item-action">
                            <img src="{{ $user->avatar }}" class="rounded-circle me-2" width="32">
                            {{ $user->username }}
                        </li>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
