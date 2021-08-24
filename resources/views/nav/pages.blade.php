<a href="{{ url('/hub/missions') }}" class="nav-item nav-link hidden-sm-down active">Missions</a>

@can('manage-operations')
    <a href="{{ url('/hub/operations') }}" class="nav-item nav-link hidden-sm-down">
        Operations
    </a>
@endcan

@can('view-applications')
    <a href="{{ url('/hub/applications') }}" class="nav-item nav-link hidden-sm-down">
        Applications
    </a>
@endcan

@can('view-users')
    <a href="{{ url('/hub/users') }}" class="nav-item nav-link hidden-sm-down">
        Users
    </a>
@endcan

<li class="nav-item dropdown hidden-sm-down">
    <a class="nav-link dropdown-toggle" style="padding:0" data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
        Guides
    </a>

    <div class="dropdown-menu">
        <a
            href="https://github.com/arcomm/ARCORE/wiki/Chat-Commands"
            target="_newtab"
            class="dropdown-item">
            Chat Commands
        </a>

        <a
            href="https://docs.google.com/document/d/1PHaM-6em5acDyNg3PRfn2TXrGsgAZemZaVyEmChuVdM"
            target="_newtab"
            class="dropdown-item">
            Modset Installation
        </a>

        @foreach (Storage::disk('guides')->files() as $file)
            <a
                href="{{ url("/guides/$file") }}"
                class="dropdown-item">
                {{ rtrim($file, '.pdf') }}
            </a>
        @endforeach
    </div>
</li>

<li class="nav-item dropdown hidden-sm-down">
    <a class="nav-link dropdown-toggle" style="padding:0" data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
        Tutorials
    </a>

    <div class="dropdown-menu">
        @foreach (config('arcmf.videos') as $name => $url)
            <a
                href="{{ $url }}"
                class="dropdown-item">
                {{ $name }}
            </a>
        @endforeach
    </div>
</li>

<a href="{{ config('app.donate_url') }}" target="_newtab" class="nav-item nav-link hidden-sm-down">
    Donate
</a>

<li id="nav-pages" class="nav-item dropdown hidden-md-up m-l-0">
    <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bars"></i>
    </a>

    <div class="dropdown-menu" style="width: 25rem;max-height: 40rem;overflow-y: auto;right:auto;left:0;">
        <div class="list-group">
            <a
                href="{{ url('/hub/missions') }}"
                class="list-group-item list-group-item-action">
                Missions
            </a>

            @can('manage-operations')
                <a href="{{ url('/hub/operations') }}" class="list-group-item list-group-item-action">
                    Operations
                </a>
            @endcan

            @can('view-applications')
                <a href="{{ url('/hub/applications') }}" class="list-group-item list-group-item-action">
                    Applications
                </a>
            @endcan

            @can('view-users')
                <a href="{{ url('/hub/users') }}" class="list-group-item list-group-item-action">
                    Users
                </a>
            @endcan

            @yield('nav-left-mobile')
        </div>
    </div>
</li>
