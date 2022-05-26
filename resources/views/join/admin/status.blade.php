<script>
    $(document).ready(function() {
        reload = function() {
            $.ajax({
                type: 'GET',
                url: '{{ url('/hub/applications/api/'.$jr->id.'/status') }}',
                success: function(data) {
                    $('#status').html(data);
                }
            });
        }

        setStatus = function(id) {
            $.ajax({
                type: 'POST',
                url: '{{ url('/hub/applications/api/'.$jr->id.'/status') }}',
                data: {
                    "join_request_id": {{ $jr->id }},
                    "new_status_id": id,
                    "_method": "put"
                },
                success: function(data) {
                    reload();
                }
            });
        }

        $('#new_status').keyup(function(event) {
            var $this = $(this);
            var text = $this.val();
            var code = event.keyCode || event.which;

            // Do nothing if there is no text
            if (text.length === 0) return;

            // Enter key was pressed
            if (code == 13) {
                // Disable the input to prevent multiple insertions
                $this.prop("disabled", true);

                $.ajax({
                    type: 'POST',
                    url: '{{ url('/hub/applications/api/status') }}',
                    data: {"text": text},
                    success: function(data) {
                        var newStatusID = data;
                        setStatus(newStatusID);
                    }
                });
            }
        });
    });
</script>

<div class="btn-group mt-0 jr-status-dropdown float-end {{ strtolower($jr->status->permalink) }}">
    <button
        class="btn btn-secondary dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        data-id="{{ $jr->status->id }}"
        data-text="{{ $jr->status->text }}">
        {{ $jr->status->text }}
    </button>

    <div class="dropdown-menu">
        @foreach ($joinStatuses as $status)
            @unless ($status->id == $jr->status->id)
                <a class="dropdown-item" onclick="javascript:setStatus({{ $status->id }})" href="javascript:void(0)">
                    {{ $status->text }}
                </a>
            @endunless
        @endforeach

        <div class="dropdown-divider"></div>

        <input type="text" class="form-control" name="status" id="new_status" placeholder="New status" maxlength="12">
    </div>
</div>
