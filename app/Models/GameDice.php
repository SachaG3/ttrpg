<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameDice extends Model
{
    use HasFactory;

    protected $table = 'gamedé';
    protected $fillable = ['game_id', 'hero_id', 'dice_type'];

    /**
     * Relation avec le modèle Game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Relation avec le modèle Player (Héros).
     */
    public function hero()
    {
        return $this->belongsTo(Player::class, 'hero_id');
    }

    /**
     * Relation avec les lancers de dés associés.
     */
    public function rolls()
    {
        return $this->hasMany(RollDice::class, 'game_dice_id');
    }
}
