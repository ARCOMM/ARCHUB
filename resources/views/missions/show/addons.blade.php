@foreach ($mission->addonWarnings as $addon)
    @if ($mission->hasAddon($addon))
        <div class="alert alert-warning my-2" role="alert">
            <strong>Editor contains {{ strtoupper($addon) }}</strong><br />
            This mission's assets need to be updated.
        </div>
    @endif
@endforeach

<div class="list-group list-group-flush">
    @foreach ($mission->addons() as $addon)
        <a
            href="javascript:void(0)"
            class="list-group-item list-group-item-action jr-item">

            <span class="jr-item-meta">{{ $addon }}</span>
        </a>
    @endforeach
</div>
