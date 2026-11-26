<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'booked_by' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('start_time') || $validator->errors()->has('end_time')) {
                return;
            }

            $timezone = (string) config('app.timezone');
            $start = Carbon::parse($this->input('start_time'), $timezone)->setTimezone($timezone);
            $end = Carbon::parse($this->input('end_time'), $timezone)->setTimezone($timezone);

            if ($start->isPast()) {
                $validator->errors()->add('start_time', 'The start time must not be in the past.');
            }

            if ($end->lessThanOrEqualTo($start)) {
                $validator->errors()->add('end_time', 'The end time must be after the start time.');

                return;
            }

            if ($start->toDateString() !== $end->toDateString()) {
                $validator->errors()->add('end_time', 'Bookings must start and end on the same day.');
            }

            if ($end->diffInSeconds($start) > 4 * 60 * 60) {
                $validator->errors()->add('end_time', 'A booking cannot be longer than 4 hours.');
            }

            $openingTime = $start->copy()->setTime(8, 0);
            $closingTime = $start->copy()->setTime(20, 0);

            if ($start->lessThan($openingTime)) {
                $validator->errors()->add('start_time', 'Bookings cannot start before 08:00.');
            }

            if ($start->greaterThanOrEqualTo($closingTime)) {
                $validator->errors()->add('start_time', 'Bookings must start before 20:00.');
            }

            if ($end->greaterThan($closingTime)) {
                $validator->errors()->add('end_time', 'Bookings must end by 20:00.');
            }
        });
    }
}
