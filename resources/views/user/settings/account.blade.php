<script>
    $(document).ready(function(e) {
        $('#update-account').submit(function(event) {
            $.ajax({
                type: 'POST',
                url: '{{ url('/hub/settings') }}',
                data: $('#update-account').serialize(),
                success: function(data) {
                    alert('Changes Saved!');
                }
            });

            event.preventDefault();
        });

        $('#avatar-sync').click(function(event) {
            $.ajax({
                type: 'GET',
                url: '{{ url('/hub/settings/avatar-sync') }}',
                success: function(data) {
                    $('.user-avatar').attr('src', data.trim());
                }
            });

            event.preventDefault();
        });
    });
</script>

<div class="text-center">
    <img class="user-avatar mx-auto img-thumbnail" src="{{ auth()->user()->avatar }}">
</div>

<div class="mt-3 text-center">
    <a href="javascript:void(0)" class="btn btn-primary mx-auto mt-1 mb-2" id="avatar-sync">
        Sync From Discord
    </a>
</div>

<div class="mt-3">
    <form method="post" class="mx-auto col-md-4" id="update-account">
        <div class="form-group">
            <label class="w-100 text-center">Username</label>
            <input type="text" name="username" class="form-control text-center" placeholder="Username" value="{{ auth()->user()->username }}" maxlength="30">
        </div>

        <div class="form-group text-center">
            <button type="submit" class="btn btn-raised btn-primary mx-auto mt-2">
                Save Changes
            </button>
        </div>
    </form>
</div>
