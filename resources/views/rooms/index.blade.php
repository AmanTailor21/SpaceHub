@extends('layouts.app')

@section('title', 'Meeting rooms · SpaceHub')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-700">Space directory</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">Meeting rooms</h1>
        <p class="mt-2 text-slate-600">Find the right room for your next team meeting.</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($rooms as $room)
            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <h2 class="text-xl font-semibold">{{ $room->name }}</h2>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">{{ $room->floor }}</span>
                </div>
                <p class="mt-5 text-sm text-slate-600">Capacity</p>
                <p class="mt-1 text-2xl font-semibold">{{ $room->capacity }} <span class="text-base font-normal text-slate-500">people</span></p>
            </article>
        @endforeach
    </div>
@endsection
