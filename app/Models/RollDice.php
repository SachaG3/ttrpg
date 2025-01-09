<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollDice extends Model
{
    use HasFactory;

    protected $table = 'lancerdé'; // Nom de la table
    protected $fillable = ['game_dice_id', 'player_id', 'result'];

    /**
     * Relation avec le modèle GameDice.
     */
    public function gameDice()
    {
        return $this->belongsTo(GameDice::class, 'game_dice_id');
    }

    /**
     * Relation avec le modèle Player.
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
