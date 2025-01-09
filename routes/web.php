<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\controller;
use App\Http\Controllers\DeeController;
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

Route::get('/',[HomeController::class,'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/wait',[HomeController::class,'wait'])->name('wait');
Route::get('/game/check-start', [GameController::class, 'checkStart'])->name('game.checkStart');

Route::get('/de20',[DeeController::class,'de20'])->name('de20');
Route::get('/de6',[DeeController::class,'de6'])->name('de6');
Route::get('/de4',[DeeController::class,'de4'])->name('de4');

Route::middleware('admin')->group(function () {
    Route::get('/import', [JsonImportController::class, 'import'])->name('import');
    Route::get('/game/end', [GameController::class, 'end'])->name('game.end');
    Route::post('/admin/rendomize', [AdminController::class, 'gameRandomise'])->name('admin.rendomize');
    Route::get('/admin/panel', [AdminController::class, 'panel'])->name('admin.panel');
    Route::post('/admin/start-game', [AdminController::class, 'startGame'])->name('admin.startGame');
    Route::post('/admin/stop-game', [AdminController::class, 'stopGame'])->name('admin.stopGame');
    Route::post('/admin/create-heroes', [AdminController::class, 'createHeroes'])->name('admin.createHeroes');
    Route::get('/admin/panel/hero', [AdminController::class, 'heroPanel'])->name('admin.panel.hero');
    Route::post('/admin/game/request-roll', [AdminController::class, 'requestRoll'])->name('admin.gamede.requestRoll');
});

Route::resources([
    'players' => PlayerController::class,
    'factions' => FactionController::class,
    'items' => ItemController::class,
    'groups' => GroupController::class,
    'missions' => MissionController::class,
]);

Route::middleware('player.auth')->group(function () {
    Route::get('/start',[GameController::class,'index'])->name('game.start');
    Route::post('/next',[GameController::class,'next'])->name('game.next');
    Route::get('/result',[GameController::class,'result'])->name('game.result');
    Route::get('/game/check-roll-status', [GameController::class, 'checkRollStatus'])->name('game.checkRollStatus');

});
