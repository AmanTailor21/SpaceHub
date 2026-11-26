@props([
    'name',
    'label',
    'hint' => null,
    'error' => null,
    'required' => false,
])

@php
    $fieldId = $attributes->get('id', $name);
    $fieldError = $error ?? $errors->first($name);
    $describedBy = trim(implode(' ', array_filter([
        $hint ? "{$fieldId}-hint" : null,
        $fieldError ? "{$fieldId}-error" : null,
        $attributes->get('aria-describedby'),
    ])));
@endphp

<div class="space-y-1.5">
    <label for="{{ $fieldId }}" class="block text-sm font-medium text-ink">
        {{ $label }}
        @if ($required)<span class="text-danger-600" aria-hidden="true">*</span>@endif
    </label>
    <select
        id="{{ $fieldId }}"
        name="{{ $name }}"
        @required($required)
        @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        @if ($fieldError) aria-invalid="true" @endif
        {{ $attributes->except(['id', 'aria-describedby'])->class([
            'min-h-10 w-full rounded-md border bg-panel px-3 py-2 text-sm text-ink shadow-sm transition-colors focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100',
            'border-danger-600' => $fieldError,
            'border-line-strong hover:border-ink-subtle' => ! $fieldError,
        ]) }}
    >
        {{ $slot }}
    </select>
    @if ($hint && ! $fieldError)
        <p id="{{ $fieldId }}-hint" class="text-xs text-ink-subtle">{{ $hint }}</p>
    @endif
    @if ($fieldError)
        <p id="{{ $fieldId }}-error" class="text-xs font-medium text-danger-600">{{ $fieldError }}</p>
    @endif
</div>
