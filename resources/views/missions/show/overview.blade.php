@php
    $canManageTags = auth()->user()->can('manage-tags');
@endphp

@if ($mission->isMine() || $canManageTags)
    <script>
        $(document).ready(function(event) {
            $('#tag_select').select2({
                multiple: true,
                placeholder: "Tags",
                tags: "{{ $canManageTags }}",
            });

            $.ajax({
                type: 'GET',
                url: '{{ url("/hub/missions/{$mission->id}/tags") }}',

                success: function(selectedTagIds) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ url("/hub/missions/tags") }}',

                        success: function(data) {
                            $.each(data, function(index, value) {
                                var selected = selectedTagIds.indexOf(value["id"]) != -1;
                                var newOption = new Option(value["name"], index, selected, selected);
                                $('#tag_select').append(newOption).trigger('change');
                            });
                        }
                    });
                }
            });

            $('#tag_select').on('select2:select', function(e) {
                $.ajax({
                    type: 'POST',
                    url: '{{ url("/hub/missions/{$mission->id}/tags") }}',
                    data: {
                        "tag": e.params.data["text"],
                    }
                });
            });

            $('#tag_select').on('select2:unselect', function(e) {
                $.ajax({
                    type: 'DELETE',
                    url: '{{ url("/hub/missions/{$mission->id}/tags") }}',
                    data: {
                        "tag": e.params.data["text"],
                    }
                });
            });
        });
    </script>
@else
    <script>
        $(document).ready(function(event) {
            $.ajax({
                type: 'GET',
                url: '{{ url("/hub/missions/{$mission->id}/tags") }}',

                success: function(selectedTagIds) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ url("/hub/missions/tags") }}',

                        success: function(data) {
                            $.each(data, function(index, value) {
                                var selected = selectedTagIds.indexOf(value["id"]) != -1;
                                if (selected) {
                                    $('.mission-tags').append('<span class="badge bg-primary">' + value["name"] + '</span>');
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
@endif

<div class="row">
    <div class="col-md-4 mission-overview-card">
        <h4>
            {{ $mission->fulltextGamemode() }} on {{ $mission->map->display_name }}
        </h4>

        <h6>
            {{ $mission->date() }} &mdash; {{ $mission->time() }}
        </h6>

        <p class="mt-2">{{ $mission->summary }}</p>
    </div>

    <div class="col-md-4 mission-overview-card">
        <h4 class="text-center">
            {{ $mission->weather() }}
        </h4>

        <span class="mission-weather-image" style="background-image: url('{{ $mission->weatherImage() }}')"></span>
    </div>

    <div class="col-md-4 mission-overview-card">
        <h4>
            Published {{ $mission->created_at->diffForHumans() }}
        </h4>

        <hr class="mt-3">

        <p class="text-center pt-3">
            @if ($mission->hasBeenPlayed())
                Last played {{ $mission->last_played->diffForHumans() }}
            @else
                Mission has not been played in an operation yet! Soon&trade;
            @endif
        </p>
    </div>
</div>

<div class="row">
    <a href="{{ $mission->url('aar') }}" class="col-md-4 mission-overview-card">
        @php
            $commentCount = $mission->comments->where('published', 1)->count();
        @endphp

        <h4 class="text-center">
            {{ $commentCount }} After-Action Report{{ $commentCount != 1 ? 's' : '' }}
        </h4>

        <span class="mission-weather-image">
            <i class="fa fa-comments"></i>
        </span>
    </a>

    <a href="{{ $mission->url('media') }}" class="col-md-4 mission-overview-card">
        @php
            $imageCount = $mission->photos()->count();
        @endphp

        <h4 class="text-center">
            {{ $imageCount }} Photo{{ $imageCount != 1 ? 's' : '' }}
        </h4>

        <span class="mission-weather-image">
            <i class="fa fa-camera" style="font-size: 6rem"></i>
        </span>
    </a>

    <a href="{{ $mission->url('media') }}" class="col-md-4 mission-overview-card">
        @php
            $videoCount = $mission->videos->count();
        @endphp

        <h4 class="text-center">
            {{ $videoCount }} Video{{ $videoCount != 1 ? 's' : '' }}
        </h4>

        <span class="mission-weather-image">
            <i class="fa fa-video-camera"></i>
        </span>
    </a>
</div>

<div class="mission-tags">
    @if ($mission->isMine() || $canManageTags)
        <select name="tags" class="form-control" id="tag_select"></select>
    @endif
</div>
