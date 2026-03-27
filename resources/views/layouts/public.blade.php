<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CareSmile Dental Clinic' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-light">
    <div id="app">
        {{-- Navbar --}}
        @include('components.navbar')

        {{-- Flash Messages --}}
        @include('components.flash-message')

        {{-- Validation Errors --}}
        @include('components.validation-errors')

        {{-- Page Content --}}
        <main class="main-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('components.footer')
    </div>

    {{-- Theme Toggle Script --}}
    <script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>
