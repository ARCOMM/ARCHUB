<div class="mission-comments">
    @if ($mission->comments->isEmpty())
        <p class="text-center py-3 text-muted">There are no after-action reports yet. Be the first to submit one!</p>
    @else
        @include('missions.comments.list', ['comments' => $mission->comments])
    @endif
</div>

<div class="mission-comments-form float-start w-100">
    <script>
        $(document).ready(function(e) {
            $(document).on('click', '.mission-comment-control-edit', function(event) {
                var caller = $(this);
                var id = caller.data('id');

                $.ajax({
                    type: 'GET',
                    url: '{{ url("/hub/missions/comments") }}/' + id + '/edit',

                    success: function(data) {
                        data = JSON.parse(data);
                        $('#submit-mission-comment input[name="id"]').val(id);
                        $('#submit-mission-comment textarea[name="text"]').val(data.text);
                        $('#submit-mission-comment .mission-aar-textarea').html(data.text);
                        $('#submit-mission-comment button[type="submit"]').html('Save Changes');
                        $('#submit-mission-comment #save-mission-comment').hide();
                        $('#submit-mission-comment textarea[name="text"]').focus();
                        $('.mission-container').scrollTop(10000);
                    }
                });

                event.preventDefault();
            });

            $(document).on('click', '.mission-comment-control-delete', function(event) {
                event.preventDefault();
                var canDelete = confirm("Are you sure you want to delete this?");

                if (canDelete) {
                    var caller = $(this);

                    $.ajax({
                        type: 'DELETE',
                        url: '{{ url("/hub/missions/comments") }}/' + caller.data('id'),

                        success: function(data) {
                            caller.parents('.mission-comment-item').remove();
                        }
                    });
                }
            });

            $('#submit-mission-comment').submit(function(event) {
                $('#submit-mission-comment input[name="published"]').val(1);
                $('#submit-mission-comment button[type="submit"]').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: '{{ url('/hub/missions/comments') }}',
                    data: $('#submit-mission-comment').serialize(),
                    success: function(data) {
                        $('#submit-mission-comment input[name="id"]').val(-1);
                        $('#submit-mission-comment textarea[name="text"]').val('');
                        $('#submit-mission-comment .mission-aar-textarea').html('');
                        $('#submit-mission-comment button[type="submit"]').html('Publish');
                        $('#submit-mission-comment button[type="submit"]').prop('disabled', false);
                        $('#submit-mission-comment #save-mission-comment').show();

                        $.ajax({
                            type: 'GET',
                            url: '{{ url('/hub/missions/comments?mission_id=' . $mission->id) }}',
                            success: function(data) {
                                $('.mission-comments').html(data);
                            }
                        });
                    },
                    error: function() {
                        $('#submit-mission-comment button[type="submit"]').prop('disabled', false);
                    }
                });

                event.preventDefault();
            });

            $('#save-mission-comment').click(function(event) {
                $('#submit-mission-comment input[name="published"]').val(0);

                $.ajax({
                    type: 'POST',
                    url: '{{ url('/hub/missions/comments') }}',
                    data: $('#submit-mission-comment').serialize(),
                    success: function(data) {
                        $('#submit-mission-comment input[name="id"]').val(data.trim());
                        $('#auto-save-hint').fadeIn().html('Saved Draft');
                        setTimeout(function() {
                            $('#auto-save-hint').fadeOut();
                        }, 3000);
                    }
                });

                event.preventDefault();
            });

            $('.mission-comments .mission-comment-item[id="' + window.location.hash.substr(1) + '"]').addClass('highlight');

            setInterval(function() {
                if ($('#submit-mission-comment-text').val().length > 0) {
                    $('#save-mission-comment').click();
                }
            }, 10000);

            document.querySelector('.mission-aar-textarea').addEventListener('paste', function(e) {
                e.preventDefault();
                let text = e.clipboardData.getData('text/plain');
                document.execCommand('insertHTML', false, text);
            });
        });
    </script>

    <form method="post" id="submit-mission-comment">
        <input type="hidden" name="id" value="{{ (!is_null($mission->draft())) ? $mission->draft()->id : '-1' }}">
        <input type="hidden" name="mission_id" value="{{ $mission->id }}">
        <input type="hidden" name="published" value="0">

        <textarea
            class="form-control-editable form-control mission-aar-textarea my-3"
            id="submit-mission-comment-text"
            name="text">{!! (!is_null($mission->draft())) ? $mission->draft()->text : '' !!}</textarea>

        <span id="auto-save-hint" class="float-start text-muted ps-3"></span>

        <button type="submit" class="btn btn-raised btn-primary float-end mx-3">Publish</button>
        <button class="btn float-end" id="save-mission-comment">Save Draft</button>
    </form>
</div>
