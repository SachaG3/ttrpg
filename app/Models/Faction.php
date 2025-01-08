<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'score'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function inventory()
    {
        return $this->belongsToMany(Item::class, 'faction_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function missions()
    {
        return $this->hasMany(Mission::class, 'assigned_id')
            ->where('assigned_type', 3); // 3 = Faction
    }
}
