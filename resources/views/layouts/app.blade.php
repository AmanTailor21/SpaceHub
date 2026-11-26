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
            <nav class="flex items-center gap-5 text-sm font-medium text-slate-600">
                <a href="{{ route('bookings.index') }}" class="hover:text-indigo-700">Bookings</a>
                <a href="{{ route('rooms.index') }}" class="hover:text-indigo-700">Rooms</a>
            </nav>
        </div>
    </header>
    <main class="mx-auto max-w-6xl px-6 py-10">
        @if (session('success'))
            <div role="status" class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
