<?php


namespace App\Http\Controllers;

use App\Models\Choice;
use App\Models\Game;
use App\Models\Item;
use App\Models\Mission;
use App\Models\Player;
use App\Models\PlayerInventory;
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
        $players = Player::where('game_id', $game->id)->get();

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

        return redirect()->route('game.index')
            ->with('success', 'Missions randomized successfully.');
    }



}

