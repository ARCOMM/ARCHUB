@extends('layout-share')

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

@section('content')
    <script>
        $(document).ready(function(e) {
            $('.mission-nav .subnav .subnav-link').click(function(event) {
                var caller = $(this);

                $.ajax({
                    type: 'GET',
                    url: "{{ url('/share/'.$mission->id) }}/" + caller.data('panel'),
                    success: function(data) {
                        $('.mission-nav .subnav .subnav-link').removeClass('active');
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
                    data-panel="media"
                    class="subnav-link ripple"
                >Media</a>
            </div>
        </header>

        <div class="mission-inner">
            @if (isset($panel))
                @include('share.'.$panel, ['mission' => $mission])
            @else
                @include('share.overview', ['mission' => $mission])
            @endif
        </div>
    </div>
@endsection
