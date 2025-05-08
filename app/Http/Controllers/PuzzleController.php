<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PuzzleCombination;
use App\Models\RoomObject;
use Illuminate\Http\Request;

class PuzzleController extends Controller
{
    /**
     * Combine items to solve a puzzle
     */
    public function combineItems(Request $request)
    {
        // Validate the request
        $request->validate([
            'items' => 'required|array|min:2',
            'target_object' => 'required|string',
        ]);

        $itemKeys = $request->input('items');
        $targetObjectKey = $request->input('target_object');

        // Sort item keys alphabetically for consistent matching
        sort($itemKeys);
        $itemKeysString = implode(',', $itemKeys);

        // Check if this combination exists in any puzzle
        $puzzleCombinations = PuzzleCombination::all();
        $matchedPuzzle = null;

        foreach ($puzzleCombinations as $puzzle) {
            $requiredItems = explode(',', $puzzle->required_items);
            sort($requiredItems);
            
            // Check if the items match and target object is correct
            if (implode(',', $requiredItems) === $itemKeysString && 
                $puzzle->target_object_key === $targetObjectKey) {
                $matchedPuzzle = $puzzle;
                break;
            }
        }

        if (!$matchedPuzzle) {
            return response()->json([
                'success' => false,
                'message' => 'These items cannot be combined in this way.',
            ]);
        }

        // Check if player has all required items in inventory
        $inventory = $request->session()->get('inventory', []);
        foreach (explode(',', $matchedPuzzle->required_items) as $requiredItem) {
            if (!in_array($requiredItem, $inventory)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You don\'t have all required items in your inventory.',
                ]);
            }
        }

        // Process the puzzle result
        $targetObject = RoomObject::where('object_key', $targetObjectKey)->first();
        
        if (!$targetObject) {
            return response()->json([
                'success' => false,
                'message' => 'Target object not found.',
            ]);
        }

        $resultMessage = $matchedPuzzle->success_message;
        
        // Handle different result actions
        switch ($matchedPuzzle->result_action) {
            case 'unlock':
                $targetObject->update(['is_locked' => false]);
                break;
                
            case 'reveal':
                // Nothing to do as the item is already in the database
                break;
                
            case 'create':
                // Add the result item to target object if not already there
                if ($matchedPuzzle->result_item_key) {
                    $existingItem = Item::where('object_id', $targetObject->id)
                        ->where('item_key', $matchedPuzzle->result_item_key)
                        ->first();
                    
                    if (!$existingItem) {
                        Item::create([
                            'object_id' => $targetObject->id,
                            'item_key' => $matchedPuzzle->result_item_key,
                            'name' => ucwords(str_replace('_', ' ', $matchedPuzzle->result_item_key)),
                            'description' => 'An item created by combining other items.',
                            'takeable' => true,
                        ]);
                    }
                }
                break;
        }
        
        // Add result item to inventory if applicable
        if ($matchedPuzzle->result_item_key) {
            $inventory = $request->session()->get('inventory', []);
            if (!in_array($matchedPuzzle->result_item_key, $inventory)) {
                $inventory[] = $matchedPuzzle->result_item_key;
                $request->session()->put('inventory', $inventory);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => $resultMessage,
            'inventory' => $inventory
        ]);
    }

    /**
     * Try to solve a pattern-based puzzle (like the bookshelf)
     */
    public function solvePuzzle(Request $request, $roomKey, $objectKey)
    {
        $request->validate([
            'pattern' => 'required|string',
        ]);

        $object = RoomObject::where('object_key', $objectKey)
            ->whereHas('room', function($query) use ($roomKey) {
                $query->where('room_key', $roomKey);
            })
            ->first();

        if (!$object) {
            return response()->json(['error' => 'Object not found'], 404);
        }

        // Check if object has a code for pattern puzzles
        if (!$object->getAttribute('code')) {
            return response()->json([
                'error' => 'This object does not have a pattern puzzle'
            ], 400);
        }

        $enteredPattern = $request->input('pattern');
        $correctPattern = $object->getAttribute('code');

        // Check if pattern is correct
        if ($enteredPattern === $correctPattern) {
            // Mark object as unlocked
            $object->update(['is_locked' => false]);
            
            // Get items revealed by solving the puzzle
            $items = $object->items->pluck('name')->toArray();
            
            return response()->json([
                'success' => true,
                'message' => 'The pattern is correct! The mechanism unlocks.',
                'items_found' => $items,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'The pattern seems incorrect. Nothing happens.',
        ]);
    }

    /**
     * Read an item's content
     */
    public function readItem(Request $request, $itemKey)
    {
        // Find item by key
        $item = Item::where('item_key', $itemKey)->first();
        
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }
        
        // Check if item has content
        if (!$item->content) {
            return response()->json([
                'error' => 'There\'s nothing to read on this item'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'item_name' => $item->name,
            'content' => $item->content
        ]);
    }
}
