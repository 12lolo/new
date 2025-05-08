<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_key',
        'name',
        'description',
        'is_final',
    ];

    /**
     * Get the objects that belong to this room.
     */
    public function objects()
    {
        return $this->hasMany(RoomObject::class);
    }

    /**
     * Get the top-level objects that belong to this room.
     */
    public function topLevelObjects()
    {
        return $this->hasMany(RoomObject::class)->whereNull('parent_object_id');
    }
}
