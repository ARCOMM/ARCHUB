<script>
    $(document).ready(function(e) {
        $('.download-mission').click(function(event) {
            var caller = $(this);

            // Exit if already getting download
            if (caller.hasClass('pending')) {
                event.stopPropagation();
                event.preventDefault();
                return;
            }

            $.ajax({
                type: 'GET',
                url: "{{ url('/hub/missions/'.$mission->id.'/download') }}",
                beforeSend: function() {
                    caller.prepend('<i class="fa fa-spin fa-refresh m-r-1"></i>');
                    caller.addClass('pending');
                },
                success: function(data) {
                    caller.removeClass('pending');
                    caller.find('i.fa').remove();
                    window.location.href = data.trim();
                }
            });

            event.stopPropagation();
            event.preventDefault();
        });
    });
</script>

<li class="nav-item dropdown m-l-2">
    <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-download"></i>
    </a>

    <div class="dropdown-menu">
        <a href="javascript:void(0)" class="dropdown-item download-mission">Download</a>
    </div>
</li>
