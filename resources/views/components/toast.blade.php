@props([
    'message' => null,
    'variant' => 'success',
])

<div x-data="toast" x-init="init" @spacehub:toast.window="showToast" x-cloak x-show="visible"
    x-transition.opacity.duration.150ms data-initial-message="{{ $message }}" data-variant="{{ $variant }}"
    class="border-line bg-panel text-ink shadow-popover fixed bottom-4 right-4 z-[100] flex w-[min(28rem,calc(100vw-2rem))] items-start gap-3 rounded-lg border p-4 text-sm"
    :role="variant === 'danger' ? 'alert' : 'status'" :aria-live="variant === 'danger' ? 'assertive' : 'polite'"
    aria-atomic="true">
    <span x-show="variant === 'danger'">@include('components.icon', ['name' => 'alert', 'class' => 'mt-0.5 size-4 shrink-0 text-danger-600'])</span>
    <span x-show="variant !== 'danger'">@include('components.icon', ['name' => 'check', 'class' => 'mt-0.5 size-4 shrink-0 text-success-600'])</span>
    <p class="min-w-0 flex-1"><span x-text="message">{{ $message ?? '' }}</span></p>
    <button type="button" @click="dismiss"
        class="text-ink-subtle hover:bg-panel-muted hover:text-ink -mr-1 -mt-1 grid size-8 shrink-0 place-items-center rounded-md"
        aria-label="Dismiss notification">
        @include('components.icon', ['name' => 'close', 'class' => 'size-4'])
    </button>
</div>
