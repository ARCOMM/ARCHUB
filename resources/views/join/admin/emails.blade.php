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

<div class="row">
    <div class="col-md-12 px-3 pt-1">
        <a href="javascript:void(0)" class="btn btn-primary float-end mt-0 mb-5" id="create-email">
            <i class="fa fa-plus mr-1"></i>
            Create
        </a>
    </div>
</div>

<div class="list-group">
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
