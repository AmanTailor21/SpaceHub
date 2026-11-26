@props([
    'name',
    'label',
    'type' => 'time',
    'value' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
])

<x-input
    :name="$name"
    :label="$label"
    :type="$type"
    :value="$value"
    :hint="$hint"
    :error="$error"
    :required="$required"
    {{ $attributes }}
/>
