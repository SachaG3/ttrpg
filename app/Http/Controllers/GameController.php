<?php


namespace App\Http\Controllers;

use App\Models\Choice;
use App\Models\Game;
use App\Models\GameDice;
use App\Models\Item;
use App\Models\Mission;
use App\Models\Player;
use App\Models\PlayerInventory;
use App\Models\RollDice;
use Illuminate\Http\Request;

Class GameController extends Controller {

    public function index() {

        $player = Player::find(session('player_id'));
        $mission = Mission::find($player->mission_id);

        return view('game.index', compact('player','mission'));
    }
    public function next(Request $request)
    {
        $request->validate([
            'choice_id' => 'required|exists:choices,id',
        ]);

        $player = Player::find(session('player_id'));

        if (!$player) {
            return redirect()->route('home')->with('error', 'Player not found.');
        }

        $choice = Choice::find($request->choice_id);

        if (!$choice) {
            return redirect()->route('game.start')->with('error', 'Choice not found.');
        }
        $consequence_type = $choice->consequence_type;


        $player->$consequence_type += 1;
        $player->save();

        $item= Item::where('id', $choice->item_id)->first();

        if (!$item->is_final){
            PlayerInventory::create([
                'player_id' => $player->id,
                'item_id' => $choice->item_id,
                'quantity' => 1,
                'shared_with_faction' => false,
            ]);
        }
        else{
            PlayerInventory::create([
                'player_id' => $player->id,
                'item_id' => $choice->item_id,
                'quantity' => 1,
                'shared_with_faction' => true,
            ]);
        }


        $nextMission = $choice->next_mission_id;

        if (!$nextMission) {
            $player->update(['is_finish' => true]);
            return redirect()->route('game.result')->with('success', 'You have completed all missions!');
        }

        $player->update(['mission_id' => $nextMission]);

        return redirect()->route('game.start')->with('success', 'Mission updated successfully.');
    }

    public function result()
    {
        return view('game.result');
    }

    public function create()
    {
        return view('game.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $game = Game::create($request->all());

        return redirect()->route('game.index')
            ->with('success', 'Game created successfully.');
    }
    public function start($id)
    {
        $game = Game::find($id);
        $game->update(['date_start' => now()]);

        return redirect()->route('game.index')
            ->with('success', 'Game started successfully.');
    }
    public function end($id)
    {
        $game = Game::find($id);
        $game->update(['date_end' => now()]);

        return redirect()->route('game.index')
            ->with('success', 'Game ended successfully.');
    }

    public function checkStart()
    {
        $game = Game::whereNotNull('date_start')
            ->whereNull('date_end')
            ->first();

        return response()->json(['is_start' => $game ? $game->is_start : false]);
    }


    public function submitRoll(Request $request)
    {
        $request->validate([
            'gamede_id' => 'required|exists:game_dices,id',
            'player_id' => 'required|exists:players,id',
            'dice_result' => 'required|integer|min:1|max:20',
        ]);

        // Créer un nouveau lancer de dé
        $roll = RollDice::create([
            'game_dice_id' => $request->gamede_id,
            'player_id' => $request->player_id,
            'result' => $request->dice_result,
        ]);

        // Vérifier si tous les joueurs associés à ce héros ont lancé leurs dés
        $gameDice = GameDice::find($request->gamede_id);
        $hero = $gameDice->hero;

        $associatedPlayers = Player::where('hero_id', $hero->id)->pluck('id');
        $rolls = RollDice::where('game_dice_id', $request->gamede_id)
            ->whereIn('player_id', $associatedPlayers)
            ->get();

        // Si tous les joueurs associés ont lancé leurs dés
        if ($rolls->count() === $associatedPlayers->count()) {
            // Extraire les résultats
            $results = $rolls->pluck('result');

            // Vérifier les cas critiques
            $criticalFails = $results->filter(fn($r) => $r === 1);
            $criticalSuccesses = $results->filter(fn($r) => $r === 20);

            $finalResult = null;

            if ($criticalFails->count() > 0 && $criticalSuccesses->count() > 0) {
                // Si échec critique ET réussite critique, calculer la moyenne
                $finalResult = round($results->average());
            } elseif ($criticalSuccesses->count() > 0) {
                // Si au moins une réussite critique
                $finalResult = 20;
            } elseif ($criticalFails->count() > 0) {
                // Si au moins un échec critique
                $finalResult = 1;
            } else {
                // Sinon, prendre la valeur maximale
                $finalResult = $results->max();
            }

            // Mettre à jour le résultat dans GameDice
            $gameDice->update(['dice_result' => $finalResult]);

            return response()->json([
                'success' => true,
                'message' => 'Dice roll submitted. Final result calculated.',
                'final_result' => $finalResult,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dice roll submitted. Waiting for other players.',
        ]);
    }

    public function checkRollStatus(Request $request)
    {
        $playerId = Player::find(session('player_id'));
        $heroId = $playerId->hero_id;

        // Trouver la partie en cours
        $game = Game::whereNotNull('date_start')
            ->whereNull('date_end')
            ->first();

        if (!$game) {
            return response()->json(['completed' => false, 'error' => 'No active game found'], 404);
        }

        // Trouver le dernier Gamede associé à cette partie
        $gameDe = GameDice::where('game_id', $game->id)
            ->latest()
            ->first();

        if (!$gameDe) {
            return response()->json(['completed' => false, 'error' => 'No dice roll request found'], 404);
        }

        // Vérifier si un lancer de dé a été effectué pour ce joueur et ce héros
        $rollExists = RollDice::where('gamede_id', $gameDe->id)
            ->where('player_id', $playerId)
            ->where('hero_id', $heroId)
            ->exists();

        if ($rollExists) {
            return response()->json(['completed' => false]);
        }

        // Si un lancer existe, retourner les informations du dé
        return response()->json([
            'completed' => true,
            'dice_type' => "de".$gameDe->dice_type,
        ]);
    }





}

