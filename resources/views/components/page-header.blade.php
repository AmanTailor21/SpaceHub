@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<header {{ $attributes->class(['mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @if ($eyebrow)
            <p class="text-brand-700 dark:text-brand-600 mb-1.5 text-xs font-semibold uppercase tracking-[0.12em]">
                {{ $eyebrow }}</p>
        @endif
        <h2 class="text-ink text-2xl font-semibold tracking-tight sm:text-3xl">{{ $title }}</h2>
        @if ($description)
            <p class="text-ink-muted mt-2 max-w-2xl text-sm leading-6">{{ $description }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</header>
