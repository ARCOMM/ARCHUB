<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
        <meta name="viewport" content="width=device-width">

        <title>
            @if (trim($__env->yieldContent('title')))
                @yield('title')
                &mdash;
            @endif
            {{ env('SITE_NAME_PUBLIC', 'ARCOMM') }}
        </title>

        <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.css" rel="stylesheet"/>

        <link rel="icon" type="image/png" href="{{ url('/images/favicon.png') }}">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
        <link rel="stylesheet" href="{{ mix('/css/public.css') }}">

        @yield('head')
    </head>

    <body class="landing-page">
        <main id="app">
            <nav class="navbar navbar-expand-lg navbar-light navbar-transparent" role="navigation">
                <div class="container">
                    <a class="navbar-brand">
                        <img src="{{ url('/images/logo-white-full.png') }}" alt="Logo" width="130" height="130" style="background: #bd2c2c; padding: 18px;">
                    </a>

                    <div class="navbar-nav">
                        @if (auth()->guest())
                            <li>
                                <a href="{{ url('/auth/redirect') }}" style="padding-top: 8px">
                                    <span>Login</span>
                                    <img style="width: 16px" src="{{ url('/images/discord.png') }}" alt="Discord">
                                </a>
                            </li>
                        @else
                            @can('access-hub')
                                <a class="nav-item" href="{{ url('/hub') }}">Hub</a>
                            @endcan
                        @endif
                        <a class="nav-item" href="{{ url('join') }}">Join</a>
                        <a class="nav-item" href="{{ url('/media') }}">Media</a>
                        <a class="nav-item" href="{{ url('/roster') }}">Roster</a>  
                    </div>
                </div>
            </nav>

            @yield('content')
        </main>
    </body>

    @yield('scripts')
</html>
