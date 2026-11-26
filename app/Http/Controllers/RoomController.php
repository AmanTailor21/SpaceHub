<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Contracts\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::query()->orderBy('floor')->orderBy('name')->get();

        return view('rooms.index', compact('rooms'));
    }
}
