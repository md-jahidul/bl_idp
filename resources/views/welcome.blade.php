<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Animate Css -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
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

            .content {
                text-align: center;
            }

            .title {
                font-size: 30px;
            }

            .title h4{
                padding: 10px 0px;
                margin: 0
            }

            .title p{
                font-size: 15px;
                margin: 0px 150px
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
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

                    <img class="animated-css" src="{{ asset('images/home_logo.jpg') }}" alt="">
                    <h4>Banglalink IDP Server</h4>
                    <p>
                        Design to authenticate and authorize through single channel.
                    </p>
                    <div class="links">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}">Login</a>
                            @endauth
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>

        <script type="text/javascript">
            const element =  document.querySelector('.animated-css')
            element.classList.add('animated', 'pulse')    
        </script>
    </body>
</html>
