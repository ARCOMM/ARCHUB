@extends('layout')

@section('title')
    Missions
@endsection

@section('header-color')
    primary
@endsection

@section('head')
    <script>
        $(document).ready(function(e) {
            $('#mission-upload-btn').dropzone({
                url: '{{ url('/hub/missions') }}',
                acceptedFiles: '',
                addedfile: function(file) {},
                sending: function() {
                    $('#mission-upload-btn').prepend('<i class="fa fa-spin fa-refresh me-1"></i>');
                },
                success: function(file, data) {
                    $('#mission-upload-btn').find('i.fa').remove();
                    window.location = data.trim();
                },
                error: function(file, message) {
                    $('#mission-upload-btn').find('i.fa').remove();
                    alert(message.message);
                }
            });
        });
    </script>
@endsection

@section('nav-right')
    <a
        href="javascript:void(0)"
        data-panel="upload"
        data-noajax="true"
        id="mission-upload-btn"
        class="nav-item nav-link hidden-sm-down"
    >Upload</a>
@endsection

@section('content')
    <div id="mission-content">
        @include('missions.library')
    </div>
@endsection
