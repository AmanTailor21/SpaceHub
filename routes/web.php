<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->whereNumber('booking')->name('bookings.destroy');
