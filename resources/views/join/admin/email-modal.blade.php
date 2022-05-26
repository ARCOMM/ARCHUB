<script>
    $(document).ready(function(e) {
        var emails = {!! json_encode($emails) !!};
        emails.push({ id: -1, subject: '', content: '' });

        $('#f-template').change(function(event) {
            var email = null;
            var id = $(this).val();
            for (var e of emails) if (e.id == id) email = e;

            $('#f-subject').val(email.subject);
            $('#f-body').val(email.content);

            event.preventDefault();
        });

        $('#f-form').submit(function(event) {
            $.ajax({
                type: 'POST',
                url: '{{ url('/hub/applications/api/send-email') }}',
                data: $('#f-form').serialize(),
                success: function(data) {
                    $('#emailModal').modal('hide');
                    $('#email-modal-send').prop('disabled', false);
                    reloadSubmissions();
                }
            });

            event.preventDefault();
        });

        $('#email-modal-send').click(function(event) {
            $(this).prop('disabled', true);
            $('#f-form').submit();
            event.preventDefault();
        });
    });
</script>

<form method="post" id="f-form">
    <input type="hidden" name="jr_id" value="{{ $jr->id }}">

    <div class="form-group pt-0 mb-3">
        <select id="f-template" name="template" class="form-control">
            <option value="-1" selected="true">Select Preset (optional)</option>
            @foreach ($emails as $email)
                <option value="{{ $email->id }}">
                    {{ $email->subject }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group pt-0 mb-3">
        <input name="subject" id="f-subject" type="text" class="form-control" placeholder="Subject">
    </div>

    <div class="form-group mb-3">
        <textarea name="body" id="f-body" class="form-control" placeholder="Contents" rows="10"></textarea>
    </div>
</form>
