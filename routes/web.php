<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FactionController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\JsonImportController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;


Route::get('/up', function () {
    return response()->json(['status' => 'up up up up']);
});

Route::get('/',[HomeController::class,'index']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/import',[JsonImportController::class,'import'])->name('import');

Route::resources([
    'players' => PlayerController::class,
    'factions' => FactionController::class,
    'items' => ItemController::class,
    'groups' => GroupController::class,
    'missions' => MissionController::class,
]);

Route::middleware('player.auth')->group(function () {
    Route::get('/start',[GameController::class,'index'])->name('game.start');
});
