<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Look at the current room and see what objects are available.
     */
    public function look(Request $request, $roomKey = 'room1')
    {
        $room = Room::where('room_key', $roomKey)->first();
        
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        // Get only top-level objects (not nested within other objects)
        $objects = $room->topLevelObjects()->get();
        
        return response()->json([
            'room' => $room->name,
            'description' => $room->description,
            'objects' => $objects->map(function($object) {
                $name = $object->getAttribute('name');
                if ($object->getAttribute('is_locked')) {
                    $name .= ' (locked)';
                }
                return $name;
            }),
        ]);
    }

    /**
     * Try to open a room if player has the required key.
     */
    public function open(Request $request, $roomKey = 'room2')
    {
        $room = Room::where('room_key', $roomKey)->first();

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        // Check if player has the required item (sleutel/key)
        $inventory = $request->session()->get('inventory', []);
        if (!in_array('sleutel', $inventory)) {
            return response()->json(['error' => 'De deur is op slot. Vind een sleutel!']);
        }

        // Update player's current room
        $request->session()->put('current_room', $roomKey);

        return response()->json([
            'message' => "Je hebt {$room->name} geopend!",
            'current_room' => $room->name,
        ]);
    }
}
