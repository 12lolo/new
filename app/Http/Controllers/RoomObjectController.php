<?php

namespace App\Http\Controllers;

use App\Models\RoomObject;
use Illuminate\Http\Request;

class RoomObjectController extends Controller
{
    public function look($roomKey, $objectKey)
    {
        $object = RoomObject::where('object_key', $objectKey)->with('childObjects', 'items')->first();

        if (!$object) {
            return response()->json(['error' => 'Object not found'], 404);
        }

        return response()->json([
            'location' => $object->name,
            'objects' => $object->childObjects->pluck('name'),
            'items' => $object->items->pluck('name'),
        ]);
    }

    public function interact(Request $request, $roomKey, $objectKey, $action)
    {
        $object = RoomObject::where('object_key', $objectKey)->first();

        if (!$object) {
            return response()->json(['error' => 'Object not found'], 404);
        }

        if ($action === 'take-key') {
            $item = $object->items()->where('name', 'key')->first();

            if (!$item) {
                return response()->json(['error' => 'No key found in this object']);
            }

            $inventory = $request->session()->get('inventory', []);
            $inventory[] = 'key';
            $request->session()->put('inventory', $inventory);

            return response()->json([
                'message' => 'Je hebt de sleutel gepakt!',
                'inventory' => $inventory,
            ]);
        }

        return response()->json(['error' => 'Invalid action'], 400);
    }
}
