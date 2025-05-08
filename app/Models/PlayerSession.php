<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'current_room_id',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the current room of this player.
     */
    public function currentRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'current_room_id');
    }

    /**
     * Get player's inventory items.
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(PlayerInventory::class);
    }
}
