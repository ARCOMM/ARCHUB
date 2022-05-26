<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #bd2c2c !important;">
    <div class="container-fluid">
        <div class="navbar-nav">
            <a href="{{ url('/hub') }}" class="nav-link hidden-sm-down">
                <img id="logo-white" src="{{ url('/images/logo.png') }}">
            </a>

            <a href="{{ url('/hub/missions') }}" class="nav-link hidden-sm-down active">Missions</a>
            @can('view-applications')
                <a href="{{ url('/hub/applications') }}" class="nav-link hidden-sm-down">Applications</a>
            @endcan
            @can('view-users')
                <a href="{{ url('/hub/users') }}" class="nav-link hidden-sm-down">Users</a>
            @endcan
            <li class="nav-item dropdown hidden-sm-down">
                <a class="nav-link dropdown-toggle" id="guidesDropdown" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
                    Guides
                </a>

                <ul class="dropdown-menu" aria-labelledby="guidesDropdown">
                    <li><a class="dropdown-item" href="https://github.com/arcomm/ARCORE/wiki/Chat-Commands" target="_newtab">Chat Commands</a></li>
                    <li><a class="dropdown-item" href="https://docs.google.com/document/d/1PHaM-6em5acDyNg3PRfn2TXrGsgAZemZaVyEmChuVdM" target="_newtab">Modset Installation</a></li>

                    @foreach (Storage::disk('guides')->files() as $file)
                        <li><a class="dropdown-item" href="{{ url("/guides/$file") }}">{{ rtrim($file, '.pdf') }}</a></li>
                    @endforeach
                </ul>
            </li>

            <li class="nav-item dropdown hidden-sm-down">
                <a class="nav-link dropdown-toggle" id="tutorialsDropdown" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
                    Tutorials
                </a>

                <ul class="dropdown-menu" aria-labelledby="tutorialsDropdown">
                    @foreach (config('arcmf.videos') as $name => $url)
                        <a class="dropdown-item" href="{{ $url }}">{{ $name }}</a>
                    @endforeach
                </ul>
            </li>

            <a class="nav-link hidden-sm-down" href="{{ config('app.donate_url') }}" target="_newtab">Donate</a>
            @yield('nav-left')
        </div>
        
        <div class="navbar-nav">
            @yield('nav-right')
            <a class="nav-link hidden-sm-down" href="https://github.com/ARCOMM/ARCMT/releases/latest/download/ARCMT.zip">
                ARCMF
                <i class="material-icons" style="margin-top: 15px;float: right;font-size: 18px;margin-left: 5px;">file_download</i>
            </a>

            <li class="nav-item dropdown hidden-sm-down">
                <a class="nav-link dropdown-toggle" id="settingsDropdown" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
                    {{ auth()->user()->username }}
                </a>

                <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                    <li><a href="{{ url('/hub/settings') }}" class="dropdown-item">Settings</a></li>
                </ul>
            </li>
        </div>
    </div>
</nav>