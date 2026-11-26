<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Alpha', 'capacity' => 4, 'floor' => '1F'],
            ['name' => 'Beta', 'capacity' => 6, 'floor' => '1F'],
            ['name' => 'Gamma', 'capacity' => 8, 'floor' => '2F'],
            ['name' => 'Delta', 'capacity' => 10, 'floor' => '2F'],
            ['name' => 'Epsilon', 'capacity' => 12, 'floor' => '3F'],
            ['name' => 'Zeta', 'capacity' => 20, 'floor' => '3F'],
        ] as $room) {
            Room::query()->updateOrCreate(
                ['name' => $room['name']],
                ['capacity' => $room['capacity'], 'floor' => $room['floor']],
            );
        }
    }
}
