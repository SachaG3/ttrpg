<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'attributes',
        'is_final',
    ];

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_inventory')
            ->withPivot('quantity', 'shared_with_faction')
            ->withTimestamps();
    }

    public function factions()
    {
        return $this->belongsToMany(Faction::class, 'faction_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    public function missions()
    {
        return $this->hasMany(Mission::class, 'final_item_id');
    }
}
