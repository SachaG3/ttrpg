<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Player;

class EnsurePlayerIsAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('player_id') || !Player::find(session('player_id'))) {
            return redirect()->route('login')->with('error', 'You must be logged in as a player.');
        }

        return $next($request);
    }
}
