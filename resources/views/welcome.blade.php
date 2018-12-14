<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- CSS Files -->
        <link href="/css/app.css" rel="stylesheet" />
    </head>
    <body style="background-color: #f4f3ef;">

        <div id="app">

            <uccx-dashboard></uccx-dashboard>

        </div>

        <script src="js/app.js"></script>

    </body>
</html>
