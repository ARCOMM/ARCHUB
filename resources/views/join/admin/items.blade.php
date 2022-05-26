@if ($joinRequests->isEmpty())
    <p class="pa-3 text-muted">No pending applications.</p>
@else
    <div class="list-group list-group-flush">
        @foreach ($joinRequests as $jr)
            <a
                href="{{ url('/hub/applications/show/' . $jr->id) }}"
                class="list-group-item list-group-item-action jr-item"
                data-id="{{ $jr->id }}">

                <span class="jr-item-title">{{ $jr->name }}</span>
                <br />

                <span class="jr-item-meta">
                    {{ $jr->age }}, {{ $jr->location }} &middot; {{ $jr->created_at->diffForHumans() }}
                </span>
            </a>
        @endforeach
    </div>
@endif
