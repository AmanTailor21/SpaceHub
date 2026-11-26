<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="{{ $class ?? 'size-5' }}" aria-hidden="true">
    @switch($name)
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M16 3v4M8 3v4M3 10h18" />
        @break

        @case('building')
            <path d="M4 21V5l8-2v18M4 21h16M16 21V9l4 2v10M8 8h1m-1 4h1m-1 4h1m6-4h1m-1 4h1" />
        @break

        @case('search')
            <circle cx="10.8" cy="10.8" r="6.8" />
            <path d="m16 16 4 4" />
        @break

        @case('list')
            <path d="M9 6h11M9 12h11M9 18h11M4 6h.01M4 12h.01M4 18h.01" />
        @break

        @case('theme')
            <path d="M12 3a9 9 0 1 0 9 9c0-.6-.05-1.19-.17-1.76A7 7 0 0 1 12 3Z" />
            <path d="M16.5 3.8v4.4m-2.2-2.2h4.4" />
        @break

        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16" />
        @break

        @case('plus')
            <path d="M12 5v14m-7-7h14" />
        @break

        @case('chevron-left')
            <path d="m15 18-6-6 6-6" />
        @break

        @case('chevron-right')
            <path d="m9 18 6-6-6-6" />
        @break

        @case('close')
            <path d="m18 6-12 12M6 6l12 12" />
        @break

        @case('clock')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7v5l3 2" />
        @break

        @case('filter')
            <path d="M4 6h16M7 12h10m-7 6h4" />
        @break

        @case('check')
            <path d="m5 12 4 4L19 6" />
        @break

        @case('alert')
            <path d="M12 3 2.8 19h18.4L12 3Z" />
            <path d="M12 9v4m0 3h.01" />
        @break
    @endswitch
</svg>
