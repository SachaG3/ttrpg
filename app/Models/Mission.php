<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'assigned_type',
        'assigned_id',
        'start_mission',
    ];

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}
