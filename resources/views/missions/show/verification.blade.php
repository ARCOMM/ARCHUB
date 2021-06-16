@can('verify-missions')
    <script>
        $(document).ready(function(e) {
            $('#mission-verification').click(function(event) {
                var caller = $(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ url("/hub/missions/{$mission->id}/set-verification") }}',
                    success: function(data) {
                        if (caller.hasClass('mission-verified')) {
                            caller.removeClass('mission-verified');
                            caller.addClass('mission-unverified');
                            caller.attr('title', "Mark this mission as verified");
                        } else {
                            caller.removeClass('mission-unverified');
                            caller.addClass('mission-verified');
                            caller.find('em').html(data);
                            caller.attr('title', "Mark this mission as unverified");
                        }
                    }
                });

                event.preventDefault();
            });
        });
    </script>
@endcan

<li class="nav-item hidden-md-down">
    <a
        href="javascript:void(0)"
        id="mission-verification"
        class="nav-link {{ ($mission->verified) ? 'mission-verified' : 'mission-unverified' }}"
        title="Mark this mission as {{ ($mission->verified) ? 'unverified' : 'verified' }}">

        <em>
            @if ($mission->verifiedByUser())
                Verified by {{ $mission->verifiedByUser()->username }}
            @endif
        </em>

        @if (auth()->user()->can('verify-missions') || $mission->verifiedByUser())
            <i class="fa fa-check"></i>
        @endif
    </a>
</li>
