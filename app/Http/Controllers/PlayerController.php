<?php

namespace App\Http\Controllers;

use App\Models\Faction;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::all();
        return view('players.index', compact('players'));
    }

    public function create()
    {
        $factions = Faction::all();
        return view('players.create',compact('factions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
        ]);

        $currentGame = Game::whereNull('date_end')->orderBy('date_start', 'desc')->first();

        if (!$currentGame) {
            return redirect()->back()->with('error', 'No active game found. Please start a game first.');
        }

        // Créer le joueur et l'associer au jeu en cours
        $player = Player::create([
            'name' => $request->nickname,
            'faction_id' => null,
            'firstname' => $request->firstname,
            'is_spy' => false,
            'isHero' => false,
            'force' => 0,
            'dexterity' => 0,
            'intelligence' => 0,
            'wisdom' => 0,
            'game_id' => $currentGame->id??null,
        ]);

        session(['player_id' => $player->id]);

        return redirect()->route('game.start')->with('success', 'Player created successfully and logged in.');
    }



    public function show(Player $player)
    {
        return view('players.show', compact('player'));
    }

    public function edit(Player $player)
    {
        return view('players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'faction_id' => 'nullable|exists:factions,id',
            'is_spy' => 'boolean',
        ]);

        $player->update($request->all());
        return redirect()->route('players.index')->with('success', 'Player updated successfully.');
    }

    public function destroy(Player $player)
    {
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted successfully.');
    }
}
