<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomObject;
use App\Models\Item;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Room 1
        $room1 = Room::create([
            'room_key' => 'room1',
            'name' => 'Entrance Room',
            'description' => 'You find yourself in a dimly lit room. There appears to be no way out except for a locked door.',
            'is_final' => false,
        ]);

        // Create objects in Room 1
        $cabinet = RoomObject::create([
            'room_id' => $room1->id,
            'object_key' => 'kabinet2',
            'name' => 'Old Cabinet',
            'description' => 'A dusty cabinet with two doors - left and right.',
            'is_container' => true,
        ]);

        $table = RoomObject::create([
            'room_id' => $room1->id,
            'object_key' => 'tafel',
            'name' => 'Table',
            'description' => 'A wooden table with scratch marks on the surface.',
            'is_container' => true,
        ]);

        $door = RoomObject::create([
            'room_id' => $room1->id,
            'object_key' => 'deur naar room2',
            'name' => 'Door to Room 2',
            'description' => 'A solid wooden door with a keyhole. It seems to be locked.',
            'is_container' => false,
            'is_locked' => true,
            'required_item' => 'sleutel',
        ]);

        // Create nested objects
        $leftDoor = RoomObject::create([
            'room_id' => $room1->id,
            'object_key' => 'leftdoor',
            'name' => 'Left Cabinet Door',
            'description' => 'The left door of the cabinet.',
            'is_container' => true,
            'parent_object_id' => $cabinet->id,
        ]);

        $rightDoor = RoomObject::create([
            'room_id' => $room1->id,
            'object_key' => 'rightdoor',
            'name' => 'Right Cabinet Door',
            'description' => 'The right door of the cabinet.',
            'is_container' => true,
            'parent_object_id' => $cabinet->id,
        ]);

        // Create items
        Item::create([
            'object_id' => $leftDoor->id,
            'item_key' => 'sleutel',
            'name' => 'Key',
            'description' => 'A rusty old key. Might open something nearby.',
            'takeable' => true,
        ]);

        Item::create([
            'object_id' => $leftDoor->id,
            'item_key' => 'oude brief',
            'name' => 'Old Letter',
            'description' => 'A yellowed piece of paper with faded handwriting.',
            'takeable' => true,
            'content' => 'The path to freedom lies behind the locked door. Find the key.',
        ]);

        Item::create([
            'object_id' => $rightDoor->id,
            'item_key' => 'stof',
            'name' => 'Dust',
            'description' => 'Just a lot of dust. Nothing interesting here.',
            'takeable' => false,
        ]);

        Item::create([
            'object_id' => $table->id,
            'item_key' => 'notitie',
            'name' => 'Note',
            'description' => 'A handwritten note.',
            'takeable' => true,
            'content' => 'Check the cabinet carefully. Not everything is as it seems.',
        ]);

        // Create Room 2
        $room2 = Room::create([
            'room_key' => 'room2',
            'name' => 'Mystery Room',
            'description' => 'A room with strange symbols on the walls. There\'s a door leading to the exit.',
            'is_final' => false,
        ]);

        // Create objects in Room 2
        $bookshelf = RoomObject::create([
            'room_id' => $room2->id,
            'object_key' => 'boekenkast',
            'name' => 'Bookshelf',
            'description' => 'A tall bookshelf filled with old books.',
            'is_container' => true,
        ]);

        $safe = RoomObject::create([
            'room_id' => $room2->id,
            'object_key' => 'safe',
            'name' => 'Wall Safe',
            'description' => 'A metal safe embedded in the wall with a 4-digit keypad.',
            'is_container' => true,
            'is_locked' => true,
            'code' => '1234',
        ]);

        $exitDoor = RoomObject::create([
            'room_id' => $room2->id,
            'object_key' => 'deur naar exit',
            'name' => 'Exit Door',
            'description' => 'A door with a sign that says "EXIT".',
            'is_container' => false,
            'is_locked' => true,
            'required_item' => 'exit_key',
        ]);

        // Create items
        Item::create([
            'object_id' => $bookshelf->id,
            'item_key' => 'boek',
            'name' => 'Red Book',
            'description' => 'A thick red book with gold lettering.',
            'takeable' => true,
            'content' => 'The final code is: 1-2-3-4',
        ]);

        Item::create([
            'object_id' => $safe->id,
            'item_key' => 'exit_key',
            'name' => 'Exit Key',
            'description' => 'A golden key that looks important.',
            'takeable' => true,
        ]);

        // Create Exit Room
        $exit = Room::create([
            'room_key' => 'exit',
            'name' => 'Freedom',
            'description' => 'Congratulations! You\'ve escaped the room.',
            'is_final' => true,
        ]);
    }
}
