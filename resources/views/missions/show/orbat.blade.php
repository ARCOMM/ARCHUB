<script>
    $(document).ready(function(e) {
        $('.mission-orbat-nav a').click(function(event) {
            var caller = $(this);
            var faction = caller.data('faction');

            $.ajax({
                type: 'POST',
                url: '{{ url('/hub/missions/orbat') }}',
                data: {
                    mission_id: {{ $mission->id }},
                    faction: faction
                },
                success: function(data) {
                    $('.mission-orbat-content').html(data);
                    $('.mission-orbat-nav a').removeClass('active');
                    caller.addClass('active');
                }
            });

            event.preventDefault();
        });

        $('.mission-orbat-nav a:first').click();
    });
</script>

<div class="mission-orbat">
    <div class="mission-orbat-nav">
        @foreach ($mission->orbatFactions() as $faction)
            <a
                href="javascript:void(0)"
                class="ripple"
                data-faction="{{ $faction }}">
                {{ $faction }}
            </a>
        @endforeach
    </div>

    <div class="mission-slotting-details">
        {{ $mission->slottingDetails }}
    </div>

    <div class="mission-orbat-content"></div>
</div>
