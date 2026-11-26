@extends('layouts.app')

@section('title', 'Find a room · SpaceHub')
@section('page-title', 'Find a room')
@section('header-context', 'Search by time and group size')

@section('content')
    <x-page-header eyebrow="Room search" title="Find an available room"
        description="Only rooms that fit your group and have no confirmed booking in this time range are shown." />

    <x-card padding="none" class="mb-6">
        <form method="GET" action="{{ route('rooms.available') }}"
            class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-5 xl:items-end">
            <x-input name="date" label="Date" type="date" :value="$date" required />
            <x-time-picker name="start_time" label="Starts · IST" :value="$startTime" required />
            <x-time-picker name="end_time" label="Ends · IST" :value="$endTime" required />
            <x-input name="people" label="People" type="number" min="1" :value="$people" required />
            <x-button type="submit" class="w-full">Search availability</x-button>
        </form>
    </x-card>

    <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
        <div>
            <h2 class="text-ink text-sm font-semibold">Available rooms</h2>
            <p class="text-ink-muted mt-1 text-sm">
                {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }} · {{ $startTime }}–{{ $endTime }} IST ·
                {{ $people }} {{ (int) $people === 1 ? 'person' : 'people' }}
            </p>
        </div>
        <p class="text-ink-subtle text-xs">{{ $rooms->count() }} {{ $rooms->count() === 1 ? 'room' : 'rooms' }} found</p>
    </div>

    @if ($rooms->isEmpty())
        <x-empty-state title="No rooms available for this search"
            description="Try another time or reduce the group size. Back-to-back bookings are available." icon="search" />
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($rooms as $room)
                <article class="border-line bg-panel shadow-panel rounded-lg border p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-ink text-base font-semibold">{{ $room->name }}</h3>
                            <p class="text-ink-muted mt-1 text-sm">{{ $room->floor }}</p>
                        </div>
                        <x-badge variant="available">Available</x-badge>
                    </div>
                    <p class="text-ink-muted mt-5 text-sm">
                        <span class="text-ink text-2xl font-semibold">{{ $room->capacity }}</span>
                        seats · {{ $room->capacity - (int) $people }} spare
                    </p>
                    <a href="{{ route('bookings.index', [
                        'date' => $date,
                        'room_id' => $room->id,
                        'start_time' => $date . 'T' . $startTime,
                        'end_time' => $date . 'T' . $endTime,
                    ]) }}#new-booking"
                        data-open-booking data-room-id="{{ $room->id }}"
                        data-start-time="{{ $date }}T{{ $startTime }}"
                        data-end-time="{{ $date }}T{{ $endTime }}"
                        class="bg-brand-600 hover:bg-brand-700 focus-visible:outline-brand-500 mt-5 inline-flex min-h-10 w-full items-center justify-center rounded-md px-4 text-sm font-semibold text-white transition-colors focus-visible:outline-2 focus-visible:outline-offset-2"><span>Book
                            this room</span></a>
                </article>
            @endforeach
        </div>
    @endif
@endsection
