@extends('layouts.app')

@section('title', 'Calendar · SpaceHub')
@section('page-title', 'Calendar')
@section('header-context')
    {{ \Carbon\Carbon::parse($selectedDate)->format('l, d M Y') }} · Times shown in IST
@endsection

@section('content')
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-danger-600/30 bg-danger-50 px-4 py-3 text-sm text-danger-600" role="alert" aria-live="assertive">
            <p class="font-semibold">We couldn’t save that booking.</p>
            <p class="mt-1">{{ $errors->first() }}</p>
        </div>
    @endif

    <x-page-header
        eyebrow="Room schedule"
        :title="\Carbon\Carbon::parse($selectedDate)->format('D, d M Y')"
        description="A clear view of every meeting room, from 08:00 to 20:00 IST."
    >
        <a href="{{ route('bookings.index', ['date' => $previousDate, 'room_id' => $selectedRoomId]) }}" aria-label="Previous day" class="grid size-10 place-items-center rounded-md border border-line bg-panel text-ink-muted shadow-sm transition-colors hover:bg-panel-muted hover:text-ink">
            @include('components.icon', ['name' => 'chevron-left', 'class' => 'size-4'])
        </a>
        <a href="{{ route('bookings.index', ['date' => now()->toDateString(), 'room_id' => $selectedRoomId]) }}" @class([
            'inline-flex min-h-10 items-center rounded-md border px-3 text-sm font-medium shadow-sm transition-colors',
            'border-brand-200 bg-brand-50 text-brand-700 dark:text-brand-600' => $isToday,
            'border-line bg-panel text-ink-muted hover:bg-panel-muted' => ! $isToday,
        ])>Today</a>
        <a href="{{ route('bookings.index', ['date' => $nextDate, 'room_id' => $selectedRoomId]) }}" aria-label="Next day" class="grid size-10 place-items-center rounded-md border border-line bg-panel text-ink-muted shadow-sm transition-colors hover:bg-panel-muted hover:text-ink">
            @include('components.icon', ['name' => 'chevron-right', 'class' => 'size-4'])
        </a>
    </x-page-header>

    <x-card padding="none" class="mb-5">
        <form method="GET" action="{{ route('bookings.index') }}" class="flex flex-col gap-3 p-4 sm:flex-row sm:items-end sm:justify-between sm:px-5">
            <div class="grid flex-1 gap-3 sm:grid-cols-2 sm:max-w-lg">
                <x-input name="date" label="Date" type="date" :value="$selectedDate" />
                <x-select name="room_id" label="Room">
                    <option value="">All rooms</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected($selectedRoomId === $room->id)>{{ $room->name }} · {{ $room->floor }}</option>
                    @endforeach
                </x-select>
            </div>
            <x-button type="submit" variant="secondary" size="sm">
                @include('components.icon', ['name' => 'filter', 'class' => 'size-4'])
                Apply filters
            </x-button>
        </form>
    </x-card>

    <x-card padding="none" class="overflow-hidden">
        <div class="flex items-center justify-between border-b border-line px-5 py-3.5">
            <div>
                <h2 class="text-sm font-semibold text-ink">Rooms</h2>
                <p class="mt-0.5 text-xs text-ink-subtle">Select an open 30-minute slot to start a booking</p>
            </div>
            <span class="hidden items-center gap-1.5 text-xs text-ink-subtle sm:inline-flex">
                <span class="size-1.5 rounded-full bg-success-600"></span>
                08:00–20:00 · IST
            </span>
        </div>

        <div class="hidden border-b border-line bg-panel-muted/50 md:grid md:grid-cols-[12rem_minmax(0,1fr)]">
            <div class="border-r border-line px-5 py-3 text-[11px] font-medium uppercase tracking-wide text-ink-subtle">Room</div>
            <div class="overflow-x-auto px-4 py-3">
                <div class="timeline-ruler">
                    @foreach ($timeLabels as $hour)
                        <span>{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}:00</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="divide-y divide-line">
            @foreach ($rooms as $room)
                @if ($selectedRoomId === null || $selectedRoomId === $room->id)
                    @php
                        $roomBookings = $bookingsByRoom->get($room->id, collect());
                    @endphp
                    <section class="md:grid md:grid-cols-[12rem_minmax(0,1fr)]" aria-label="{{ $room->name }} room schedule">
                        <header class="flex items-center justify-between gap-3 border-b border-line bg-panel px-4 py-3 md:border-b-0 md:border-r md:px-5">
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-semibold text-ink">{{ $room->name }}</h3>
                                <p class="mt-0.5 text-xs text-ink-subtle">{{ $room->floor }} · {{ $room->capacity }} seats</p>
                            </div>
                            <span class="shrink-0 rounded-md bg-panel-muted px-2 py-1 text-[11px] font-medium text-ink-muted">{{ $roomBookings->count() }}</span>
                        </header>

                        <div class="hidden overflow-x-auto px-4 py-3 md:block">
                            <div class="timeline-track" aria-label="08:00 to 20:00 timeline">
                                @foreach (range(0, 23) as $slot)
                                    @php
                                        $slotStart = $slot * 30;
                                        $slotEnd = $slotStart + 30;
                                        $slotStartTime = \Carbon\Carbon::parse($selectedDate, config('app.timezone'))->setTime(8, 0)->addMinutes($slotStart);
                                        $slotEndTime = $slotStartTime->copy()->addMinutes(30);
                                    @endphp
                                    <button
                                        type="button"
                                        class="timeline-slot"
                                        data-grid-start="{{ $slotStart + 1 }}"
                                        data-grid-end="{{ $slotEnd + 1 }}"
                                        data-room-id="{{ $room->id }}"
                                        data-start-time="{{ $slotStartTime->format('Y-m-d\TH:i') }}"
                                        data-end-time="{{ $slotEndTime->format('Y-m-d\TH:i') }}"
                                        data-open-booking
                                        @disabled($slotStartTime->lessThan(now()))
                                        aria-label="Book {{ $room->name }} from {{ $slotStartTime->format('H:i') }} to {{ $slotEndTime->format('H:i') }} IST"
                                        title="Book {{ $room->name }} · {{ $slotStartTime->format('H:i') }}–{{ $slotEndTime->format('H:i') }}"
                                    ></button>
                                @endforeach

                                @foreach ($roomBookings as $booking)
                                    @php
                                        $bookingStart = max(0, ($booking->start_time->hour * 60) + $booking->start_time->minute - 480);
                                        $bookingEnd = min(720, ($booking->end_time->hour * 60) + $booking->end_time->minute - 480);
                                    @endphp
                                    <button
                                        type="button"
                                        class="timeline-event {{ $booking->status === \App\Enums\BookingStatus::Cancelled ? 'timeline-event--cancelled' : 'timeline-event--confirmed' }}"
                                        data-grid-start="{{ $bookingStart + 1 }}"
                                        data-grid-end="{{ max($bookingStart + 1, $bookingEnd) + 1 }}"
                                        data-booking-id="{{ $booking->id }}"
                                        data-booking-title="{{ $booking->title }}"
                                        data-booked-by="{{ $booking->booked_by }}"
                                        data-room-name="{{ $room->name }}"
                                        data-starts-at="{{ $booking->start_time->format('d M Y · H:i') }} IST"
                                        data-ends-at="{{ $booking->end_time->format('H:i') }} IST"
                                        data-booking-status="{{ $booking->status->value }}"
                                        data-cancel-url="{{ route('bookings.destroy', $booking->id) }}"
                                        data-open-booking-details
                                        aria-label="{{ $booking->title }}, {{ $booking->start_time->format('H:i') }} to {{ $booking->end_time->format('H:i') }} IST, {{ $booking->status->value }}"
                                        title="{{ $booking->title }} · {{ $booking->start_time->format('H:i') }}–{{ $booking->end_time->format('H:i') }} IST"
                                    >
                                        <span class="block truncate text-xs font-semibold">{{ $booking->title }}</span>
                                        <span class="mt-1 block truncate text-[11px] font-medium">
                                            {{ $booking->start_time->format('H:i') }}–{{ $booking->end_time->format('H:i') }}
                                            <span class="sr-only">IST</span>
                                        </span>
                                    </button>
                                @endforeach

                                @if ($nowOffset !== null && $nowOffset < 720)
                                    <span class="timeline-now" data-grid-start="{{ $nowOffset + 1 }}" aria-label="Current time"></span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-2 px-4 py-3 md:hidden">
                            @forelse ($roomBookings as $booking)
                                <button
                                    type="button"
                                    data-booking-id="{{ $booking->id }}"
                                    data-booking-title="{{ $booking->title }}"
                                    data-booked-by="{{ $booking->booked_by }}"
                                    data-room-name="{{ $room->name }}"
                                    data-starts-at="{{ $booking->start_time->format('d M Y · H:i') }} IST"
                                    data-ends-at="{{ $booking->end_time->format('H:i') }} IST"
                                    data-booking-status="{{ $booking->status->value }}"
                                    data-cancel-url="{{ route('bookings.destroy', $booking->id) }}"
                                    data-open-booking-details
                                    class="flex w-full items-start justify-between gap-3 rounded-md border border-line bg-panel-muted/50 p-3 text-left"
                                >
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-ink">{{ $booking->title }}</span>
                                        <span class="mt-1 block text-xs text-ink-muted">
                                            {{ $booking->start_time->format('H:i') }}–{{ $booking->end_time->format('H:i') }} IST · {{ $booking->booked_by }}
                                        </span>
                                    </span>
                                    <x-badge
                                        :variant="$booking->status->value"
                                        :label="$booking->status === \App\Enums\BookingStatus::Confirmed ? 'Confirmed' : 'Cancelled'"
                                    />
                                </button>
                            @empty
                                <p class="py-2 text-sm text-ink-subtle">No bookings for this room.</p>
                            @endforelse
                        </div>
                    </section>
                @endif
            @endforeach
        </div>
    </x-card>

    <section id="find-room" class="mt-6 scroll-mt-24">
        <x-card padding="none">
            <div class="border-b border-line px-5 py-4">
                <h2 class="text-sm font-semibold text-ink">Find a room</h2>
                <p class="mt-1 text-sm text-ink-muted">Search open rooms by time and group size.</p>
            </div>
            <form method="GET" action="{{ route('rooms.available') }}" class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-5 xl:items-end sm:px-5">
                <x-input name="date" label="Date" type="date" :value="$selectedDate" required />
                <x-time-picker name="start_time" label="Starts · IST" value="10:00" required />
                <x-time-picker name="end_time" label="Ends · IST" value="11:00" required />
                <x-input name="people" label="People" type="number" min="1" value="1" required />
                <x-button type="submit" class="w-full">Find availability</x-button>
            </form>
        </x-card>
    </section>

    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 text-xs text-ink-subtle">
        <p>All times shown in India Standard Time (IST, UTC+05:30).</p>
        <p><span class="font-medium text-ink-muted">Tip:</span> Select an empty 30-minute slot to start a booking.</p>
    </div>

    <div
        id="new-booking"
        x-data="bookingDrawer"
        data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}"
        data-initial-room="{{ $initialRoomId ?? '' }}"
        data-initial-start="{{ $initialStartTime ?? '' }}"
        data-initial-end="{{ $initialEndTime ?? '' }}"
        @spacehub:booking-open.window="openBooking"
    >
        <x-drawer id="booking-create" title="New booking" description="Choose a room and meeting time. All times are shown in IST.">
            <form method="POST" action="{{ route('bookings.store') }}" class="flex min-h-[calc(100dvh-8rem)] flex-col">
                @csrf
                <div class="space-y-5">
                    <x-select name="room_id" label="Room" required x-ref="room">
                        <option value="">Choose a room</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->name }} · {{ $room->capacity }} seats · {{ $room->floor }}</option>
                        @endforeach
                    </x-select>
                    <x-input name="title" label="Meeting title" :value="old('title')" required maxlength="255" x-ref="title" />
                    <x-input name="booked_by" label="Booked by" :value="old('booked_by')" required maxlength="255" />
                    <x-time-picker name="start_time" label="Starts · IST" type="datetime-local" :value="old('start_time')" required x-ref="startTime" @input="updateDuration" />
                    <x-time-picker name="end_time" label="Ends · IST" type="datetime-local" :value="old('end_time')" required x-ref="endTime" @input="updateDuration" />
                    <p class="text-xs text-ink-subtle" aria-live="polite">
                        Duration: <span x-text="duration || 'Set a valid start and end time'"></span>
                    </p>
                </div>
                <div class="mt-auto flex gap-2 border-t border-line pt-5">
                    <x-button type="submit" class="flex-1">Confirm booking</x-button>
                    <x-button type="button" variant="secondary" @click="closeDialog">Close</x-button>
                </div>
            </form>
        </x-drawer>
    </div>

    <div
        x-data="bookingDetails"
        @spacehub:booking-details.window="openDetails"
        @spacehub:confirm-accepted.window="acceptCancel"
    >
        <x-drawer id="booking-details" title="Booking details" description="Meeting information and booking status.">
            <div class="space-y-5">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-ink-subtle">Meeting</p>
                    <h3 class="mt-1 text-lg font-semibold text-ink" x-text="booking.title"></h3>
                </div>
                <dl class="divide-y divide-line rounded-lg border border-line">
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <dt class="text-ink-muted">Room</dt>
                        <dd class="font-medium text-ink" x-text="booking.room"></dd>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <dt class="text-ink-muted">Starts</dt>
                        <dd class="font-medium text-ink" x-text="booking.startsAt"></dd>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <dt class="text-ink-muted">Ends</dt>
                        <dd class="font-medium text-ink" x-text="booking.endsAt"></dd>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <dt class="text-ink-muted">Booked by</dt>
                        <dd class="font-medium text-ink" x-text="booking.bookedBy"></dd>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <dt class="text-ink-muted">Status</dt>
                        <dd class="font-medium capitalize text-ink" x-text="booking.status"></dd>
                    </div>
                </dl>
                <button
                    type="button"
                    x-show="booking.status === 'confirmed'"
                    @click="askCancel"
                    class="min-h-10 rounded-md border border-danger-600/40 px-3 text-sm font-semibold text-danger-600 hover:bg-danger-50"
                >Cancel booking</button>
                <form id="booking-cancel-form" method="POST" :action="booking.cancelUrl" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </x-drawer>

        <x-confirm-dialog id="cancel-confirm" title="Cancel this booking?" confirm-label="Cancel booking">
            <p>Cancel <span class="font-semibold text-ink" x-text="booking.title"></span>? This releases the room for that time.</p>
        </x-confirm-dialog>
    </div>
@endsection
