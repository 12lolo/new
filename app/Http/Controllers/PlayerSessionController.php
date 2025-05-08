<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\PlayerSession;
use App\Models\PlayerInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlayerSessionController extends Controller
{
    /**
     * Start a new game session
     */
    public function startGame(Request $request)
    {
        // Generate a unique session ID
        $sessionId = Str::uuid();
        
        // Find the starting room (room1)
        $startRoom = Room::where('room_key', 'room1')->first();
        
        if (!$startRoom) {
            return response()->json(['error' => 'Starting room not found'], 500);
        }
        
        // Create a new player session
        $playerSession = PlayerSession::create([
            'session_id' => $sessionId,
            'current_room_id' => $startRoom->id,
            'start_time' => now(),
        ]);
        
        // Store the session ID in the request session
        $request->session()->put('player_session_id', $sessionId);
        
        return response()->json([
            'success' => true,
            'message' => 'New game started!',
            'session_id' => $sessionId,
            'current_room' => $startRoom->name,
            'time_started' => now()->toString(),
        ]);
    }
    
    /**
     * Get the player's current game status
     */
    public function getStatus(Request $request)
    {
        $sessionId = $request->session()->get('player_session_id');
        
        if (!$sessionId) {
            return response()->json(['error' => 'No active game session'], 404);
        }
        
        $playerSession = PlayerSession::where('session_id', $sessionId)->first();
        
        if (!$playerSession) {
            return response()->json(['error' => 'Game session not found'], 404);
        }
        
        $currentRoom = Room::find($playerSession->current_room_id);
        $inventory = PlayerInventory::where('player_session_id', $playerSession->id)
            ->pluck('item_key')
            ->toArray();
        
        $elapsedTime = now()->diffInMinutes($playerSession->start_time);
        
        return response()->json([
            'session_id' => $sessionId,
            'current_room' => $currentRoom->name,
            'time_elapsed' => $elapsedTime . ' minutes',
            'inventory' => $inventory,
            'game_completed' => $currentRoom->is_final,
        ]);
    }
    
    /**
     * Update player's inventory in the database
     */
    public function updateInventory(Request $request, $itemKey)
    {
        $sessionId = $request->session()->get('player_session_id');
        
        if (!$sessionId) {
            return response()->json(['error' => 'No active game session'], 404);
        }
        
        $playerSession = PlayerSession::where('session_id', $sessionId)->first();
        
        if (!$playerSession) {
            return response()->json(['error' => 'Game session not found'], 404);
        }
        
        // Add the item to player's inventory in database
        PlayerInventory::firstOrCreate([
            'player_session_id' => $playerSession->id,
            'item_key' => $itemKey,
        ]);
        
        // Update session inventory for easy access
        $inventory = PlayerInventory::where('player_session_id', $playerSession->id)
            ->pluck('item_key')
            ->toArray();
            
        $request->session()->put('inventory', $inventory);
        
        return response()->json([
            'message' => "Item '$itemKey' added to inventory",
            'inventory' => $inventory,
        ]);
    }
    
    /**
     * Update the player's current room
     */
    public function updateRoom(Request $request, $roomKey)
    {
        $sessionId = $request->session()->get('player_session_id');
        
        if (!$sessionId) {
            return response()->json(['error' => 'No active game session'], 404);
        }
        
        $playerSession = PlayerSession::where('session_id', $sessionId)->first();
        
        if (!$playerSession) {
            return response()->json(['error' => 'Game session not found'], 404);
        }
        
        $room = Room::where('room_key', $roomKey)->first();
        
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }
        
        // Update current room
        $playerSession->current_room_id = $room->id;
        $playerSession->save();
        
        // Check if this is the final room to mark game completion
        if ($room->is_final) {
            $playerSession->end_time = now();
            $playerSession->save();
            
            $totalTime = $playerSession->end_time->diffInMinutes($playerSession->start_time);
            
            return response()->json([
                'message' => 'Congratulations! You have completed the escape room!',
                'room' => $room->name,
                'description' => $room->description,
                'completion_time' => $totalTime . ' minutes',
            ]);
        }
        
        return response()->json([
            'message' => "Moved to {$room->name}",
            'current_room' => $room->name,
        ]);
    }
}
