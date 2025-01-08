<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'option_text',
        'consequence_type',
        'consequence_value',
        'next_mission_id',
        'item_id',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function nextMission()
    {
        return $this->belongsTo(Mission::class, 'next_mission_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
