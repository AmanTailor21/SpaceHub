@props([
    'message' => null,
    'variant' => 'success',
])

<div
    x-data="toast"
    @spacehub:toast.window="showToast"
    x-cloak
    x-show="visible"
    x-transition.opacity.duration.150ms
    class="fixed bottom-4 right-4 z-[100] flex w-[min(28rem,calc(100vw-2rem))] items-start gap-3 rounded-lg border border-line bg-panel p-4 text-sm text-ink shadow-popover"
    role="status"
    aria-live="polite"
    aria-atomic="true"
>
    @if ($variant === 'danger')
        @include('components.icon', ['name' => 'alert', 'class' => 'mt-0.5 size-4 shrink-0 text-danger-600'])
    @else
        @include('components.icon', ['name' => 'check', 'class' => 'mt-0.5 size-4 shrink-0 text-success-600'])
    @endif
    <p class="min-w-0 flex-1">{{ $message ?? '' }}<span x-text="message"></span></p>
    <button type="button" @click="dismiss" class="-mr-1 -mt-1 grid size-8 shrink-0 place-items-center rounded-md text-ink-subtle hover:bg-panel-muted hover:text-ink" aria-label="Dismiss notification">
        @include('components.icon', ['name' => 'close', 'class' => 'size-4'])
    </button>
</div>
