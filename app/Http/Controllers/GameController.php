<?php


namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Mission;
use App\Models\Player;
use Illuminate\Http\Request;

Class GameController extends Controller {

    public function index() {
        $player = Player::find(session('player_id'));

        return view('game.index', compact('player'));
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
    public function gameStart($id)
    {
        $game = Game::find($id);
        $game->update(['date_start' => now()]);

        return redirect()->route('game.index')
            ->with('success', 'Game started successfully.');
    }
    public function gameEnd($id)
    {
        $game = Game::find($id);
        $game->update(['date_end' => now()]);

        return redirect()->route('game.index')
            ->with('success', 'Game ended successfully.');
    }
    public function gameRandomise(Game $game)
    {
        $game = Game::find($game->id);
        $missions = Mission::where('start_mission', true)->get(); // Récupérer toutes les missions de départ
        $players = Player::where('game_id', $game->id)->get();

        if ($missions->count() < $players->count()) {
            return redirect()->route('game.index')
                ->with('error', 'Not enough missions to assign one to each player.');
        }

        $assignedMissions = [];

        foreach ($players as $player) {
            $availableMissions = $missions->whereNotIn('id', $assignedMissions);

            if ($availableMissions->isEmpty()) {
                return redirect()->route('game.index')
                    ->with('error', 'Not enough unassigned missions.');
            }

            $mission = $availableMissions->random();

            $player->update(['mission_id' => $mission->id]);

            $assignedMissions[] = $mission->id;
        }

        return redirect()->route('game.index')
            ->with('success', 'Missions randomized successfully.');

    }


}

