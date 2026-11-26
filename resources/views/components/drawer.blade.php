@props(['id', 'title', 'description' => null])

<div x-data="dialog">
    <dialog id="{{ $id }}" x-ref="dialog" @spacehub:dialog-open.window="openDialog"
        @spacehub:dialog-close.window="closeDialog" @close="onDialogClose" aria-labelledby="{{ $id }}-title"
        @if ($description) aria-describedby="{{ $id }}-description" @endif
        class="spacehub-drawer border-line bg-panel text-ink shadow-drawer fixed inset-y-0 right-0 m-0 ml-auto h-dvh max-h-dvh w-full max-w-[30rem] overflow-y-auto border-0 border-l p-0 backdrop:bg-slate-950/45 backdrop:backdrop-blur-[2px] dark:backdrop:bg-black/65">
        <div class="flex min-h-full flex-col">
            <header
                class="border-line bg-panel/95 sticky top-0 z-10 flex items-start justify-between gap-4 border-b px-5 py-4 backdrop-blur">
                <div>
                    <h2 id="{{ $id }}-title" class="text-base font-semibold">{{ $title }}</h2>
                    @if ($description)
                        <p id="{{ $id }}-description" class="text-ink-muted mt-1 text-sm">{{ $description }}
                        </p>
                    @endif
                </div>
                <button type="button" @click="closeDialog" aria-label="Close drawer"
                    class="text-ink-muted hover:bg-panel-muted hover:text-ink grid size-9 shrink-0 place-items-center rounded-md">
                    @include('components.icon', ['name' => 'close', 'class' => 'size-4'])
                </button>
            </header>
            <div class="flex-1 p-5">{{ $slot }}</div>
        </div>
    </dialog>
</div>
