<div {{ $attributes->class(['overflow-x-auto rounded-lg border border-line bg-panel shadow-panel']) }}>
    <table class="w-full border-collapse text-left text-sm">
        {{ $slot }}
    </table>
</div>
