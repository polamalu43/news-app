<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Newsアプリ</title>

        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>

    <body class="antialiased">
        <div id="app">
            <app-vue
                :auth-status='{{ json_encode($authStatus) }}'
                :liked-by-user='{{ json_encode($likedByUser) }}'
             />
        </div>
    </div>
    </body>
</html>
