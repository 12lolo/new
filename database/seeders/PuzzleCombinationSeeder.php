<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PuzzleCombination;

class PuzzleCombinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Combination puzzle for the crystal pedestal in the garden
        PuzzleCombination::create([
            'combination_key' => 'activate_pedestal',
            'required_items' => 'crystal_key,ancient_coin,special_flower',
            'target_object_key' => 'kristallen-voetstuk',
            'result_action' => 'unlock',
            'result_item_key' => 'final_key',
            'success_message' => 'As you place the glowing flower and ancient coin on the pedestal after unlocking it with the crystal key, a powerful surge of energy creates a dimensional rift. A glowing key materializes in the center of the portal!'
        ]);

        // Book arrangement puzzle in the library
        PuzzleCombination::create([
            'combination_key' => 'flower_books',
            'required_items' => 'book_roses,book_lilies,book_orchids,book_sunflowers',
            'target_object_key' => 'geheime-boekenkast',
            'result_action' => 'unlock',
            'result_item_key' => 'crystal_key',
            'success_message' => 'As you arrange the flower books in the correct order, the bookcase clicks and slides open, revealing a hidden compartment containing a crystal key!'
        ]);

        // Chemical combination in the laboratory
        PuzzleCombination::create([
            'combination_key' => 'chemical_reaction',
            'required_items' => 'sodium_chloride,test_tube,water',
            'target_object_key' => 'werkbank',
            'result_action' => 'create',
            'result_item_key' => 'salt_solution',
            'success_message' => 'You successfully create a salt solution. The chemical reaction causes the liquid to glow briefly, revealing hidden writing at the bottom of the test tube!'
        ]);
    }
}
