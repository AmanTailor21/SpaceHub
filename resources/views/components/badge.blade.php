@props([
    'variant' => 'neutral',
    'label' => null,
])

@php
    $badgeLabel = $label ?? match ($variant) {
        'success', 'confirmed', 'available' => 'Confirmed',
        'warning', 'in-use' => 'In use',
        'danger', 'cancelled' => 'Cancelled',
        'info' => 'Information',
        default => 'Neutral',
    };
    $tone = match ($variant) {
        'success', 'confirmed', 'available' => 'bg-success-50 text-success-600',
        'warning', 'in-use' => 'bg-warning-50 text-warning-600',
        'danger', 'cancelled' => 'bg-danger-50 text-danger-600',
        'info' => 'bg-info-50 text-info-600',
        default => 'bg-panel-muted text-ink-muted',
    };
@endphp

<span {{ $attributes->class(['inline-flex min-h-6 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium', $tone]) }}>
    <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $slot->isEmpty() ? $badgeLabel : $slot }}
</span>
