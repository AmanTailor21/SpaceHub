@extends('layouts.app')

@section('title', 'Available rooms · SpaceHub')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-700">Room search</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Available rooms</h1>
        <p class="mt-2 text-slate-600">
            {{ $date }} · {{ $startTime }}–{{ $endTime }} · {{ $people }} {{ (int) $people === 1 ? 'person' : 'people' }}
        </p>
    </div>

    <section class="mb-6 rounded-xl border border-indigo-100 bg-indigo-50/60 p-5">
        <form method="GET" action="{{ route('rooms.available') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="date" class="mb-1 block text-sm font-medium">Date</label>
                <input id="date" type="date" name="date" value="{{ $date }}" required class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="start_time" class="mb-1 block text-sm font-medium">Starts</label>
                <input id="start_time" type="time" name="start_time" value="{{ $startTime }}" required class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="end_time" class="mb-1 block text-sm font-medium">Ends</label>
                <input id="end_time" type="time" name="end_time" value="{{ $endTime }}" required class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="people" class="mb-1 block text-sm font-medium">People</label>
                <input id="people" type="number" name="people" min="1" value="{{ $people }}" required class="w-24 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Search again</button>
        </form>
    </section>

    @if ($rooms->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white p-8 text-center text-slate-600">
            No rooms match this time and capacity. Try a different time or group size.
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($rooms as $room)
                <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <h2 class="text-xl font-semibold">{{ $room->name }}</h2>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">{{ $room->floor }}</span>
                    </div>
                    <p class="mt-5 text-2xl font-semibold">{{ $room->capacity }} <span class="text-base font-normal text-slate-500">people</span></p>
                    <a href="{{ route('bookings.index', ['date' => $date, 'room_id' => $room->id]) }}" class="mt-4 inline-block text-sm font-semibold text-indigo-700 hover:text-indigo-900">View this room's schedule</a>
                </article>
            @endforeach
        </div>
    @endif
@endsection
