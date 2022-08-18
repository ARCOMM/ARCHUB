<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @if (trim($__env->yieldContent('title')))
                {{ trim($__env->yieldContent('title')) }}
                —
            @endif
            {{ env('SITE_NAME', 'ARCHUB') }}
        </title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.css" rel="stylesheet"/>
        
        <link href="{{ url('/images/favicon.png') }}" rel="icon" type="image/png"></link>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet"></link>
        <script src="{{ mix('/js/app.js') }}" type="text/javascript"></script>

        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
            ]) !!};
        </script>

        @yield('head')
    </head>

    <body class="bd-docs">
        <div
            class="banner @yield('banner-classes')"
            style="background-image: url(@yield('banner'))">
            <div class="banner-fade-top"></div>
            <div class="banner-fade-bottom"></div>
        </div>

        <header
            @if (trim($__env->yieldContent('header-color')))
                class="header-colored header-{{ trim($__env->yieldContent('header-color')) }}"
            @endif>

            @include('navbar')

            @if (trim($__env->yieldContent('subnav')))
                <div class="subnav subnav-upper">
                    @yield('subnav')
                </div>
            @endif
        </header>

        <main>
            @if (app('request')->input('403'))
                <div class="alert alert-danger col-md-6 mb-3 mx-auto text-center" role="alert">
                    <strong>You're not a mission tester!</strong> You don't have the necessary permissions to access that.
                </div>
            @endif

            @yield('content')

            <div class="dynamic-modal"></div>
        </main>

        <script>
            $(document).ready(function(e) {
                $('body');
            });
        </script>
    </body>
</html>
