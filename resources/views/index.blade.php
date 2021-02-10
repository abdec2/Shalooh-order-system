<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Shalooh Order System') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body class="font-sans antialiased bg-gray-200">
    <div class="container mx-auto">
        <header class="text-gray-600 body-font">
            <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                @if (Route::has('login'))
                <nav class="md:ml-auto flex flex-wrap items-center text-base justify-center">
                    @auth
                    <a href="{{ url('/dashboard') }}" class="mr-5 hover:text-gray-900">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="mr-5 hover:text-gray-900">Login</a>

                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="mr-5 hover:text-gray-900">Register</a>
                    @endif
                    @endauth
                </nav>
                @endif
            </div>
        </header>

        <section class="text-gray-600 body-font">
            <div class="container mx-auto flex px-5 py-24 items-center justify-center flex-col">
                <img class="w-100 mt-40" src="{{ asset('img/logo.png') }}" alt="">
                <div class="text-center mt-5 lg:w-2/3 w-full">
                    <h1 class="title-font sm:text-4xl text-3xl mb-4 font-medium text-gray-900">Order Management System</h1>
                </div>
            </div>
        </section>
    </div>
</body>

</html>