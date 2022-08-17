@extends('layouts.app')

@section('title')
    Settings
@endsection

@section('header-color')
    primary
@endsection

@section('head')
    <script>
        $(document).ready(function(e) {
            $('.subnav-link').click(function(event) {
                var caller = $(this);
                var panel = caller.data('panel');

                $.ajax({
                    type: 'GET',
                    url: '{{ url('/hub/settings?panel=') }}' + panel,
                    success: function(data) {
                        $('#settings-content').html(data);
                    }
                });

                event.preventDefault();
            });
        });
    </script>
@endsection

@section('content')
    <div class="container" id="settings-content">
        @include('user.settings.account')
    </div>
@endsection
