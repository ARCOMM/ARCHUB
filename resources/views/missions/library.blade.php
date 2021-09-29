@php
    use App\Models\Operations\Operation;
    use App\Models\Missions\Mission;
@endphp

@php
    $myMissions = auth()->user()->missions();
    $nextOperation = Operation::nextWeek();

    if ($nextOperation) {
        $nextOperationMissions = $nextOperation->missions;
    }

    $prevOperation = Operation::lastWeek();
    $newMissions = Mission::allNew();
    $pastMissions = Mission::allPast();
    $isTester = auth()->user()->can('test-missions')
@endphp

<div class="missions-pinned">
    <div class="missions-pinned-groups">
        @if ($nextOperation)
            @if (!$nextOperationMissions->isEmpty())
                <ul class="mission-group mission-group-pinned" data-title="Next Operation &mdash; {{ $nextOperation->startsIn() }}">
                    @foreach ($nextOperationMissions as $item)
                        @include('missions.item', [
                            'mission' => $item->mission, 
                            'isTester' => $isTester, 
                            'classes' => 'mission-item-pinned', 
                            'pulse' => true
                        ])
                    @endforeach
                </ul>
            @else
                <div
                    class="mission-empty-group mission-group-pinned"
                    data-title="Next Operation &mdash; {{ $nextOperation->startsIn() }}"
                    data-subtitle="Missions haven't been picked yet!">
                </div>
            @endif
        @endif

        @if ($prevOperation)
            <ul class="mission-group mission-group-pinned" data-title="Past Operation">
                @foreach ($prevOperation->missions as $item)
                    @include('missions.item', ['mission' => $item->mission, 'isTester' => $isTester, 'ignore_new_banner' => true])
                @endforeach
            </ul>
        @endif
    </div>
</div>

@if ($isTester && !$newMissions->isEmpty())
    <ul class="mission-group" data-title="New Missions">
        @foreach ($newMissions as $mission)
            @include('missions.item', ['mission' => $mission, 'isTester' => true])
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
            @include('missions.item', [
                'mission' => $mission,
                'isTester' => $isTester,
                'ignore_new_banner' => true
            ])
        @endforeach
    @endif
</ul>

@if (!$pastMissions->isEmpty())
    <ul class="mission-group" data-title="Past Missions">
        @foreach ($pastMissions as $mission)
            @include('missions.item', ['mission' => $mission, 'isTester' => $isTester, 'ignore_new_banner' => true])
        @endforeach
    </ul>
@endif
