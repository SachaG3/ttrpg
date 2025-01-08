<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    // Colonnes mass-assignable
    protected $fillable = [
        'game_id',
        'role',
        'firstname',
        'name',
        'faction_id',
        'is_spy',
        'is_hero',
        'force',
        'dexterity',
        'intelligence',
        'wisdom',
    ];

    /**
     * Relation avec la faction
     */
    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }

    /**
     * Relation avec l'inventaire (many-to-many avec la table pivot `player_inventory`)
     */
    public function inventory()
    {
        return $this->belongsToMany(Item::class, 'player_inventory')
            ->withPivot('quantity', 'shared_with_faction') // Colonnes additionnelles dans la table pivot
            ->withTimestamps(); // Timestamps dans la table pivot
    }

    /**
     * Relation avec les groupes (many-to-many avec la table pivot `group_player`)
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_player')
            ->withTimestamps();
    }

    /**
     * Relation avec les missions (hasMany filtré par `assigned_type = 1`)
     */
    public function missions()
    {
        return $this->hasMany(Mission::class, 'assigned_id')
            ->where('assigned_type', 1); // 1 = Player
    }

    /**
     * Ajout d'une méthode pour calculer les statistiques totales
     */
    public function totalStats()
    {
        return $this->force + $this->dexterity + $this->intelligence + $this->wisdom;
    }
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

}
