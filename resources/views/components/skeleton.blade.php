@props([
    'lines' => 3,
])

<div {{ $attributes->class(['animate-pulse space-y-3']) }} aria-hidden="true">
    @for ($line = 0; $line < $lines; $line++)
        <div @class([
            'h-3 rounded bg-panel-muted',
            'w-full' => $line === 0,
            'w-4/5' => $line % 2 === 1,
            'w-3/5' => $line > 0 && $line % 2 === 0,
        ])></div>
    @endfor
</div>
