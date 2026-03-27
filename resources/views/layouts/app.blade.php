<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CareSmile Dental Clinic' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="theme-light">
    <div id="app" class="d-flex">
        {{-- Sidebar --}}
        @auth
            @include('components.sidebar')
        @endauth

        {{-- Main Content Area --}}
        <div class="flex-grow-1 @auth() @else w-100 @endauth">
            {{-- Navbar --}}
            @include('components.navbar')

            {{-- Flash Messages --}}
            @include('components.flash-message')

            {{-- Validation Errors --}}
            @include('components.validation-errors')

            {{-- Page Content --}}
            <main class="py-4">
                <div class="container">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            @include('components.footer')
        </div>
    </div>

    {{-- Theme Toggle Script --}}
    <script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>
