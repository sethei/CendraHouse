<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CendraHouse - @yield('title')</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <!--Relació absoluta i perque carregui sempre el css-->
    <link rel="stylesheet" href="{{ url('/static/css/connect.css?v=.time()') }}">
    <script src="https://kit.fontawesome.com/34547ed440.js" crossorigin="anonymous"></script>
</head>
<body>
    @section('content')
    hola mundo
    @show
</body>
</html>