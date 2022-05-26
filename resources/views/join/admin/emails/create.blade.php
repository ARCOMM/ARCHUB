<script>
    $(document).ready(function(e) {
        $('#email-modal-save').click(function(event) {
            $(this).prop('disabled', true);
            $('#create-email-form').submit();
            event.preventDefault();
        });

        $('#create-email-form').submit(function(event) {
            var form = $(this);

            $.ajax({
                type: 'POST',
                url: '{{ url('/hub/applications/api/emails') }}',
                data: form.serialize(),
                success: function(data) {
                    $('#emailCreateModal').modal('hide');
                    $('.modal-backdrop').remove();
                    reloadEmails();
                }
            });

            event.preventDefault();
        });
    });
</script>

<div class="modal" id="emailCreateModal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>

                <h4 class="modal-title">Create Email Template</h4>
            </div>

            <div class="modal-body">
                <form id="create-email-form">
                    @include('join.admin.emails.form')
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="email-modal-save">Create</button>
            </div>
        </div>
    </div>
</div>
