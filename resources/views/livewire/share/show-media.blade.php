<div class="float-start w-100 mission-media">
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

    @if ($mission->photos()->isEmpty() && $mission->videos->isEmpty())
        <p class="text-center text-muted">Upload a photo for the mission banner!</p>
    @else
        @foreach ($mission->photos() as $media)
            <div
                href="{{ $media->getUrl() }}"
                class="mission-media-item mission-media-item-image"
                style="background-image: url('{{ url($media->getUrl('thumb')) }}')">
            </div>
        @endforeach
    @endif
</div>
