<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'خدمة التلمذة') }}</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" href="{{ asset('icons/icon.png') }}">

    <!-- Fonts: Switched to Cairo for consistency -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <div class="flex flex-col items-center min-h-screen pt-6 sm:justify-center sm:pt-0">
        <x-application-logo class="w-20 h-20 text-gray-500 fill-current" />
        <div class="w-full px-6 py-4 mt-6 overflow-hidden bg-white rounded-lg shadow-xl sm:max-w-md">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
