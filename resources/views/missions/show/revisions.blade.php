<li class="nav-item dropdown hidden-sm-down">
    <a class="nav-link dropdown-toggle" id="revisionDropdown" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-history"></i>
    </a>

    <ul class="dropdown-menu" aria-labelledby="revisionDropdown" style="width: 20rem">
        <div class="list-group list-group-flush float-start w-100">
            @if ($mission->revisions->isEmpty())
                <li><p class="text-center text-muted py-2 mb-0">No revisions have been made!</p></li>
            @else
                @foreach ($mission->revisions as $revision)
                    <li class="list-group-item-text my-1" title="{{ $revision->created_at }}" style="text-decoration: none !important;cursor: default;">
                        <div class="ms-3 me-auto">
                            <div class="fw-bold">{{ $revision->user->username }}</div>
                            {{ $revision->created_at->diffForHumans() }}
                        </div>
                    </li>
                @endforeach
            @endif
        </div>
    </ul>
</li>
