<?php

namespace App\Http\Requests;

/**
 * Availability searches accept HH:mm values and combine them with the requested date.
 */
class AvailableRoomsRequest extends BookingWindowRequest
{
    protected function prepareForValidation(): void
    {
        $date = $this->input('date');
        $start = $this->input('start_time');
        $end = $this->input('end_time');

        if (is_string($date) && is_string($start)) {
            $this->merge(['start_time' => "{$date} {$start}"]);
        }

        if (is_string($date) && is_string($end)) {
            $this->merge(['end_time' => "{$date} {$end}"]);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'date_format:Y-m-d H:i'],
            'end_time' => ['required', 'date_format:Y-m-d H:i'],
            'people' => ['required', 'integer', 'min:1'],
        ];
    }
}
