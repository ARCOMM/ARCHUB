@if ($mission->isMine() || auth()->user()->can('manage-missions'))
    <script>
        $(document).ready(function(e) {
            $('#lock-briefing').click(function(event) {
                var caller = $(this);
                var locked = caller.data('locked');

                $.ajax({
                    type: 'PUT',
                    url: '{{ url("/hub/missions") }}/' + caller.data('id') + '/briefing/' + caller.data('faction') + '/lock',
                    data: {
                        'locked': locked,
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

    <div class="pull-left w-100">
        <a
            href="javascript:void(0)"
            class="btn btn-primary pull-right m-t-3"
            id="lock-briefing"
            data-faction="{{ $faction }}"
            data-id="{{ $mission->id }}"
            data-locked="{{ ($mission->briefingLocked($faction)) ? '0' : '1' }}">
            {{ ($mission->briefingLocked($faction)) ? 'Unlock' : 'Lock' }} {{ $mission->factions[$faction] }} Briefing
        </a>
    </div>
@endif

@if ($mission->briefingLocked($faction))
    <div class="alert alert-warning pull-left w-100 m-t-2" role="alert">
        <strong>This briefing is locked!</strong> Only the mission maker and testers can see it.
    </div>
@endif

<div class="pull-left w-100 m-t-3">
    @foreach ($mission->briefing($faction) as $subject)
        <h5>{{ $subject->title }}</h5>       

        {!! $subject->paragraphs !!}
        <br/><br/>
    @endforeach
</div>
