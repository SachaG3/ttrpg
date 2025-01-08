<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerInventory extends Model
{
    use HasFactory;

    protected $table = 'player_inventory';

    protected $fillable = [
        'player_id',
        'item_id',
        'quantity',
        'shared_with_faction',
    ];

    /**
     * Relation avec le modèle Player.
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Relation avec le modèle Item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
