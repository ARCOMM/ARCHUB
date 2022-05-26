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
                    caller.prepend('<i class="fa fa-spin fa-refresh me-1"></i>');
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

<li class="nav-item dropdown hidden-sm-down">
    <a class="nav-link dropdown-toggle" id="downloadDropdown" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-download"></i>
    </a>

    <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
        <li><a class="dropdown-item download-mission" href="javascript:void(0)">Download</a></li>
    </ul>
</li>
