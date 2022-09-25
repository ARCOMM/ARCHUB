<script>
    $(document).ready(function(e) {
        $('.mission-briefing-nav a').click(function(event) {
            var caller = $(this);
            var locked = caller.hasClass('locked');
            var faction = caller.data('faction');

            if (locked) return;

            $.ajax({
                type: 'POST',
                url: "{{ url('/hub/missions/briefing')}} ",
                data: {
                    mission_id: "{{ $mission->id }}",
                    faction: faction
                },
                success: function(data) {
                    $('.mission-briefing-content').html(data);
                    $('.mission-briefing-nav a').removeClass('active');
                    caller.addClass('active');
                }
            });

            event.preventDefault();
        });

        $('.mission-briefing-nav a:first').click();
    });
</script>

<div class="mission-briefing">
    <div class="mission-briefing-nav">
        @foreach ($mission->briefingFactions() as $briefing)
        <a href="javascript:void(0)" class="ripple" data-faction="{{ $briefing->name }}">
            {{ $briefing->name }}
        </a>
        @endforeach
    </div>

    <div class="mission-briefing-content"></div>
</div>