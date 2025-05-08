<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function look($roomKey)
    {
        $room = Room::where('room_key', $roomKey)->with('objects')->first();

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        return response()->json([
            'room' => $room->name,
            'objects' => $room->objects->pluck('name'),
        ]);
    }

    public function open(Request $request, $roomKey)
    {
        $room = Room::where('room_key', $roomKey)->first();

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        if (!$request->session()->has('inventory') || !in_array('key', $request->session()->get('inventory'))) {
            return response()->json(['error' => 'De deur is op slot. Vind een sleutel!']);
        }

        $request->session()->put('current_room', $roomKey);

        return response()->json([
            'message' => "Je hebt {$room->name} geopend!",
            'current_room' => $room->name,
        ]);
    }
}
