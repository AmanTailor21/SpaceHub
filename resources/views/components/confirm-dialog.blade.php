@props([
    'id',
    'title' => 'Confirm action',
    'confirmLabel' => 'Confirm',
    'variant' => 'danger',
])

<div x-data="dialog">
    <dialog
        id="{{ $id }}"
        x-ref="dialog"
        @spacehub:dialog-open.window="openDialog"
        @spacehub:dialog-close.window="closeDialog"
        @close="onDialogClose"
        aria-labelledby="{{ $id }}-title"
        class="m-auto w-[calc(100%-2rem)] max-w-md rounded-xl border border-line bg-panel p-0 text-ink shadow-popover backdrop:bg-slate-950/45 dark:backdrop:bg-black/65"
    >
        <div class="p-5">
            <h2 id="{{ $id }}-title" class="text-base font-semibold">{{ $title }}</h2>
            <div class="mt-2 text-sm leading-6 text-ink-muted">{{ $slot }}</div>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" @click="closeDialog">Keep it</x-button>
                <x-button :variant="$variant" data-confirm-accept>{{ $confirmLabel }}</x-button>
            </div>
        </div>
    </dialog>
</div>
