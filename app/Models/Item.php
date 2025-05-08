<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'object_id',
        'item_key',
        'name',
        'description',
        'takeable',
        'content',
    ];

    /**
     * Get the object this item belongs to.
     */
    public function object()
    {
        return $this->belongsTo(RoomObject::class, 'object_id');
    }
}