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
        // ======================= ROOM 1: ENTRANCE ROOM =======================
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
            'name' => 'Door to Mystery Room',
            'description' => 'A solid wooden door with a keyhole. It seems to be locked.',
            'is_container' => false,
            'is_locked' => true,
            'required_item' => 'sleutel',
        ]);

        $painting = RoomObject::create([
            'room_id' => $room1->id,
            'object_key' => 'schilderij',
            'name' => 'Mysterious Painting',
            'description' => 'A painting of a landscape with four colored flowers. The frame seems loose.',
            'is_container' => true,
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

        $behindPainting = RoomObject::create([
            'room_id' => $room1->id,
            'object_key' => 'achter-schilderij',
            'name' => 'Behind the Painting',
            'description' => 'A hidden compartment behind the painting reveals a small safe.',
            'is_container' => true,
            'parent_object_id' => $painting->id,
        ]);

        $paintingSafe = RoomObject::create([
            'room_id' => $room1->id,
            'object_key' => 'kluis',
            'name' => 'Small Wall Safe',
            'description' => 'A small safe with a 4-digit code lock.',
            'is_container' => true,
            'is_locked' => true,
            'code' => '1472',
            'parent_object_id' => $behindPainting->id,
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
            'content' => 'The path to freedom lies behind the locked door. Find the key. But beware, there are more puzzles ahead. Look for the pattern: 1-4-7-2',
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
            'content' => 'Check the cabinet carefully. Not everything is as it seems. For the wall safe, follow the colors: Blue-Yellow-Red-Green.',
        ]);

        Item::create([
            'object_id' => $painting->id,
            'item_key' => 'hint_bloemen',
            'name' => 'Flower Arrangement',
            'description' => 'The flowers in the painting are arranged in a specific pattern.',
            'takeable' => false,
            'content' => 'The blue flower is in position 1, yellow in 4, red in 7, and green in 2.',
        ]);

        Item::create([
            'object_id' => $paintingSafe->id,
            'item_key' => 'lab_sleutel',
            'name' => 'Laboratory Key',
            'description' => 'A modern key with "Lab" etched on it.',
            'takeable' => true,
        ]);

        // ======================= ROOM 2: MYSTERY ROOM =======================
        $room2 = Room::create([
            'room_key' => 'room2',
            'name' => 'Mystery Room',
            'description' => 'A room with strange symbols on the walls. There\'s a door leading to the exit and another door labeled "Laboratory".',
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

        $labDoor = RoomObject::create([
            'room_id' => $room2->id,
            'object_key' => 'deur naar lab',
            'name' => 'Laboratory Door',
            'description' => 'A sturdy metal door labeled "Laboratory".',
            'is_container' => false,
            'is_locked' => true,
            'required_item' => 'lab_sleutel',
        ]);

        $desk = RoomObject::create([
            'room_id' => $room2->id,
            'object_key' => 'bureau',
            'name' => 'Antique Desk',
            'description' => 'A beautiful wooden desk with several drawers.',
            'is_container' => true,
        ]);

        // Create desk drawers
        $topDrawer = RoomObject::create([
            'room_id' => $room2->id,
            'object_key' => 'bovenste-la',
            'name' => 'Top Drawer',
            'description' => 'The top drawer of the antique desk.',
            'is_container' => true,
            'parent_object_id' => $desk->id,
        ]);

        $middleDrawer = RoomObject::create([
            'room_id' => $room2->id,
            'object_key' => 'middelste-la',
            'name' => 'Middle Drawer',
            'description' => 'The middle drawer of the antique desk. It has a simple lock.',
            'is_container' => true,
            'is_locked' => true,
            'required_item' => 'small_key',
            'parent_object_id' => $desk->id,
        ]);

        $bottomDrawer = RoomObject::create([
            'room_id' => $room2->id,
            'object_key' => 'onderste-la',
            'name' => 'Bottom Drawer',
            'description' => 'The bottom drawer of the antique desk.',
            'is_container' => true,
            'parent_object_id' => $desk->id,
        ]);

        // Create items
        Item::create([
            'object_id' => $bookshelf->id,
            'item_key' => 'boek',
            'name' => 'Red Book',
            'description' => 'A thick red book with gold lettering.',
            'takeable' => true,
            'content' => 'The final code is: 1-2-3-4. The library holds ancient secrets.',
        ]);

        Item::create([
            'object_id' => $safe->id,
            'item_key' => 'exit_key',
            'name' => 'Exit Key',
            'description' => 'A golden key that looks important.',
            'takeable' => true,
        ]);

        Item::create([
            'object_id' => $topDrawer->id,
            'item_key' => 'small_key',
            'name' => 'Small Key',
            'description' => 'A tiny key, perhaps for a drawer.',
            'takeable' => true,
        ]);

        Item::create([
            'object_id' => $middleDrawer->id,
            'item_key' => 'library_card',
            'name' => 'Library Card',
            'description' => 'An ornate card with "Library Access" written on it.',
            'takeable' => true,
        ]);

        Item::create([
            'object_id' => $bottomDrawer->id,
            'item_key' => 'strange_note',
            'name' => 'Strange Note',
            'description' => 'A note with strange symbols.',
            'takeable' => true,
            'content' => 'When elements combine: H₂O + NaCl, seek the answer in the laboratory.',
        ]);

        // ======================= ROOM 3: LABORATORY =======================
        $room3 = Room::create([
            'room_key' => 'room3',
            'name' => 'Laboratory',
            'description' => 'A scientific laboratory with various equipment and chemicals. There\'s a door leading to a library.',
            'is_final' => false,
        ]);

        // Create objects in Room 3
        $workbench = RoomObject::create([
            'room_id' => $room3->id,
            'object_key' => 'werkbank',
            'name' => 'Workbench',
            'description' => 'A large workbench with various scientific equipment.',
            'is_container' => true,
        ]);

        $chemicalCabinet = RoomObject::create([
            'room_id' => $room3->id,
            'object_key' => 'chemicalienkast',
            'name' => 'Chemical Cabinet',
            'description' => 'A cabinet containing various chemicals and compounds.',
            'is_container' => true,
        ]);

        $computerStation = RoomObject::create([
            'room_id' => $room3->id,
            'object_key' => 'computer',
            'name' => 'Computer Station',
            'description' => 'An old computer that seems to be working.',
            'is_container' => true,
            'is_locked' => true,
            'code' => 'salt',  // A password rather than numeric code
        ]);

        $libraryDoor = RoomObject::create([
            'room_id' => $room3->id,
            'object_key' => 'deur naar bibliotheek',
            'name' => 'Library Door',
            'description' => 'A wooden door with "Library" etched on a brass plate.',
            'is_container' => false,
            'is_locked' => true,
            'required_item' => 'library_card',
        ]);

        // Create items for Laboratory
        Item::create([
            'object_id' => $workbench->id,
            'item_key' => 'microscoop',
            'name' => 'Microscope',
            'description' => 'A powerful microscope. Something is under the lens.',
            'takeable' => false,
            'content' => 'Looking through the microscope, you see tiny letters spelling: "The garden key is hidden in the oldest book."',
        ]);

        Item::create([
            'object_id' => $workbench->id,
            'item_key' => 'reageerbuisjes',
            'name' => 'Test Tubes',
            'description' => 'A rack of test tubes with colored liquids.',
            'takeable' => false,
        ]);

        Item::create([
            'object_id' => $chemicalCabinet->id,
            'item_key' => 'sodium_chloride',
            'name' => 'Sodium Chloride',
            'description' => 'A bottle labeled "NaCl" (table salt).',
            'takeable' => true,
            'content' => 'The chemical formula for table salt is NaCl.',
        ]);

        Item::create([
            'object_id' => $computerStation->id,
            'item_key' => 'password_hint',
            'name' => 'Password Hint',
            'description' => 'A sticky note on the computer.',
            'takeable' => false,
            'content' => 'Password hint: What do you get when you combine H₂O + NaCl?',
        ]);

        Item::create([
            'object_id' => $computerStation->id,
            'item_key' => 'ancient_manuscript',
            'name' => 'Ancient Manuscript',
            'description' => 'A digital copy of an ancient manuscript with garden diagrams.',
            'takeable' => true,
            'content' => 'The secret garden pattern is: Rose, Lily, Orchid, Sunflower. The first letters reveal the truth.',
        ]);

        // ======================= ROOM 4: LIBRARY =======================
        $room4 = Room::create([
            'room_key' => 'room4',
            'name' => 'Library',
            'description' => 'A vast library with towering bookshelves and ancient tomes. There\'s a door leading to a garden.',
            'is_final' => false,
        ]);

        // Create objects in Library
        $historySection = RoomObject::create([
            'room_id' => $room4->id,
            'object_key' => 'geschiedenis-sectie',
            'name' => 'History Section',
            'description' => 'Shelves filled with history books from various eras.',
            'is_container' => true,
        ]);

        $readingDesk = RoomObject::create([
            'room_id' => $room4->id,
            'object_key' => 'leeshoek',
            'name' => 'Reading Desk',
            'description' => 'A comfortable desk for reading, with a lamp and some books already laid out.',
            'is_container' => true,
        ]);

        $oldestBook = RoomObject::create([
            'room_id' => $room4->id,
            'object_key' => 'oudste-boek',
            'name' => 'Ancient Tome',
            'description' => 'The oldest book in the library, dating back centuries.',
            'is_container' => true,
            'parent_object_id' => $historySection->id,
        ]);

        $gardenDoor = RoomObject::create([
            'room_id' => $room4->id,
            'object_key' => 'deur naar tuin',
            'name' => 'Garden Door',
            'description' => 'A glass door leading to a beautiful garden outside.',
            'is_container' => false,
            'is_locked' => true,
            'required_item' => 'garden_key',
        ]);

        $secretBookcase = RoomObject::create([
            'room_id' => $room4->id,
            'object_key' => 'geheime-boekenkast',
            'name' => 'Peculiar Bookcase',
            'description' => 'A bookcase that seems slightly different from the others. There are four empty slots labeled with flower names.',
            'is_container' => true,
            'is_locked' => true,
            'code' => 'RLOS',  // Rose, Lily, Orchid, Sunflower - first letters
        ]);

        // Create items for Library
        Item::create([
            'object_id' => $oldestBook->id,
            'item_key' => 'garden_key',
            'name' => 'Garden Key',
            'description' => 'An ornate key with flower designs.',
            'takeable' => true,
        ]);

        Item::create([
            'object_id' => $readingDesk->id,
            'item_key' => 'book_roses',
            'name' => 'Book of Roses',
            'description' => 'A beautifully illustrated book about roses.',
            'takeable' => true,
            'content' => 'The rose symbolizes love and passion. Place this book first.',
        ]);

        Item::create([
            'object_id' => $readingDesk->id,
            'item_key' => 'book_lilies',
            'name' => 'Book of Lilies',
            'description' => 'A comprehensive guide to lily varieties.',
            'takeable' => true,
            'content' => 'The lily symbolizes purity and renewal. Place this book second.',
        ]);

        Item::create([
            'object_id' => $historySection->id,
            'item_key' => 'book_orchids',
            'name' => 'Book of Orchids',
            'description' => 'An encyclopedia of exotic orchids.',
            'takeable' => true,
            'content' => 'The orchid symbolizes luxury and strength. Place this book third.',
        ]);

        Item::create([
            'object_id' => $historySection->id,
            'item_key' => 'book_sunflowers',
            'name' => 'Book of Sunflowers',
            'description' => 'A botanical study of sunflowers.',
            'takeable' => true,
            'content' => 'The sunflower symbolizes adoration and loyalty. Place this book fourth.',
        ]);

        Item::create([
            'object_id' => $secretBookcase->id,
            'item_key' => 'crystal_key',
            'name' => 'Crystal Key',
            'description' => 'A key made entirely of crystal that shimmers with magical energy.',
            'takeable' => true,
        ]);

        // ======================= ROOM 5: GARDEN =======================
        $room5 = Room::create([
            'room_key' => 'room5',
            'name' => 'Secret Garden',
            'description' => 'A beautiful garden with colorful flowers and a central fountain. There\'s a mysterious crystal pedestal.',
            'is_final' => false,
        ]);

        // Create objects in Garden
        $fountain = RoomObject::create([
            'room_id' => $room5->id,
            'object_key' => 'fontein',
            'name' => 'Garden Fountain',
            'description' => 'A beautiful fountain with crystal-clear water.',
            'is_container' => true,
        ]);

        $flowerBed = RoomObject::create([
            'room_id' => $room5->id,
            'object_key' => 'bloemperk',
            'name' => 'Flower Bed',
            'description' => 'A circular arrangement of vibrant flowers.',
            'is_container' => true,
        ]);

        $stoneBench = RoomObject::create([
            'room_id' => $room5->id,
            'object_key' => 'stenen-bank',
            'name' => 'Stone Bench',
            'description' => 'An old stone bench beneath a blossoming tree.',
            'is_container' => true,
        ]);

        $crystalPedestal = RoomObject::create([
            'room_id' => $room5->id,
            'object_key' => 'kristallen-voetstuk',
            'name' => 'Crystal Pedestal',
            'description' => 'A pedestal made of crystal with a keyhole shaped indentation.',
            'is_container' => false,
            'is_locked' => true,
            'required_item' => 'crystal_key',
        ]);

        // Create items for Garden
        Item::create([
            'object_id' => $fountain->id,
            'item_key' => 'ancient_coin',
            'name' => 'Ancient Coin',
            'description' => 'A coin at the bottom of the fountain, too old to identify.',
            'takeable' => true,
        ]);

        Item::create([
            'object_id' => $flowerBed->id,
            'item_key' => 'special_flower',
            'name' => 'Glowing Flower',
            'description' => 'A flower that emits a soft, magical glow.',
            'takeable' => true,
        ]);

        Item::create([
            'object_id' => $stoneBench->id,
            'item_key' => 'riddle_note',
            'name' => 'Riddle Note',
            'description' => 'A weathered note with a riddle.',
            'takeable' => true,
            'content' => 'When the crystal key meets its match, the final path will be revealed. Place the glowing flower and ancient coin upon the activated pedestal.',
        ]);

        Item::create([
            'object_id' => $crystalPedestal->id,
            'item_key' => 'dimensional_portal',
            'name' => 'Dimensional Portal',
            'description' => 'A shimmering portal that appears after the pedestal is activated.',
            'takeable' => false,
        ]);

        // Create an additional hidden object that appears only when specific items are combined
        $portalActivated = RoomObject::create([
            'room_id' => $room5->id,
            'object_key' => 'geactiveerde-portal',
            'name' => 'Activated Portal',
            'description' => 'The fully activated dimensional portal, ready for travel.',
            'is_container' => false,
            'parent_object_id' => $crystalPedestal->id,
        ]);

        Item::create([
            'object_id' => $portalActivated->id,
            'item_key' => 'final_key',
            'name' => 'Final Key',
            'description' => 'A key that seems to be made of pure energy.',
            'takeable' => true,
        ]);

        // ======================= EXIT ROOM =======================
        $exit = Room::create([
            'room_key' => 'exit',
            'name' => 'Freedom',
            'description' => 'Congratulations! You\'ve escaped the room and solved all the puzzles.',
            'is_final' => true,
        ]);

        // Create a special object in the exit room
        $exitPedestal = RoomObject::create([
            'room_id' => $exit->id,
            'object_key' => 'victory-stand',
            'name' => 'Victory Pedestal',
            'description' => 'A golden pedestal commemorating your escape room victory.',
            'is_container' => true,
        ]);

        Item::create([
            'object_id' => $exitPedestal->id,
            'item_key' => 'certificate',
            'name' => 'Certificate of Completion',
            'description' => 'A beautifully designed certificate with your name on it.',
            'takeable' => true,
            'content' => 'This certifies that you have successfully completed the Ultimate Escape Room Challenge!',
        ]);
    }
}
