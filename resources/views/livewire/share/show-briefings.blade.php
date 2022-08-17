<div class="mission-briefing">
    <div class="mission-briefing-nav">
        @foreach ($factions as $item)
            <a class="ripple {{ $activeFaction == $item->faction ? 'active' : '' }}" wire:click="$set('activeFaction', {{ $item->faction }})">
                {{ $item->name }}</a>
        @endforeach
    </div>

    <div class="mission-briefing-content">
        @if ($mission->briefingLocked($activeFaction))
            <div class="alert alert-warning float-start w-100 mt-2" role="alert">
                <strong>This briefing is locked!</strong> Only the mission maker and testers can see it.
            </div>
        @endif

        <div class="float-start w-100 mt-3">
            @foreach ($mission->briefing($activeFaction) as $subject)
                <h5>{{ $subject->title }}</h5>       

                {!! $subject->paragraphs !!}
                <br/><br/>
            @endforeach
        </div>
    </div>
</div>
