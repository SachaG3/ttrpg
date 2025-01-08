<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function players()
    {
        return $this->belongsToMany(Player::class, 'group_player')
            ->withTimestamps();
    }

    public function missions()
    {
        return $this->hasMany(Mission::class, 'assigned_id')
            ->where('assigned_type', 2); // 2 = Group
    }
}
