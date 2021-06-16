<a
    href="{{ url('/hub/missions/' . $mission->id) }}"
    class="mission-item ripple {{ (isset($classes)) ? $classes : '' }}"
    style="background-image: url('{{ $mission->thumbnail() }}')"
    data-id="{{ $mission->id }}">

    @if (isset($pulse) && $pulse)
        {{-- <span class="mission-item-pulse pulse"></span> --}}
    @endif

    <div class="mission-item-inner {{ $mission->mode }}">
        <h4 class="mission-item-title">
            {{ $mission->display_name }}
        </h4>

        <h6 class="mission-item-author">
            By {{ $mission->user->username }} on {{ $mission->map->display_name }}
        </h6>

        <p class="mission-item-summary">
            @unless (strlen($mission->summary) == 0)
                {{ Str::limit($mission->summary, 80) }}
            @endunless
        </p>

        {{-- <span class="mission-item-mode mission-item-mode-{{ $mission->mode }}">
            {{ $mission->mode }}
        </span> --}}

        @if (!$mission->verified && ($mission->isMine() || (isset($isTester) && $isTester)))
            <span class="mission-item-verified" title="Not verified">!</span>
        @endif
    </div>
</a>
