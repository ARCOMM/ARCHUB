@if ($mission->isMine() || auth()->user()->can('manage-missions'))
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="dropdownMenuLink" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-cog"></i>
        </a>

        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
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

                <a class="dropdown-item" id="update-mission" href="javascript:void(0)">Update</a>
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
