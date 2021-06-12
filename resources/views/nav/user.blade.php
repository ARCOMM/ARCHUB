<a href="https://github.com/ARCOMM/ARCMT/releases/latest/download/ARCMT.zip" class="nav-item nav-link hidden-sm-down">
    ARCMF
    <i class="material-icons" style="margin-top: 15px;float: right;font-size: 18px;margin-left: 5px;">file_download</i>
</a>

<li class="nav-item dropdown hidden-sm-down">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
        {{ auth()->user()->name }}
    </a>

    <div class="dropdown-menu">
        <a
            href="{{ url('/hub/settings') }}"
            class="dropdown-item">
            Settings
        </a>
    </div>
</li>
