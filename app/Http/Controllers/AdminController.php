<?php
namespace App\Http\Controllers;

use App\Models\Game;
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

    public function createHeroes()
    {
        $game = Game::whereNotNull('date_start')->whereNull('date_end')->first();

        if (!$game) {
            return response()->json(['success' => false, 'message' => 'No active game!']);
        }

        $players = Player::where('game_id', $game->id)->get();

        // Group players into heroes
        $heroes = [
            'Lyria' => [], // Wizard
            'Vale' => [], // Rogue
            'Alarion' => [], // Healer
            'Eileen' => [], // Warrior
        ];

        foreach ($players as $player) {
            $stats = [
                'strength' => $player->force,
                'dexterity' => $player->dexterity,
                'intelligence' => $player->intelligence,
                'wisdom' => $player->wisdom,
            ];

            arsort($stats);
            $mainStat = array_key_first($stats);

            switch ($mainStat) {
                case 'strength':
                    if (count($heroes['Eileen']) < 4) {
                        $heroes['Eileen'][] = $player;
                    } else {
                        $this->assignAlternateHero($player, $heroes);
                    }
                    break;

                case 'dexterity':
                    if (count($heroes['Vale']) < 4) {
                        $heroes['Vale'][] = $player;
                    } else {
                        $this->assignAlternateHero($player, $heroes);
                    }
                    break;

                case 'intelligence':
                    if (count($heroes['Lyria']) < 4) {
                        $heroes['Lyria'][] = $player;
                    } else {
                        $this->assignAlternateHero($player, $heroes);
                    }
                    break;

                case 'wisdom':
                    if (count($heroes['Alarion']) < 4) {
                        $heroes['Alarion'][] = $player;
                    } else {
                        $this->assignAlternateHero($player, $heroes);
                    }
                    break;
            }
        }

        return response()->json(['success' => true, 'heroes' => $heroes]);
    }

    private function assignAlternateHero($player, &$heroes)
    {
        $stats = [
            'strength' => $player->force,
            'dexterity' => $player->dexterity,
            'intelligence' => $player->intelligence,
            'wisdom' => $player->wisdom,
        ];

        arsort($stats);
        foreach (array_keys($stats) as $stat) {
            switch ($stat) {
                case 'strength':
                    if (count($heroes['Eileen']) < 4) {
                        $heroes['Eileen'][] = $player;
                        return;
                    }
                    break;

                case 'dexterity':
                    if (count($heroes['Vale']) < 4) {
                        $heroes['Vale'][] = $player;
                        return;
                    }
                    break;

                case 'intelligence':
                    if (count($heroes['Lyria']) < 4) {
                        $heroes['Lyria'][] = $player;
                        return;
                    }
                    break;

                case 'wisdom':
                    if (count($heroes['Alarion']) < 4) {
                        $heroes['Alarion'][] = $player;
                        return;
                    }
                    break;
            }
        }
    }
}
