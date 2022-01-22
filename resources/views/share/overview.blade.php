<div class="row">
    <div class="col-md-4 mission-overview-card">
        <h4>
            {{ $mission->fulltextGamemode() }} on {{ $mission->map->display_name }}
        </h4>

        <h6>
            {{ $mission->date() }} &mdash; {{ $mission->time() }}
        </h6>

        <p class="m-t-2">{{ $mission->summary }}</p>
    </div>

    <div class="col-md-4 mission-overview-card">
        <h4 class="text-xs-center">
            {{ $mission->weather() }}
        </h4>

        <span class="mission-weather-image" style="background-image: url('{{ $mission->weatherImage() }}')"></span>
    </div>

    <div class="col-md-4 mission-overview-card">
        <h4>
            Published {{ $mission->created_at->diffForHumans() }}
        </h4>

        <hr class="m-t-3">

        <p class="text-xs-center p-t-3">
            @if ($mission->hasBeenPlayed())
                Last played {{ $mission->last_played->diffForHumans() }}
            @else
                Mission has not been played in an operation yet! Soon&trade;
            @endif
        </p>
    </div>
</div>

<div class="row">
    <a href="javascript:void(0)" class="col-md-4 mission-overview-card">
        @php
            $commentCount = $mission->comments->where('published', 1)->count();
        @endphp

        <h4 class="text-xs-center">
            {{ $commentCount }} After-Action Report{{ $commentCount != 1 ? 's' : '' }}
        </h4>

        <span class="mission-weather-image">
            <i class="fa fa-comments"></i>
        </span>
    </a>

    <a href="javascript:void(0)" class="col-md-4 mission-overview-card">
        @php
            $imageCount = $mission->photos()->count();
        @endphp

        <h4 class="text-xs-center">
            {{ $imageCount }} Photo{{ $imageCount != 1 ? 's' : '' }}
        </h4>

        <span class="mission-weather-image">
            <i class="fa fa-camera" style="font-size: 6rem"></i>
        </span>
    </a>

    <a href="javascript:void(0)" class="col-md-4 mission-overview-card">
        @php
            $videoCount = $mission->videos->count();
        @endphp

        <h4 class="text-xs-center">
            {{ $videoCount }} Video{{ $videoCount != 1 ? 's' : '' }}
        </h4>

        <span class="mission-weather-image">
            <i class="fa fa-video-camera"></i>
        </span>
    </a>
</div>
