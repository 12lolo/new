<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_session_id',
        'item_key',
    ];

    /**
     * Get the player session this inventory item belongs to.
     */
    public function playerSession(): BelongsTo
    {
        return $this->belongsTo(PlayerSession::class);
    }
}
