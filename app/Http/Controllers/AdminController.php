<?php
namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameDice;
use App\Models\Mission;
use App\Models\Player;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function panel(Request $request)
    {
        $currentGame = Game::whereNotNull('date_start')->whereNull('date_end')->first();
        $players = Player::where('game_id', $currentGame->id ?? null)->get();

        if ($request->ajax()) {
            return response()->json(['players' => $players]);
        }

        return view('admin.panel', compact('currentGame', 'players'));
    }

    public function startGame(Request $request)
    {
        $game = Game::create([
            'name' => $request->input('name'),
            'date_start' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Game started!', 'game' => $game]);
    }

    public function stopGame(Request $request)
    {
        $game = Game::whereNotNull('date_start')->whereNull('date_end')->first();
        if ($game) {
            $game->update(['date_end' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Game stopped!']);
    }
    public function gameRandomise()
    {
        $game = Game::whereNotNull('date_start')
            ->whereNull('date_end')
            ->first();

        if (!$game) {
            return redirect()->route('game.index')
                ->with('error', 'No active game found.');
        }

        $missions = Mission::where('start_mission', true)->get(); // Récupérer toutes les missions de départ
        $players = Player::where('game_id', $game->id)
            ->where('role', 0) // Exclure les administrateurs
            ->get();

        if ($missions->isEmpty()) {
            return redirect()->route('game.index')
                ->with('error', 'No missions available to assign.');
        }

        $unassignedPlayers = $players; // Liste initiale des joueurs sans mission
        $assignedMissions = []; // Liste des missions déjà attribuées

        while ($unassignedPlayers->isNotEmpty()) {
            // Vérifier si toutes les missions ont été attribuées
            if (count($assignedMissions) >= $missions->count()) {
                // Réinitialiser la liste des missions déjà attribuées
                $assignedMissions = [];
            }

            foreach ($unassignedPlayers as $key => $player) {
                $availableMissions = $missions->whereNotIn('id', $assignedMissions);

                if ($availableMissions->isEmpty()) {
                    break; // Sortir de la boucle si aucune mission disponible
                }

                $mission = $availableMissions->random();

                // Assigner la mission au joueur
                $player->update(['mission_id' => $mission->id]);

                $assignedMissions[] = $mission->id;

                // Retirer le joueur de la liste des joueurs sans mission
                unset($unassignedPlayers[$key]);
            }
        }

        // Activer le jeu
        $game->update(['is_start' => true]);

        return response()->json(['success' => true, 'message' => 'Missions randomized successfully.']);
    }


    public function createHeroes()
    {
        $game = Game::whereNotNull('date_start')->whereNull('date_end')->first();

        if (!$game) {
            return response()->json(['error' => true, 'message' => 'No active game found.']);
        }

        // Créer les héros avec des statistiques basées sur les moyennes des joueurs
        $heroesData = [
            ['name' => 'Lyria', 'role' => 'Wizard', 'stat' => 'intelligence'],
            ['name' => 'Vale', 'role' => 'Rogue', 'stat' => 'dexterity'],
            ['name' => 'Alarion', 'role' => 'Healer', 'stat' => 'wisdom'],
            ['name' => 'Eileen', 'role' => 'Warrior', 'stat' => 'strength'],
        ];

        $heroes = [];
        foreach ($heroesData as $heroData) {

            $players = Player::where('game_id', $game->id)
                ->where('isHero', false)
                ->where('role', '!=', 1)
                ->get();

            // Calculer la moyenne de la statistique dominante
            $averageStat = $players->avg($heroData['stat']);

            $heroes[] = Player::create([
                'firstname' => $heroData['name'],
                'name' => $heroData['name'],
                'faction_id' => null,
                'isHero' => true,
                'game_id' => $game->id,
                $heroData['stat'] => $averageStat + 1, // Stat principale +1
                'strength' => $heroData['stat'] === 'strength' ? $averageStat + 1 : $players->avg('strength'),
                'dexterity' => $heroData['stat'] === 'dexterity' ? $averageStat + 1 : $players->avg('dexterity'),
                'intelligence' => $heroData['stat'] === 'intelligence' ? $averageStat + 1 : $players->avg('intelligence'),
                'wisdom' => $heroData['stat'] === 'wisdom' ? $averageStat + 1 : $players->avg('wisdom'),
            ]);
        }

        // Assigner les joueurs aux héros
        $players = Player::where('game_id', $game->id)->where('isHero', false)->get();
        $playersPerHero = ceil($players->count() / count($heroes));
        $playerGroups = $players->chunk($playersPerHero);

        foreach ($heroes as $index => $hero) {
            if (isset($playerGroups[$index])) {
                foreach ($playerGroups[$index] as $player) {
                    $player->update(['hero_id' => $hero->id]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Heroes created and players assigned successfully.',
            'heroes' => $heroes,
        ]);
    }



    public function heroPanel(Request $request)
    {
        $currentGame = Game::whereNotNull('date_start')->whereNull('date_end')->first();
        $players = Player::where('game_id', $currentGame->id)->where('isHero', true)->get();

        if ($request->ajax()) {
            return response()->json(['players' => $players]);
        }

        return view('admin.panel-hero', compact('currentGame', 'players'));
    }
    public function requestRoll(Request $request)
    {
        $request->validate([
            'hero_id' => 'required|exists:players,id',
            'dice_type' => 'required|integer',
        ]);

        $game = Game::whereNotNull('date_start')->whereNull('date_end')->first();

        if (!$game) {
            return response()->json(['error' => true, 'message' => 'No active game found.']);
        }

        $gamede = GameDice::create([
            'game_id' => $game->id,
            'hero_id' => $request->hero_id,
            'dice_type' => 20,
        ]);

        return response()->json(['success' => true, 'message' => 'Dice roll requested.', 'gamede' => $gamede]);
    }
}
