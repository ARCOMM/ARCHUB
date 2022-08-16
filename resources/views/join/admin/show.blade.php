@extends('layout')

@section('title')
    {{ $jr->name }} &middot; Applications
@endsection

@section('header-color')
    primary
@endsection

@section('head')
@endsection

@section('content')
    <div class="container">
        <div class="card application-card p-3 mb-3">
            @can('manage-applications')
                <div id="status" class="float-end me-3">
                    @include('join.admin.status', [
                        'joinStatuses' => $joinStatuses,
                        'jr' => $jr
                    ])
                </div>
            @endcan

            <h2>{{ $jr->name }}</h2>

            <table class="table table-sm no-border float-start" style="margin: 15px 0 30px 0">
                <tr>
                    <td width="50%">
                        @if (!is_null($jr->email))
                            <i class="jr-icon fa fa-envelope"></i>
                            {{ $jr->email }}
                        @else
                            <i class="jr-icon fa-brands fa-discord"></i>
                            {{ $jr->discord }}
                        @endif
                    </td>

                    <td width="50%" class="{{ ($jr->age) ? 'text-success' : 'text-danger' }}">
                        <i class="jr-icon fa-regular fa-calendar-days"></i>
                        {{ $jr->age }}
                    </td>
                </tr>

                <tr>
                    <td>
                        <i class="jr-icon fa-solid fa-location-dot"></i>
                        {{ $jr->location }}
                    </td>

                    <td>
                        <i class="jr-icon fa-brands fa-steam-square"></i>
                        <a href="{{ $jr->steam }}" target="_newtab">Steam Account</a>
                    </td>
                </tr>

                <tr>
                    <td class="{{ ($jr->available) ? 'text-success' : 'text-danger' }}">
                        <i class="jr-icon fa-regular fa-clock"></i>
                        {{ ($jr->available) ? 'Available ' : 'Unavailable ' }} for operations
                    </td>

                    <td>
                        <i class="jr-icon fa-solid fa-earth-americas"></i>
                        {{ $jr->source->name }}
                        @if (strlen($jr->source_text))
                            ({{ $jr->source_text }})
                        @endif
                    </td>
                </tr>
            </table>

            <h5 class="mt-3">Game Experience</h5>
            <p>{!! $jr->experience !!}</p>

            <h5 class="mt-3">About</h5>
            <p class="mb-0">{!! $jr->bio !!}</p>
        </div>
    </div>
@endsection
