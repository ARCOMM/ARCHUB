<div class="container card-container mission-container {{ ($mission->banner() != '') ?: 'mission-without-banner' }}" style="margin-top: 6rem;">
    <h1 class="mission-title">
        {{ $mission->display_name }}
    </h1>

    <h3 class="mission-author">
        By {{ $mission->user->username }}
    </h3>

    <header class="mission-nav">
        <div class="subnav">
            <a class="subnav-link ripple {{ $panel == 'overview' ? 'active' : '' }}" wire:click="$set('panel', 'overview')">
                Overview
            </a>
            @if (!empty($mission->briefingFactions()))
                <a class="subnav-link ripple {{ $panel == 'briefings' ? 'active' : '' }}" wire:click="$set('panel', 'briefings')">
                    Briefings
                </a>
            @endif
            <a class="subnav-link ripple {{ $panel == 'media' ? 'active' : '' }}" wire:click="$set('panel', 'media')">Media</a>
        </div>
    </header>

    <div class="mission-inner">
        @switch($panel)
            @case('overview')
                @livewire('share.show-overview', ['mission' => $mission])
                @break
            @case('briefings')
                @livewire('share.show-briefings', ['mission' => $mission])
                @break
            @case('media')
                @livewire('share.show-media', ['mission' => $mission])
                @break
        @endswitch
    </div>
</div>
