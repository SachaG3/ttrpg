<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string|max:255',
        ]);

        $player = Player::where('name', $request->nickname)->first();

        if ($player) {
            session(['player_id' => $player->id]);
            return redirect()->route('game.start')->with('success', 'Logged in successfully.');
        }

        return back()->withErrors(['nickname' => 'Player not found.']);
    }
}
