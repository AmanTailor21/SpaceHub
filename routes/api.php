<?php

use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/bookings', [BookingController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
