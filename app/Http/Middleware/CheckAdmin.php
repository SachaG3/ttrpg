<?php

namespace App\Http\Middleware;

use App\Models\Player;
use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('player_id')) {
            return redirect()->route('home')->with('error', 'Access denied. Please log in first.');
        }

        $player = Player::find(session('player_id'));

        if (!$player || $player->role === 0) {
            return redirect()->route('home')->with('error', 'Access denied. Admins only.');
        }

        return $next($request);
    }

}
