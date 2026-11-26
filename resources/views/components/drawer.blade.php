@props([
    'id',
    'title',
    'description' => null,
])

<div x-data="dialog">
    <dialog
        id="{{ $id }}"
        x-ref="dialog"
        @spacehub:dialog-open.window="openDialog"
        @spacehub:dialog-close.window="closeDialog"
        @close="onDialogClose"
        aria-labelledby="{{ $id }}-title"
        @if ($description) aria-describedby="{{ $id }}-description" @endif
        class="spacehub-drawer fixed inset-y-0 right-0 m-0 ml-auto h-dvh max-h-dvh w-full max-w-[30rem] overflow-y-auto border-0 border-l border-line bg-panel p-0 text-ink shadow-drawer backdrop:bg-slate-950/45 backdrop:backdrop-blur-[2px] dark:backdrop:bg-black/65"
    >
        <div class="flex min-h-full flex-col">
            <header class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-line bg-panel/95 px-5 py-4 backdrop-blur">
                <div>
                    <h2 id="{{ $id }}-title" class="text-base font-semibold">{{ $title }}</h2>
                    @if ($description)<p id="{{ $id }}-description" class="mt-1 text-sm text-ink-muted">{{ $description }}</p>@endif
                </div>
                <button type="button" @click="closeDialog" aria-label="Close drawer" class="grid size-9 shrink-0 place-items-center rounded-md text-ink-muted hover:bg-panel-muted hover:text-ink">
                    @include('components.icon', ['name' => 'close', 'class' => 'size-4'])
                </button>
            </header>
            <div class="flex-1 p-5">{{ $slot }}</div>
        </div>
    </dialog>
</div>
