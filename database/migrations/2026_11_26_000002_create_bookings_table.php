<?php

use App\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('booked_by');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default(BookingStatus::Confirmed->value);
            $table->timestamps();

            $table->index(['room_id', 'start_time', 'end_time'], 'bookings_room_time_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
