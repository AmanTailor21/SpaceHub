<?php

namespace Tests\Feature;

use Database\Seeders\RoomSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_room_directory_displays_seeded_rooms(): void
    {
        $this->seed(RoomSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Alpha')
            ->assertSee('20');
    }

    public function test_the_rooms_api_returns_the_seeded_rooms(): void
    {
        $this->seed(RoomSeeder::class);

        $this->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonCount(6, 'data')
            ->assertJsonPath('data.0.name', 'Alpha');
    }
}
