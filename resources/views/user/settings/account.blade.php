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

<div class="row">
    <img class="user-avatar m-x-auto img-thumbnail" src="{{ auth()->user()->avatar }}">
</div>

<div class="row">
    <a href="javascript:void(0)" class="btn btn-primary m-x-auto m-t-1" id="avatar-sync">
        Sync From Discord
    </a>
</div>

<div class="row m-t-3">
    <form method="post" class="m-x-auto col-md-4" id="update-account">
        <div class="form-group">
            <label class="w-100 text-xs-center">Username</label>
            <input type="text" name="username" class="form-control text-xs-center" placeholder="Username" value="{{ auth()->user()->username }}" maxlength="30">
        </div>

        <div class="form-group text-xs-center">
            <button type="submit" class="btn btn-raised btn-primary m-x-auto m-t-2">
                Save Changes
            </button>
        </div>
    </form>
</div>
