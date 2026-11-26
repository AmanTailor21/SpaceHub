<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SpaceHub')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="appShell" @keydown.escape.window="closeSidebar" class="min-h-screen bg-canvas text-ink antialiased">
    <div class="min-h-screen">
        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            @click="closeSidebar"
            class="fixed inset-0 z-40 bg-slate-950/45 lg:hidden"
            aria-hidden="true"
        ></div>

        <aside
            id="app-sidebar"
            class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-line bg-panel transition-transform duration-200 ease-smooth lg:translate-x-0"
            :class="sidebarClass"
            :inert="sidebarInert"
            :aria-hidden="sidebarInert ? 'true' : 'false'"
            aria-label="Main navigation"
        >
            <div class="flex h-16 items-center gap-3 border-b border-line px-5">
                <span class="grid size-8 place-items-center rounded-lg bg-brand-600 text-sm font-bold text-white" aria-hidden="true">S</span>
                <a href="{{ route('bookings.index') }}" class="text-base font-semibold tracking-tight text-ink">SpaceHub</a>
                <span class="ml-auto rounded-md border border-line px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-ink-subtle">Workspace</span>
            </div>

            <div class="px-3 pt-6">
                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.14em] text-ink-subtle">Workspace</p>
                <nav class="space-y-1">
                    @php
                        $navigation = [
                            ['label' => 'Calendar', 'href' => route('bookings.index'), 'icon' => 'calendar', 'active' => request()->routeIs('bookings.index') && request('view') !== 'list'],
                            ['label' => 'Rooms', 'href' => route('rooms.index'), 'icon' => 'building', 'active' => request()->routeIs('rooms.index')],
                            ['label' => 'Find a room', 'href' => route('bookings.index') . '#find-room', 'icon' => 'search', 'active' => request()->routeIs('rooms.available')],
                            ['label' => 'Bookings', 'href' => route('bookings.index', ['view' => 'list']), 'icon' => 'list', 'active' => request('view') === 'list'],
                        ];
                    @endphp
                    @foreach ($navigation as $item)
                        <a
                            href="{{ $item['href'] }}"
                            @class([
                                'group flex min-h-10 items-center gap-3 rounded-md px-3 text-sm font-medium transition-colors',
                                'bg-brand-50 text-brand-700 dark:text-brand-600' => $item['active'],
                                'text-ink-muted hover:bg-panel-muted hover:text-ink' => ! $item['active'],
                            ])
                            @click="closeSidebar"
                            @if ($item['active']) aria-current="page" @endif
                        >
                            @include('components.icon', ['name' => $item['icon'], 'class' => 'size-[18px] shrink-0'])
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="mt-auto border-t border-line p-3">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                    <span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700 dark:text-brand-700" aria-hidden="true">AT</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-ink">Workspace member</p>
                        <p class="text-xs text-ink-subtle">SpaceHub</p>
                    </div>
                    <button
                        type="button"
                        x-data="themeToggle"
                        @click="cycle"
                        :aria-label="label"
                        class="grid size-9 place-items-center rounded-md text-ink-muted transition-colors hover:bg-panel-muted hover:text-ink"
                        title="Change appearance"
                    >
                        @include('components.icon', ['name' => 'theme', 'class' => 'size-[18px]'])
                    </button>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-64" :inert="sidebarOpen && !isDesktop">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-line bg-panel/95 px-4 backdrop-blur sm:px-6 lg:px-8">
                <button
                    type="button"
                    @click="toggleSidebar"
                    :aria-expanded="sidebarOpen"
                    aria-controls="app-sidebar"
                    :aria-label="sidebarOpen ? 'Close navigation' : 'Open navigation'"
                    class="grid size-10 place-items-center rounded-md text-ink-muted hover:bg-panel-muted hover:text-ink lg:hidden"
                >
                    @include('components.icon', ['name' => 'menu', 'class' => 'size-5'])
                </button>
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-sm font-semibold text-ink">@yield('page-title', 'Calendar')</h1>
                    @hasSection('header-context')
                        <p class="hidden text-xs text-ink-subtle sm:block">@yield('header-context')</p>
                    @endif
                </div>
                <span class="hidden items-center gap-1.5 rounded-full border border-line px-2.5 py-1 text-xs text-ink-muted sm:inline-flex">
                    <span class="size-1.5 rounded-full bg-success-600"></span>
                    IST · {{ now()->format('H:i') }}
                </span>
                <a
                    href="{{ route('bookings.index') }}#new-booking"
                    data-open-booking
                    class="inline-flex min-h-10 items-center gap-2 rounded-md bg-brand-600 px-3.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 focus-visible:outline-offset-2"
                >
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
    <x-toast
        :message="$errors->any() ? $errors->first() : session('success')"
        :variant="$errors->any() ? 'danger' : 'success'"
    />
</body>
</html>
