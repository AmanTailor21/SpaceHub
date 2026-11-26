@props([
    'items',
    'active' => null,
])

<nav {{ $attributes->class(['flex gap-1 overflow-x-auto border-b border-line']) }} aria-label="Page sections">
    @foreach ($items as $item)
        @php
            $isActive = ($active ?? request()->fullUrl()) === ($item['value'] ?? $item['href']);
        @endphp
        <a
            href="{{ $item['href'] }}"
            @if ($isActive) aria-current="page" @endif
            @class([
                'relative -mb-px inline-flex min-h-11 shrink-0 items-center px-3 text-sm font-medium transition-colors',
                'border-b-2 border-brand-600 text-brand-700 dark:text-brand-600' => $isActive,
                'border-b-2 border-transparent text-ink-muted hover:text-ink' => ! $isActive,
            ])
        >{{ $item['label'] }}</a>
    @endforeach
</nav>
