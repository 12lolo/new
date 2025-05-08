<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomObject extends Model
{
    use HasFactory;

    protected $table = 'objects'; // Keep the original table name

    protected $fillable = [
        'room_id',
        'object_key',
        'name',
        'description',
        'is_container',
        'is_locked',
        'required_item',
        'code',
        'parent_object_id',
    ];

    /**
     * Get the room this object belongs to.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the parent object if this is a nested object.
     */
    public function parentObject(): BelongsTo
    {
        return $this->belongsTo(RoomObject::class, 'parent_object_id');
    }

    /**
     * Get the child objects if this is a container.
     */
    public function childObjects(): HasMany
    {
        return $this->hasMany(RoomObject::class, 'parent_object_id');
    }

    /**
     * Get the items that belong to this object.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'object_id');
    }
}
