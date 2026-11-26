@props([
    'align' => 'right',
])

<details class="group relative">
    <summary class="flex cursor-pointer list-none items-center gap-2 [&::-webkit-details-marker]:hidden">
        {{ $trigger }}
        <span class="sr-only">Toggle menu</span>
    </summary>
    <div @class([
        'absolute top-full z-50 mt-2 min-w-48 rounded-lg border border-line bg-panel p-1.5 shadow-popover',
        'right-0' => $align === 'right',
        'left-0' => $align === 'left',
    ])>
        {{ $slot }}
    </div>
</details>
