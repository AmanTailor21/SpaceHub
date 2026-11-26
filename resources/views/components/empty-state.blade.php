@props([
    'title',
    'description' => null,
    'icon' => 'calendar',
])

<div {{ $attributes->class(['flex min-h-56 flex-col items-center justify-center rounded-lg border border-dashed border-line-strong bg-panel px-6 py-10 text-center']) }}>
    <span class="mb-4 grid size-11 place-items-center rounded-full bg-panel-muted text-ink-muted">
        @include('components.icon', ['name' => $icon, 'class' => 'size-5'])
    </span>
    <h3 class="text-sm font-semibold text-ink">{{ $title }}</h3>
    @if ($description)<p class="mt-1 max-w-md text-sm leading-6 text-ink-muted">{{ $description }}</p>@endif
    @if ($slot->isNotEmpty())<div class="mt-5">{{ $slot }}</div>@endif
</div>
