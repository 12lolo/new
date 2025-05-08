<?php

namespace App\Http\Controllers;

use App\Models\RoomObject;
use App\Models\Item;
use Illuminate\Http\Request;

class RoomObjectController extends Controller
{
    /**
     * Look at a specific object in a room and see what it contains.
     */
    public function look(Request $request, $roomKey, $objectKey)
    {
        $object = RoomObject::where('object_key', $objectKey)
            ->whereHas('room', function($query) use ($roomKey) {
                $query->where('room_key', $roomKey);
            })
            ->first();

        if (!$object) {
            return response()->json(['error' => 'Object not found'], 404);
        }

        // Check if object is locked
        if ($object->is_locked) {
            return response()->json([
                'location' => $object->name,
                'message' => 'This object is locked. You need to find a way to unlock it.'
            ]);
        }

        $childObjects = $object->childObjects;
        $items = $object->items;

        return response()->json([
            'location' => $object->name,
            'description' => $object->description,
            'objects' => $childObjects->pluck('name'),
            'items' => $items->pluck('name'),
        ]);
    }

    /**
     * Look at a nested object (like a cabinet door)
     */
    public function lookNested(Request $request, $roomKey, $objectKey, $nestedObjectKey)
    {
        $nestedObject = RoomObject::where('object_key', $nestedObjectKey)
            ->whereHas('parentObject', function($query) use ($objectKey) {
                $query->where('object_key', $objectKey);
            })
            ->first();

        if (!$nestedObject) {
            return response()->json(['error' => 'Nested object not found'], 404);
        }

        return response()->json([
            'location' => $nestedObject->name,
            'objects' => $nestedObject->childObjects->pluck('name')->toArray(),
            'items' => $nestedObject->items->pluck('name')->toArray(),
        ]);
    }

    /**
     * Interact with an object or item.
     */
    public function interact(Request $request, $roomKey, $objectKey, $action)
    {
        $object = RoomObject::where('object_key', $objectKey)
            ->whereHas('room', function($query) use ($roomKey) {
                $query->where('room_key', $roomKey);
            })
            ->first();

        if (!$object) {
            return response()->json(['error' => 'Object not found'], 404);
        }

        // Handle different actions
        switch ($action) {
            case 'take-key':
                return $this->takeKey($request, $object);
            
            case 'unlock':
                return $this->unlockObject($request, $object);
                
            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }

    /**
     * Interact with a nested object
     */
    public function interactNested(Request $request, $roomKey, $objectKey, $nestedObjectKey, $action)
    {
        $nestedObject = RoomObject::where('object_key', $nestedObjectKey)
            ->whereHas('parentObject', function($query) use ($objectKey) {
                $query->where('object_key', $objectKey);
            })
            ->first();

        if (!$nestedObject) {
            return response()->json(['error' => 'Nested object not found'], 404);
        }

        // Handle the action based on what it is
        switch ($action) {
            case 'take-key':
                return $this->takeKey($request, $nestedObject);
            
            default:
                return response()->json(['error' => 'Invalid action for nested object'], 400);
        }
    }

    /**
     * Take a key from an object
     */
    private function takeKey(Request $request, RoomObject $object)
    {
        $key = $object->items()->where('item_key', 'sleutel')->first();

        if (!$key) {
            return response()->json(['error' => 'No key found here']);
        }

        if (!$key->takeable) {
            return response()->json(['error' => 'You cannot take this item']);
        }

        // Add key to inventory
        $inventory = $request->session()->get('inventory', []);
        if (!in_array('sleutel', $inventory)) {
            $inventory[] = 'sleutel';
            $request->session()->put('inventory', $inventory);
        }

        return response()->json([
            'message' => 'Je hebt de sleutel gepakt!',
            'inventory' => $inventory
        ]);
    }

    /**
     * Unlock an object if player has the required key
     */
    private function unlockObject(Request $request, RoomObject $object)
    {
        if (!$object->getAttribute('is_locked')) {
            return response()->json(['error' => 'This object is not locked']);
        }

        // Check if player has the required item
        $inventory = $request->session()->get('inventory', []);
        $requiredItem = $object->getAttribute('required_item');

        if (!$requiredItem || !in_array($requiredItem, $inventory)) {
            return response()->json(['error' => 'You need a specific item to unlock this']);
        }

        // Unlock the object (in a real app, you'd update the database here)
        return response()->json([
            'message' => "You unlocked the {$object->getAttribute('name')}!",
            'now_unlocked' => true
        ]);
    }

    /**
     * Try to unlock a safe with a code
     */
    public function tryCode(Request $request, $roomKey, $objectKey)
    {
        $safe = RoomObject::where('object_key', $objectKey)
            ->whereHas('room', function($query) use ($roomKey) {
                $query->where('room_key', $roomKey);
            })
            ->first();

        if (!$safe) {
            return response()->json(['error' => 'Safe not found'], 404);
        }

        // Check if this is actually a safe/lockable object with a code
        if (!$safe->getAttribute('code')) {
            return response()->json([
                'error' => 'This object does not have a code mechanism'
            ], 400);
        }

        // Validate request has a code
        $request->validate([
            'code' => 'required|string',
        ]);

        $enteredCode = $request->input('code');
        $correctCode = $safe->getAttribute('code');

        // Track attempts in session to provide hints
        $attempts = $request->session()->get("code_attempts.{$safe->id}", 0) + 1;
        $request->session()->put("code_attempts.{$safe->id}", $attempts);

        // Check if code is correct
        if ($enteredCode === $correctCode) {
            // Mark safe as unlocked
            $safe->update(['is_locked' => false]);
            
            // Get items in the safe
            $items = $safe->items->pluck('name')->toArray();
            
            return response()->json([
                'success' => true,
                'message' => 'The safe unlocks with a satisfying click!',
                'items_found' => $items,
            ]);
        }
        
        // Handle incorrect code with progressive hints
        $hint = '';
        if ($attempts >= 5) {
            // After 5 attempts, give a strong hint
            $hint = "Hint: The code is {$correctCode[0]}-{$correctCode[1]}-X-X";
        } elseif ($attempts >= 3) {
            // After 3 attempts, give a slight hint
            $hint = "Hint: The first digit is {$correctCode[0]}";
        }
        
        return response()->json([
            'success' => false,
            'message' => 'The code seems incorrect. The safe remains locked.',
            'attempts' => $attempts,
            'hint' => $hint,
        ]);
    }
}
