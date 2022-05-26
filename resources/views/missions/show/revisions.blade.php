<li class="nav-item dropdown hidden-sm-down">
    <a class="nav-link dropdown-toggle" id="revisionDropdown" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-history"></i>
    </a>

    <ul class="dropdown-menu" aria-labelledby="revisionDropdown" style="width: 20rem">
        <div class="list-group float-start w-100">
            @if ($mission->revisions->isEmpty())
                <li><p class="text-center text-muted p-y-2 m-b-0">No revisions have been made!</p></li>
            @else
                @foreach ($mission->revisions as $revision)
                    <li><a
                        href="javascript:void(0)"
                        class="list-group-item notification-item"
                        title="{{ $revision->created_at }}"
                        style="text-decoration: none !important;cursor: default;">
                        <span class="notification-image" style="'background-image: url('{{ $revision->user->avatar }}')"></span>

                        <p class="list-group-item-text notification-text">
                            Updated by {{ $revision->user->username }}
                        </p>

                        <p class="list-group-item-text notification-subtext">
                            {{ $revision->created_at->diffForHumans() }}
                        </p>
                    </a></li>
                @endforeach
            @endif
        </div>
    </ul>
</li>
