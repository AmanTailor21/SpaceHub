@extends('layouts.app')

@section('title', 'Bookings · SpaceHub')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-700">Room schedule</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Bookings</h1>
        <p class="mt-2 text-slate-600">Book a room and see what is happening throughout the day.</p>
    </div>

    <section class="mb-8 rounded-xl border border-indigo-100 bg-indigo-50/60 p-5">
        <h2 class="font-semibold">Find an available room</h2>
        <form method="GET" action="{{ route('rooms.available') }}" class="mt-4 flex flex-wrap items-end gap-4">
            <div>
                <label for="availability-date" class="mb-1 block text-sm font-medium">Date</label>
                <input id="availability-date" type="date" name="date" value="{{ $selectedDate }}" required class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="availability-start" class="mb-1 block text-sm font-medium">Starts</label>
                <input id="availability-start" type="time" name="start_time" required class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="availability-end" class="mb-1 block text-sm font-medium">Ends</label>
                <input id="availability-end" type="time" name="end_time" required class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="availability-people" class="mb-1 block text-sm font-medium">People</label>
                <input id="availability-people" type="number" name="people" min="1" value="1" required class="w-24 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Find rooms</button>
        </form>
    </section>

    <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <section>
            <form method="GET" action="{{ route('bookings.index') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div>
                    <label for="filter-date" class="mb-1 block text-sm font-medium">Date</label>
                    <input id="filter-date" type="date" name="date" value="{{ $selectedDate }}" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="filter-room" class="mb-1 block text-sm font-medium">Room</label>
                    <select id="filter-room" name="room_id" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All rooms</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" @selected($selectedRoomId === $room->id)>{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Show schedule</button>
            </form>

            <div class="space-y-5">
                @foreach ($rooms as $room)
                    @if ($selectedRoomId === null || $selectedRoomId === $room->id)
                        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                                <div>
                                    <h2 class="font-semibold">{{ $room->name }}</h2>
                                    <p class="mt-1 text-sm text-slate-500">{{ $room->floor }} · {{ $room->capacity }} people</p>
                                </div>
                                <span class="text-sm text-slate-500">{{ $bookingsByRoom->get($room->id, collect())->count() }} bookings</span>
                            </div>
                            @forelse ($bookingsByRoom->get($room->id, collect()) as $booking)
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 last:border-b-0">
                                    <div class="min-w-0">
                                        <p class="font-medium">{{ $booking->title }}</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $booking->start_time->format('H:i') }}–{{ $booking->end_time->format('H:i') }}
                                            · {{ $booking->booked_by }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if ($booking->status === \App\Enums\BookingStatus::Cancelled)
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">Cancelled</span>
                                        @else
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Confirmed</span>
                                            <form method="POST" action="{{ route('bookings.destroy', $booking->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-rose-700 hover:text-rose-900">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="px-5 py-4 text-sm text-slate-500">No bookings for this room on this date.</p>
                            @endforelse
                        </section>
                    @endif
                @endforeach
            </div>
        </section>

        <aside class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold">Book a room</h2>
            <p class="mt-1 text-sm text-slate-500">Bookings are available between 08:00 and 20:00.</p>
            <form method="POST" action="{{ route('bookings.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="room_id" class="mb-1 block text-sm font-medium">Room</label>
                    <select id="room_id" name="room_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose a room</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->name }} · {{ $room->capacity }} people</option>
                        @endforeach
                    </select>
                    @error('room_id')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="title" class="mb-1 block text-sm font-medium">Meeting title</label>
                    <input id="title" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('title')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="booked_by" class="mb-1 block text-sm font-medium">Booked by</label>
                    <input id="booked_by" name="booked_by" value="{{ old('booked_by') }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('booked_by')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="start_time" class="mb-1 block text-sm font-medium">Starts</label>
                    <input id="start_time" type="datetime-local" name="start_time" value="{{ old('start_time') }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('start_time')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_time" class="mb-1 block text-sm font-medium">Ends</label>
                    <input id="end_time" type="datetime-local" name="end_time" value="{{ old('end_time') }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('end_time')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full rounded-lg bg-indigo-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-800">Confirm booking</button>
            </form>
        </aside>
    </div>
@endsection
