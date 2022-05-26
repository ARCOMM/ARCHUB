<script>
    $(document).ready(function(e) {
        $(document).on('click', '.mission-note-edit', function(event) {
            var caller = $(this);
            var id = caller.data('id');

            $.ajax({
                type: 'GET',
                url: '{{ url("/hub/missions/{$mission->id}/notes") }}/' + caller.data('id') + '/edit',

                success: function(data) {
                    data = JSON.parse(data);
                    $('#submit-mission-comment input[name="id"]').val(id);
                    $('#submit-mission-comment textarea[name="text"]').val(data.text);
                    $('#submit-mission-comment .mission-aar-textarea').html(data.text);
                    $('#submit-mission-comment button[type="submit"]').html('Save Changes');
                    $('#submit-mission-comment textarea[name="text"]').focus();
                    $('.mission-container').scrollTop(10000);
                }
            });

            event.preventDefault();
        });

        $(document).on('click', '.mission-note-delete', function(event) {
            event.preventDefault();
            var canDelete = confirm("Are you sure you want to delete this?");

            if (canDelete) {
                var caller = $(this);

                $.ajax({
                    type: 'DELETE',
                    url: '{{ url("/hub/missions/{$mission->id}/notes") }}/' + caller.data('id'),

                    success: function(data) {
                        caller.parents('.mission-comment-item').remove();
                    }
                });
            }
        });

        $('#submit-mission-comment').submit(function(event) {
            var form = $(this);

            $.ajax({
                type: 'POST',
                url: '{{ url("/hub/missions/{$mission->id}/notes") }}',
                data: form.serialize(),

                beforeSend: function() {
                    $('*').blur();
                },
                success: function(data) {
                    $('#submit-mission-comment input[name="id"]').val(-1);
                    $('#submit-mission-comment textarea[name="text"]').val('');
                    $('#submit-mission-comment .mission-aar-textarea').html('');
                    $('#submit-mission-comment button[type="submit"]').html('Submit');

                    location.reload();
                }
            });

            event.preventDefault();
        });

        $('.mission-comments .mission-comment-item[id="' + window.location.hash.substr(1) + '"]').addClass('highlight');
    });
</script>

<div class="mission-comments">
    @foreach ($mission->notes as $note)
        @include('missions.notes.item', ['note' => $note])
    @endforeach
</div>

<div class="mission-comments-form float-start w-100">
    <form method="post" id="submit-mission-comment">
        <input type="hidden" name="id" value="-1'">

        <textarea
            class="form-control-editable form-control mission-aar-textarea my-3"
            id="submit-mission-comment-text-real"
            name="text">{!! (!is_null($mission->draft())) ? $mission->draft()->text : '' !!}</textarea>

        <a
            class="float-start mx-3"
            style="font-weight:600"
            href="https://docs.google.com/document/d/1vxyQSLEjB23ZDdZGHClXrOxN90SHbu26bEUXjq7fqLE/view"
            target="_newtab">
            Mission Checklist
        </a>

        <button type="submit" class="btn btn-raised btn-primary float-end mx-3">Submit</button>
    </form>
</div>
