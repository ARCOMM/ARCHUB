@if ($mission->isMine() || auth()->user()->can('manage-missions'))
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-cog"></i>
        </a>

        <div class="dropdown-menu">
            @if ($mission->isMine() || auth()->user()->can('manage-missions'))
                <script>
                    $(document).ready(function(e) {
                        $('#update-mission').dropzone({
                            url: '{{ url('/hub/missions/' . $mission->id . '/update') }}',
                            acceptedFiles: '',
                            addedfile: function(file) {
                                $('body').prepend('<div class="mission-update-cover"><i class="fa fa-spin fa-refresh"></i></div>');
                            },
                            success: function(file, data) {
                                window.location = "{{ $mission->url() }}?u=1";
                            },
                            error: function(file, message) {
                                alert(message.message);
                                $('.mission-update-cover').remove();
                            }
                        });
                    });
                </script>

                <a href="javascript:void(0)" class="dropdown-item" id="update-mission">Update</a>
            @endif

            @if ($mission->isMine() || auth()->user()->can('manage-missions'))
                <script>
                    $(document).ready(function(e) {
                        $('#delete-mission').click(function(event) {
                            event.preventDefault();
                            var canDelete = confirm("Are you sure you want to delete this mission?");
                            if (canDelete) window.location = $(this).attr('href');
                        });
                    });
                </script>

                @if (auth()->user()->can('manage-missions') || !($mission->existsInOperation() || $mission->hasBeenPlayed()))
                    <a
                        href="{{ url('/hub/missions/' . $mission->id . '/delete') }}"
                        class="dropdown-item"
                        id="delete-mission"
                        title="Deletes the mission and all of its media, comments and files">
                        Delete
                    </a>
                @endif
            @endif
        </div>
    </li>
@endif
{{-- 
<div class="mission-inner">
    <div class="mission-nav" style="{{ !$can_see_nav ? 'box-shadow:none' : '' }}">
        <span class="mission-version">
            @if ($mission->hasBeenPlayed())
                LAST PLAYED {{ $mission->last_played->diffForHumans() }} 
            @else
                PUBLISHED {{ $mission->created_at->diffForHumans() }}
            @endif
        </span>
    </div>
</div>
 --}}
