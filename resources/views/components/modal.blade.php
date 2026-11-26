@props(['id', 'title', 'description' => null, 'size' => 'md'])

<div x-data="dialog">
    <dialog id="{{ $id }}" x-ref="dialog" @spacehub:dialog-open.window="openDialog"
        @spacehub:dialog-close.window="closeDialog" @close="onDialogClose" aria-labelledby="{{ $id }}-title"
        @if ($description) aria-describedby="{{ $id }}-description" @endif
        @class([
            'm-auto max-h-[min(90dvh,48rem)] w-[calc(100%-2rem)] overflow-y-auto rounded-xl border border-line bg-panel p-0 text-ink shadow-popover backdrop:bg-slate-950/45 backdrop:backdrop-blur-[2px] dark:backdrop:bg-black/65',
            'max-w-sm' => $size === 'sm',
            'max-w-3xl' => $size === 'lg',
            'max-w-xl' => $size === 'md',
        ])>
        <header class="border-line flex items-start justify-between gap-4 border-b px-5 py-4">
            <div>
                <h2 id="{{ $id }}-title" class="text-base font-semibold">{{ $title }}</h2>
                @if ($description)
                    <p id="{{ $id }}-description" class="text-ink-muted mt-1 text-sm">{{ $description }}</p>
                @endif
            </div>
            <button type="button" @click="closeDialog" aria-label="Close dialog"
                class="text-ink-muted hover:bg-panel-muted hover:text-ink grid size-9 shrink-0 place-items-center rounded-md">
                @include('components.icon', ['name' => 'close', 'class' => 'size-4'])
            </button>
        </header>
        <div class="p-5">{{ $slot }}</div>
    </dialog>
</div>
