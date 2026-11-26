@extends('layouts.app')

@section('title', 'Bookings · SpaceHub')
@section('page-title', 'Bookings')
@section('header-context', 'Search and review room reservations')

@section('content')
    <x-page-header
        eyebrow="Reservations"
        title="Bookings"
        description="Review confirmed and cancelled bookings across every room."
    >
        <a href="{{ route('bookings.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">
            Open calendar
        </a>
    </x-page-header>

    <x-card padding="none" class="mb-5">
        <form method="GET" action="{{ route('bookings.index') }}" class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-5 xl:items-end sm:px-5">
            <input type="hidden" name="view" value="list">
            <x-input name="date" label="Date" type="date" :value="$filters['date']" />
            <x-select name="room_id" label="Room">
                <option value="">All rooms</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected((string) $filters['room_id'] === (string) $room->id)>{{ $room->name }}</option>
                @endforeach
            </x-select>
            <x-select name="status" label="Status">
                <option value="">All statuses</option>
                <option value="confirmed" @selected($filters['status'] === 'confirmed')>Confirmed</option>
                <option value="cancelled" @selected($filters['status'] === 'cancelled')>Cancelled</option>
            </x-select>
            <x-input name="booked_by" label="Booked by" :value="$filters['booked_by']" placeholder="Search a name" />
            <div class="flex gap-2">
                <x-button type="submit" variant="secondary" class="flex-1">Apply filters</x-button>
                <a href="{{ route('bookings.index', ['view' => 'list']) }}" class="inline-flex min-h-10 items-center rounded-md px-3 text-sm font-medium text-ink-muted hover:bg-panel-muted">Reset</a>
            </div>
        </form>
    </x-card>

    @if ($bookings->isEmpty())
        <x-empty-state
            title="No bookings found"
            description="Try widening your filters, or create a booking from the calendar."
            icon="calendar"
        >
            <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-brand-700 dark:text-brand-600">View calendar</a>
        </x-empty-state>
    @else
        <x-table>
            <thead class="border-b border-line bg-panel-muted/60 text-xs font-semibold uppercase tracking-wide text-ink-subtle">
                <tr>
                    <th scope="col" aria-sort="{{ $filters['sort'] === 'date' ? ($filters['direction'] === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-4 py-3">
                        <a href="{{ route('bookings.index', array_merge(request()->query(), ['view' => 'list', 'sort' => 'date', 'direction' => $filters['sort'] === 'date' && $filters['direction'] === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-ink">Date &amp; time</a>
                    </th>
                    <th scope="col" aria-sort="{{ $filters['sort'] === 'room' ? ($filters['direction'] === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-4 py-3">
                        <a href="{{ route('bookings.index', array_merge(request()->query(), ['view' => 'list', 'sort' => 'room', 'direction' => $filters['sort'] === 'room' && $filters['direction'] === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-ink">Room</a>
                    </th>
                    <th scope="col" class="px-4 py-3">Meeting</th>
                    <th scope="col" aria-sort="{{ $filters['sort'] === 'status' ? ($filters['direction'] === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-4 py-3">
                        <a href="{{ route('bookings.index', array_merge(request()->query(), ['view' => 'list', 'sort' => 'status', 'direction' => $filters['sort'] === 'status' && $filters['direction'] === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-ink">Status</a>
                    </th>
                    <th scope="col" aria-sort="{{ $filters['sort'] === 'booked_by' ? ($filters['direction'] === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-4 py-3">
                        <a href="{{ route('bookings.index', array_merge(request()->query(), ['view' => 'list', 'sort' => 'booked_by', 'direction' => $filters['sort'] === 'booked_by' && $filters['direction'] === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-ink">Booked by</a>
                    </th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach ($bookings as $booking)
                    <tr class="transition-colors hover:bg-panel-muted/40">
                        <td class="whitespace-nowrap px-4 py-3.5">
                            <span class="block font-medium text-ink">{{ $booking->start_time->format('d M Y') }}</span>
                            <span class="mt-1 block text-xs text-ink-muted">{{ $booking->start_time->format('H:i') }}–{{ $booking->end_time->format('H:i') }} IST</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5">
                            <span class="font-medium text-ink">{{ $booking->room->name }}</span>
                            <span class="mt-1 block text-xs text-ink-muted">{{ $booking->room->floor }}</span>
                        </td>
                        <td class="max-w-64 px-4 py-3.5">
                            <span class="block truncate font-medium text-ink" title="{{ $booking->title }}">{{ $booking->title }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <x-badge :variant="$booking->status->value" :label="$booking->status === \App\Enums\BookingStatus::Confirmed ? 'Confirmed' : 'Cancelled'" />
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-ink-muted">{{ $booking->booked_by }}</td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-right">
                            <a href="{{ route('bookings.index', ['date' => $booking->start_time->toDateString(), 'room_id' => $booking->room_id]) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-600">Open calendar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-ink-muted">
            <p>Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings</p>
            <div class="flex gap-2">
                @if ($bookings->previousPageUrl())
                    <a href="{{ $bookings->previousPageUrl() }}" class="inline-flex min-h-9 items-center rounded-md border border-line bg-panel px-3 font-medium hover:bg-panel-muted">Previous</a>
                @endif
                @if ($bookings->nextPageUrl())
                    <a href="{{ $bookings->nextPageUrl() }}" class="inline-flex min-h-9 items-center rounded-md border border-line bg-panel px-3 font-medium hover:bg-panel-muted">Next</a>
                @endif
            </div>
        </div>
    @endif
@endsection
