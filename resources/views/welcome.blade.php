<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{config('app.name', 'FEHighwayToHell')}}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-image: url("/img/indexWallpaper.png");
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            width: 100%;
            text-align: center;
            background-color: white;
        }

        .title {
            font-size: 84px;
        }

        .links a:hover {
            opacity: 0.5;
            filter: alpha(opacity=50);
        }

        .links  a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
            transition: opacity 1s;
            -webkit-transition: opacity 1s; /* Safari */
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title m-b-md">
            FEHighwayToHell
        </div>


        <div class="links">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}">Home</a>
                    <a href="{{ route('logout') }}">Logout</a>
                @else
                    <a href="{{ route('login') }}">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            @endif
            <a href="https://gitlab.com/dawsoncollege/php/f2018/section1/project3/team03/ass3">About</a>
        </div>
            </div>
        </div>
    </body>
</html>
