<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SpaceHub')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="{{ route('rooms.index') }}" class="text-xl font-bold tracking-tight text-indigo-700">SpaceHub</a>
            <span class="text-sm text-slate-600">Meeting room bookings</span>
        </div>
    </header>
    <main class="mx-auto max-w-6xl px-6 py-10">
        @yield('content')
    </main>
</body>
</html>
