<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SpaceHub')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="appShell" @keydown.escape.window="closeSidebar" class="bg-canvas text-ink min-h-screen antialiased">
    <div class="min-h-screen">
        <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="closeSidebar"
            class="fixed inset-0 z-40 bg-slate-950/45 lg:hidden" aria-hidden="true"></div>

        <aside id="app-sidebar"
            class="border-line bg-panel ease-smooth fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r transition-transform duration-200 lg:translate-x-0"
            :class="sidebarClass" :inert="sidebarInert" :aria-hidden="sidebarInert ? 'true' : 'false'"
            aria-label="Main navigation">
            <div class="border-line flex h-16 items-center gap-3 border-b px-5">
                <span class="bg-brand-600 grid size-8 place-items-center rounded-lg text-sm font-bold text-white"
                    aria-hidden="true">S</span>
                <a href="{{ route('bookings.index') }}"
                    class="text-ink text-base font-semibold tracking-tight">SpaceHub</a>
                <span
                    class="border-line text-ink-subtle ml-auto rounded-md border px-2 py-1 text-[10px] font-semibold uppercase tracking-wide">Workspace</span>
            </div>

            <div class="px-3 pt-6">
                <p class="text-ink-subtle px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.14em]">Workspace</p>
                <nav class="space-y-1">
                    @php
                        $navigation = [
                            [
                                'label' => 'Calendar',
                                'href' => route('bookings.index'),
                                'icon' => 'calendar',
                                'active' => request()->routeIs('bookings.index') && request('view') !== 'list',
                            ],
                            [
                                'label' => 'Rooms',
                                'href' => route('rooms.index'),
                                'icon' => 'building',
                                'active' => request()->routeIs('rooms.index'),
                            ],
                            [
                                'label' => 'Find a room',
                                'href' => route('bookings.index') . '#find-room',
                                'icon' => 'search',
                                'active' => request()->routeIs('rooms.available'),
                            ],
                            [
                                'label' => 'Bookings',
                                'href' => route('bookings.index', ['view' => 'list']),
                                'icon' => 'list',
                                'active' => request('view') === 'list',
                            ],
                        ];
                    @endphp
                    @foreach ($navigation as $item)
                        <a href="{{ $item['href'] }}" @class([
                            'group flex min-h-10 items-center gap-3 rounded-md px-3 text-sm font-medium transition-colors',
                            'bg-brand-50 text-brand-700 dark:text-brand-600' => $item['active'],
                            'text-ink-muted hover:bg-panel-muted hover:text-ink' => !$item['active'],
                        ]) @click="closeSidebar"
                            @if ($item['active']) aria-current="page" @endif>
                            @include('components.icon', [
                                'name' => $item['icon'],
                                'class' => 'size-[18px] shrink-0',
                            ])
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="border-line mt-auto border-t p-3">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                    <span
                        class="bg-brand-100 text-brand-700 dark:text-brand-700 grid size-9 shrink-0 place-items-center rounded-full text-xs font-semibold"
                        aria-hidden="true">AT</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-ink truncate text-sm font-medium">Workspace member</p>
                        <p class="text-ink-subtle text-xs">SpaceHub</p>
                    </div>
                    <button type="button" x-data="themeToggle" @click="cycle" :aria-label="label"
                        class="text-ink-muted hover:bg-panel-muted hover:text-ink grid size-9 place-items-center rounded-md transition-colors"
                        title="Change appearance">
                        @include('components.icon', ['name' => 'theme', 'class' => 'size-[18px]'])
                    </button>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-64" :inert="sidebarOpen && !isDesktop">
            <header
                class="border-line bg-panel/95 sticky top-0 z-30 flex h-16 items-center gap-3 border-b px-4 backdrop-blur sm:px-6 lg:px-8">
                <button type="button" @click="toggleSidebar" :aria-expanded="sidebarOpen" aria-controls="app-sidebar"
                    :aria-label="sidebarOpen ? 'Close navigation' : 'Open navigation'"
                    class="text-ink-muted hover:bg-panel-muted hover:text-ink grid size-10 place-items-center rounded-md lg:hidden">
                    @include('components.icon', ['name' => 'menu', 'class' => 'size-5'])
                </button>
                <div class="min-w-0 flex-1">
                    <h1 class="text-ink truncate text-sm font-semibold">@yield('page-title', 'Calendar')</h1>
                    @hasSection('header-context')
                        <p class="text-ink-subtle hidden text-xs sm:block">@yield('header-context')</p>
                    @endif
                </div>
                <span
                    class="border-line text-ink-muted hidden items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs sm:inline-flex">
                    <span class="bg-success-600 size-1.5 rounded-full"></span>
                    IST · {{ now()->format('H:i') }}
                </span>
                <a href="{{ route('bookings.index') }}#new-booking" data-open-booking
                    class="bg-brand-600 hover:bg-brand-700 inline-flex min-h-10 items-center gap-2 rounded-md px-3.5 text-sm font-semibold text-white shadow-sm transition-colors focus-visible:outline-offset-2">
                    @include('components.icon', ['name' => 'plus', 'class' => 'size-4'])
                    <span class="hidden sm:inline">New booking</span>
                    <span class="sm:hidden">New</span>
                </a>
            </header>

            <main id="main-content" class="mx-auto w-full max-w-[1440px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>
    <x-toast :message="$errors->any() ? $errors->first() : session('success')" :variant="$errors->any() ? 'danger' : 'success'" />
</body>

</html>
