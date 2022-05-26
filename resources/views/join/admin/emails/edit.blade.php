<script>
    $(document).ready(function(e) {
        $('#email-modal-save').click(function(event) {
            $(this).prop('disabled', true);
            $('#edit-email-form').submit();
            event.preventDefault();
        });

        $('#edit-email-form').submit(function(event) {
            var form = $(this);

            $.ajax({
                type: 'POST',
                url: '{{ url('/hub/applications/api/emails/' . $email->id) }}',
                data: form.serialize(),
                success: function(data) {
                    $('#emailEditModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('#email-modal-save').prop('disabled', false);
                    reloadEmails();
                }
            });

            event.preventDefault();
        });

        @if (!$email->locked)
            $('#delete-email').click(function(event) {
                event.preventDefault();
                var canDelete = confirm("Delete this email template?");

                if (canDelete) {
                    $.ajax({
                        type: 'DELETE',
                        url: '{{ url('/hub/applications/api/emails/' . $email->id) }}',
                        success: function(data) {
                            $('#emailEditModal').modal('hide');
                            $('.modal-backdrop').remove();
                            reloadEmails();
                        }
                    });
                }
            });
        @endif
    });
</script>

<div class="modal" id="emailEditModal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>

                <h4 class="modal-title">Edit Email Template</h4>
            </div>

            <div class="modal-body">
                <form id="edit-email-form">
                    {{ method_field('put') }}
                    @include('join.admin.emails.form')

                    @if (!$email->locked)
                        <button class="btn btn-danger float-start mt-3" id="delete-email">
                            <i class="fa fa-trash me-2"></i>
                            Delete
                        </button>
                    @endif
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="email-modal-save">Save Changes</button>
            </div>
        </div>
    </div>
</div>
