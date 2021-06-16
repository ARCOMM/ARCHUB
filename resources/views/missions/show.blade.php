@extends('layout')

@section('title')
    {{ $mission->display_name }}
@endsection

@if ($mission->banner() != '')
    @section('banner')
        '{{ $mission->banner() }}'
    @endsection

    @section('banner-classes')
        banner-fade-top
    @endsection

    @section('header-color')
        transparent
    @endsection
@else
    @section('header-color')
        {{ (['coop' => 'info', 'adversarial' => 'danger', 'arcade' => 'success'])[$mission->mode] }}
    @endsection
@endif

@section('nav-right')
    @if ($mission->isMine() || auth()->user()->can('test-missions'))
        @include('missions.show.verification')
        @include('missions.show.revisions')
        @include('missions.show.download')
    @endif

    @if ($mission->isMine() || auth()->user()->can('manage-missions'))
        @include('missions.show.manage')
    @endif

    @can('share-missions')
        @include('missions.show.share')
    @endcan
@endsection

@section('content')
    <script>
        $(document).ready(function(e) {
            $('.mission-nav .subnav .subnav-link').click(function(event) {
                var caller = $(this);

                $.ajax({
                    type: 'GET',
                    url: "{{ url('/hub/missions/'.$mission->id) }}/" + caller.data('panel'),
                    success: function(data) {
                        $('.mission-nav .subnav .subnav-link').removeClass('active');
                        'hub/missions/{{ $mission->id }}/' + caller.data('panel');
                        caller.addClass('active');
                        $('.mission-inner').html(data);
                    }
                });

                event.preventDefault();
            });

            @if (isset($panel))
                $('.mission-nav .subnav .subnav-link').removeClass('active');
                $('.mission-nav .subnav .subnav-link[data-panel="{{ $panel }}"]').addClass('active');
            @endif
        });
    </script>

    <div class="container card-container mission-container {{ ($mission->banner() != '') ?: 'mission-without-banner' }}" style="margin-top: 6rem;">
        <h1 class="mission-title">
            {{ $mission->display_name }}
        </h1>

        <h3 class="mission-author">
            By {{ $mission->user->username }}
        </h3>

        <header class="mission-nav">
            <div class="subnav">
                <a
                    href="javascript:void(0)"
                    data-panel="overview"
                    class="subnav-link ripple active"
                >Overview</a>

                @if (!empty($mission->briefingFactions()))
                    <a
                        href="javascript:void(0)"
                        data-panel="briefing"
                        class="subnav-link ripple"
                    >Briefing</a>
                @endif

                <a
                    href="javascript:void(0)"
                    data-panel="orbat"
                    class="subnav-link ripple"
                >ORBAT</a>

                <a
                    href="javascript:void(0)"
                    data-panel="aar"
                    class="subnav-link ripple"
                >After-Action Report</a>

                <a
                    href="javascript:void(0)"
                    data-panel="media"
                    class="subnav-link ripple"
                >Media</a>

                @if (auth()->user()->can('test-missions') || $mission->isMine())
                    <a
                        href="javascript:void(0)"
                        data-panel="addons"
                        class="subnav-link ripple"
                    >Addons</a>

                    <a
                        href="javascript:void(0)"
                        data-panel="notes"
                        class="subnav-link ripple"
                    >Notes</a>
                @endif
            </div>
        </header>

        <div class="mission-inner">
            @if (app('request')->input('u'))
                <div class="alert alert-success m-b-2 m-t-2" role="alert">
                    <strong>Mission Updated!</strong> Let the mission testers know what you changed by adding a note.
                </div>
            @endif

            @if (isset($panel))
                @include('missions.show.'.$panel, ['mission' => $mission])
            @else
                @include('missions.show.overview', ['mission' => $mission])
            @endif
        </div>
    </div>
@endsection
