@props([
    'title' => null,
    'description' => null,
    'padding' => 'md',
])

<section
    {{ $attributes->class([
        'overflow-hidden rounded-lg border border-line bg-panel shadow-panel',
        'p-4 sm:p-5' => $padding === 'md',
        'p-5 sm:p-6' => $padding === 'lg',
        'p-0' => $padding === 'none',
    ]) }}>
    @if ($title || $description || isset($header))
        <header class="border-line flex items-start justify-between gap-4 border-b px-5 py-4">
            <div class="min-w-0">
                @if (isset($header))
                    {{ $header }}
                @else
                    @if ($title)
                        <h2 class="text-ink text-sm font-semibold">{{ $title }}</h2>
                    @endif
                    @if ($description)
                        <p class="text-ink-muted mt-1 text-sm">{{ $description }}</p>
                    @endif
                @endif
            </div>
            @isset($actions)
                <div class="shrink-0">{{ $actions }}</div>
            @endisset
        </header>
    @endif
    <div @class(['p-5' => $padding === 'none'])>
        {{ $slot }}
    </div>
</section>
