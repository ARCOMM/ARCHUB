@extends('layout-public')

@section('title')
    Media
@endsection

@section('scripts')
    <script>
        $(document).ready(function(e) {
            $('.media-gallery').magnificPopup({
                delegate: '.media-item-public',
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        });
    </script>
@endsection

@section('content')
    <div class="content pa-0 container">
        <div class="media-gallery">
            @foreach ($missions as $mission)
                @foreach ($mission->photos() as $media)
                    <a
                        href="{{ url($media->getUrl()) }}"
                        class="media-item-public"
                        style="background-image: url('{{ url($media->getUrl('thumb')) }}')">
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>
@endsection
