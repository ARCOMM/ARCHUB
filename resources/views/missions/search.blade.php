@php
    use App\Models\Missions\Mission;
@endphp

@php
    $myMissions = auth()->user()->missions();
    $newMissions = Mission::allNew();
    $pastMissions = Mission::allPast();
    $isTester = auth()->user()->can('test-missions');
    $noFilter = !isset($results);
@endphp

@if ($isTester && !$newMissions->isEmpty())
    <ul class="mission-group" data-title="New Missions">
        @foreach ($newMissions as $mission)
            @if ($noFilter || in_array($mission->id, $results))
                @include('missions.item', ['mission' => $mission, 'isTester' => true])
            @endif
        @endforeach
    </ul>
@endif

<ul
    class="mission-group {{ ($myMissions->isEmpty()) ? 'mission-empty-group' : '' }}"
    data-title="My Missions"
    @if ($myMissions->isEmpty())
        data-subtitle="You haven't uploaded any missions!"
    @endif>

    @if (!$myMissions->isEmpty())
        @foreach ($myMissions as $mission)
            @if ($noFilter || in_array($mission->id, $results))
                @include('missions.item', ['mission' => $mission, 'isTester' => $isTester])
            @endif
        @endforeach
    @endif
</ul>

@if (!$pastMissions->isEmpty())
    <ul class="mission-group" data-title="Past Missions">
        @foreach ($pastMissions as $mission)
            @if ($noFilter || in_array($mission->id, $results))
                @include('missions.item', ['mission' => $mission, 'isTester' => $isTester])
            @endif
        @endforeach
    </ul>
@endif