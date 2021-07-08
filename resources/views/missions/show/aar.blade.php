<div class="mission-comments">
    @if ($mission->comments->isEmpty())
        <p class="text-xs-center p-y-3 text-muted">There are no after-action reports yet. Be the first to submit one!</p>
    @else
        @include('missions.comments.list', ['comments' => $mission->comments])
    @endif
</div>

<div class="mission-comments-form pull-left w-100">
    <script>
        $(document).ready(function(e) {
            var runConvert = function() {
                $('.mission-comment-item-text').each(function(idx, span) {
                    var jspan = $(span);

                    if (!jspan.hasClass('md-converted')) {
                        span.innerHTML = window.mdconvert.makeHtml(span.innerText);
                        jspan.addClass('md-converted');
                    }
                });
            };

            runConvert();

            $(document).on('click', '.mission-comment-control-edit', function(event) {
                var caller = $(this);
                var id = caller.data('id');

                $.ajax({
                    type: 'GET',
                    url: '{{ url("/hub/missions/comments") }}/' + caller.data('id') + '/edit',

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
                    url: '{{ url("/hub/missions/{$mission->id}/comments") }}',
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
                            url: '{{ url("/hub/missions/{$mission->id}/comments") }}',

                            success: function(data) {
                                $('.mission-comments').html(data);
                                runConvert();
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
                    url: '{{ url("/hub/missions/{$mission->id}/comments") }}',
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

            new Mentions({
                input: '.has-mentions',
                output: '#mentions-list',
                pools: [{
                    trigger: '@',
                    pool: 'users',
                    display: 'username',
                    reference: 'id'
                }, {
                    trigger: '#',
                    pool: 'missions',
                    display: 'display_name',
                    reference: 'id'
                }]
            });

            $('.mission-comments .mention-node[data-object*="missions"]').each(function(i, node) {
                node = $(node);
                text = node.html();
                id = node.data('object').split(':')[1];
                node.replaceWith('<a href="{{ url('/hub/missions') }}/' + id + '" class="mention-node">' + text + '</a>');
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
        <input type="hidden" name="mentions" value="" id="mentions-list">

        <textarea
            class="form-control"
            id="submit-mission-comment-text"
            name="text"
            style="display:none">{!! (!is_null($mission->draft())) ? $mission->draft()->text : '' !!}</textarea>

        <div
            class="form-control-editable has-mentions mission-aar-textarea form-control m-b-3 m-t-3"
            contenteditable="plaintext-only"
            placeholder="Your mission experience..."
            for="#submit-mission-comment-text">{!! (!is_null($mission->draft())) ? $mission->draft()->text : '' !!}</div>

        <span id="auto-save-hint" class="pull-left text-muted p-l-3"></span>

        <button type="submit" class="btn btn-raised btn-primary pull-right m-l-3 m-r-3">Publish</button>
        <button class="btn pull-right" id="save-mission-comment">Save Draft</button>
    </form>
</div>
