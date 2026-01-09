<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PUP Library Portal</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing_page.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Base background to reduce bright flashes */
        body {
            background-color: #1a1a1a; /* dark gray base color */
            margin: 0;
            padding: 0;
        }

        /* Persistent left-side background */
        .left-side {
            background: url("{{ asset('img/pup_school.JPG') }}") center center / cover no-repeat;
            background-size: cover;
            background-attachment: fixed;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="left-side"></div>

    @yield('content')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
