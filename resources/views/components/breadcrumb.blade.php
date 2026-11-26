@props([
    'items',
])

<nav aria-label="Breadcrumb" class="mb-5">
    <ol class="flex flex-wrap items-center gap-2 text-xs text-ink-subtle">
        @foreach ($items as $item)
            @if (! $loop->first)
                <li aria-hidden="true">/</li>
            @endif
            <li>
                @if (! $loop->last && isset($item['href']))
                    <a href="{{ $item['href'] }}" class="transition-colors hover:text-ink">{{ $item['label'] }}</a>
                @else
                    <span @if ($loop->last) aria-current="page" class="font-medium text-ink-muted" @endif>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
