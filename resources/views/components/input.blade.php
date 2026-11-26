@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'error' => null, 'required' => false])

@php
    $fieldId = $attributes->get('id', $name);
    $fieldError = $error ?? $errors->first($name);
    $describedBy = trim(
        implode(
            ' ',
            array_filter([
                $hint ? "{$fieldId}-hint" : null,
                $fieldError ? "{$fieldId}-error" : null,
                $attributes->get('aria-describedby'),
            ]),
        ),
    );
@endphp

<div class="space-y-1.5">
    <label for="{{ $fieldId }}" class="text-ink block text-sm font-medium">
        {{ $label }}
        @if ($required)
            <span class="text-danger-600" aria-hidden="true">*</span>
        @endif
    </label>
    <input id="{{ $fieldId }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
        @required($required) @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        @if ($fieldError) aria-invalid="true" @endif
        {{ $attributes->except(['id', 'aria-describedby'])->class([
                'min-h-10 w-full rounded-md border bg-panel px-3 py-2 text-sm text-ink shadow-sm transition-colors placeholder:text-ink-subtle focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100 dark:focus:ring-brand-100',
                'border-danger-600' => $fieldError,
                'border-line-strong hover:border-ink-subtle' => !$fieldError,
            ]) }}>
    @if ($hint && !$fieldError)
        <p id="{{ $fieldId }}-hint" class="text-ink-subtle text-xs">{{ $hint }}</p>
    @endif
    @if ($fieldError)
        <p id="{{ $fieldId }}-error" class="text-danger-600 text-xs font-medium">{{ $fieldError }}</p>
    @endif
</div>
