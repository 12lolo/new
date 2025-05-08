<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuzzleCombination extends Model
{
    use HasFactory;

    protected $fillable = [
        'combination_key',
        'required_items',
        'target_object_key',
        'result_action',
        'result_item_key',
        'success_message',
    ];

    /**
     * Get the required items as an array
     */
    public function getRequiredItemsArrayAttribute()
    {
        return explode(',', $this->required_items);
    }
}
