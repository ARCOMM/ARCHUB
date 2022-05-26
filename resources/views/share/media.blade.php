<script>
    $(document).ready(function(e) {
        $('.mission-media').magnificPopup({
            delegate: '.mission-media-item-image',
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    });
</script>

<div class="float-start w-100 mission-media">
    @if ($mission->photos()->isEmpty() && $mission->videos->isEmpty())
        <p class="text-center text-muted">Upload a photo for the mission banner!</p>
    @else
        @foreach ($mission->photos() as $media)
            @include('share.media-photo', [
                'media' => $media
            ])
        @endforeach
    @endif
</div>
