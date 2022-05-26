<script>
    $(document).ready(function(e) {
        $('#create-email').click(function(event) {
            $.ajax({
                type: 'GET',
                url: '{{ url('/hub/applications/api/emails/create') }}',
                success: function(data) {
                    $('#email-modal').html(data);
                    $('#email-modal .modal').modal('show');
                }
            });

            event.preventDefault();
        });

        $('.email-item').click(function(event) {
            var caller = $(this);

            $.ajax({
                type: 'GET',
                url: '{{ url('/hub/applications/api/emails') }}/' + caller.data('id') + '/edit',
                success: function(data) {
                    $('#email-modal').html(data);
                    $('#email-modal .modal').modal('show');
                }
            });

            event.preventDefault();
        });
    });
</script>

<a href="javascript:void(0)" class="btn btn-primary float-end mt-0 mb-3" id="create-email">
    <i class="fa fa-plus me-1"></i>
    Create
</a>

<div class="list-group list-group-flush float-end">
    @foreach ($emails as $email)
        <a href="javascript:void(0)" class="list-group-item list-group-item-action email-item jr-item" data-id="{{ $email->id }}">
        <span class="jr-item-title">{{ $email->subject }}</span>
            <br />

            <span class="jr-item-meta">
                {{ substr(strip_tags($email->content), 0, 500) }}
            </span>
        </a>
    @endforeach
</div>

<div id="email-modal"></div>
