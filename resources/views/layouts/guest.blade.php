<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'CareSmile Dental Clinic') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color: #f4f7f6; font-family: 'Inter', sans-serif;">
    
    {{-- This creates a full-screen, centered layout, but lets the child page decide its own width --}}
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-4 px-3 w-100">
        
        {{-- Your beautiful new split-screen login gets dropped right here! --}}
        {{ $slot }}
        
    </div>

</body>
</html>