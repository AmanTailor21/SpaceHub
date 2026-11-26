@props(['title', 'description' => null, 'icon' => 'calendar'])

<div
    {{ $attributes->class(['flex min-h-56 flex-col items-center justify-center rounded-lg border border-dashed border-line-strong bg-panel px-6 py-10 text-center']) }}>
    <span class="bg-panel-muted text-ink-muted mb-4 grid size-11 place-items-center rounded-full">
        @include('components.icon', ['name' => $icon, 'class' => 'size-5'])
    </span>
    <h3 class="text-ink text-sm font-semibold">{{ $title }}</h3>
    @if ($description)
        <p class="text-ink-muted mt-1 max-w-md text-sm leading-6">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
