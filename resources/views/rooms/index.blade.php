@extends('layouts.app')

@section('title', 'Meeting rooms · SpaceHub')
@section('page-title', 'Rooms')
@section('header-context', 'Capacity and live room availability')

@section('content')
    <x-page-header
        eyebrow="Space directory"
        title="Meeting rooms"
        description="Compare room capacity, location, and current availability."
    >
        <a href="{{ route('bookings.index') }}#find-room" class="inline-flex min-h-10 items-center justify-center rounded-md bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">Find a room</a>
    </x-page-header>

    <x-card padding="none" class="mb-5">
        <form method="GET" action="{{ route('rooms.index') }}" class="grid gap-3 p-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end sm:px-5">
            <x-select name="floor" label="Floor">
                <option value="">All floors</option>
                @foreach ($floors as $floorOption)
                    <option value="{{ $floorOption }}" @selected($floor === $floorOption)>{{ $floorOption }}</option>
                @endforeach
            </x-select>
            <x-input name="capacity" label="Minimum capacity" type="number" min="1" :value="$capacity" placeholder="Any group size" />
            <x-button type="submit" variant="secondary">Apply filters</x-button>
        </form>
    </x-card>

    @if ($rooms->isEmpty())
        <x-empty-state
            title="No rooms match those filters"
            description="Try a different floor or lower the minimum capacity."
            icon="building"
        >
            <a href="{{ route('rooms.index') }}" class="text-sm font-semibold text-brand-700 dark:text-brand-600">Clear filters</a>
        </x-empty-state>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($rooms as $room)
                @php
                    $currentBooking = $room->bookings->first();
                @endphp
                <article class="rounded-lg border border-line bg-panel p-5 shadow-panel">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold text-ink">{{ $room->name }}</h2>
                            <p class="mt-1 text-sm text-ink-muted">{{ $room->floor }}</p>
                        </div>
                        @if ($currentBooking)
                            <x-badge variant="in-use">In use until {{ $currentBooking->end_time->format('H:i') }}</x-badge>
                        @else
                            <x-badge variant="available">Available</x-badge>
                        @endif
                    </div>
                    <div class="mt-6 flex items-end justify-between border-t border-line pt-4">
                        <p class="text-sm text-ink-muted">
                            <span class="block text-2xl font-semibold text-ink">{{ $room->capacity }}</span>
                            seats
                        </p>
                        <a
                            href="{{ route('bookings.index', ['date' => now()->toDateString(), 'room_id' => $room->id]) }}"
                            class="inline-flex min-h-9 items-center gap-1.5 rounded-md px-2 text-sm font-semibold text-brand-700 hover:bg-brand-50 dark:text-brand-600"
                        >
                            @include('components.icon', ['name' => 'calendar', 'class' => 'size-4'])
                            View schedule
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
