@if ($mission->briefingLocked($faction))
    <div class="alert alert-warning float-start w-100 mt-2" role="alert">
        <strong>This briefing is locked!</strong> Only the mission maker and testers can see it.
    </div>
@endif

@can('manage-mission', $mission)
    <script>
        $(document).ready(function(e) {
            $('#lock-briefing').click(function(event) {
                var caller = $(this);
                var id = caller.data('id');
                var faction = caller.data('faction');
                var locked = caller.data('locked');

                $.ajax({
                    type: 'POST',
                    url: '{{ url('/hub/missions/briefing/update') }}',
                    data: {
                        'mission_id': id,
                        'faction': faction,
                        'locked': locked
                    },
                    success: function(data) {
                        caller.data('locked', (locked == 1) ? 0 : 1);
                        caller.html(
                            (locked == 1) ?
                            'Unlock {{ $mission->factions[$faction] }} Briefing' :
                            'Lock {{ $mission->factions[$faction] }} Briefing'
                        );
                    }
                });

                event.preventDefault();
            });
        });
    </script>

    <div class="float-start w-100">
        <a
            href="javascript:void(0)"
            class="btn btn-primary float-end mt-3"
            id="lock-briefing"
            data-faction="{{ $faction }}"
            data-id="{{ $mission->id }}"
            data-locked="{{ ($mission->briefingLocked($faction)) ? '0' : '1' }}">
            {{ ($mission->briefingLocked($faction)) ? 'Unlock' : 'Lock' }} {{ $mission->factions[$faction] }} Briefing
        </a>
    </div>
@endcan

<div class="float-start w-100 mt-3">
    @foreach ($mission->briefing($faction) as $subject)
        <h5>{{ $subject->title }}</h5>       

        {!! $subject->paragraphs !!}
        <br/><br/>
    @endforeach
</div>
