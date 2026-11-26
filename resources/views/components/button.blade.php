@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'loading' => false,
])

<button type="{{ $type }}" @disabled($loading || $attributes->has('disabled')) aria-busy="{{ $loading ? 'true' : 'false' }}"
    {{ $attributes->class([
        'inline-flex shrink-0 items-center justify-center gap-2 rounded-md font-semibold transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 disabled:pointer-events-none disabled:opacity-50',
        'min-h-10 px-4 text-sm' => $size === 'md',
        'min-h-9 px-3 text-sm' => $size === 'sm',
        'min-h-11 px-5 text-sm' => $size === 'lg',
        'bg-brand-600 text-white shadow-sm hover:bg-brand-700 focus-visible:outline-brand-500' => $variant === 'primary',
        'border border-line bg-panel text-ink shadow-sm hover:bg-panel-muted focus-visible:outline-brand-500' =>
            $variant === 'secondary',
        'text-ink-muted hover:bg-panel-muted hover:text-ink focus-visible:outline-brand-500' => $variant === 'ghost',
        'bg-danger-600 text-white hover:bg-red-700 focus-visible:outline-danger-600' => $variant === 'danger',
        'text-brand-700 hover:bg-brand-50 dark:text-brand-600 focus-visible:outline-brand-500' => $variant === 'link',
    ]) }}>
    @if ($loading)
        <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" />
            <path class="opacity-75" fill="currentColor" d="M12 3a9 9 0 0 1 9 9h-3a6 6 0 0 0-6-6V3Z" />
        </svg>
    @endif
    {{ $slot }}
</button>
