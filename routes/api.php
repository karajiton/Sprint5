<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;


Route::middleware('auth:api')->group(function () {
Route::post('/players', [PlayerController::class, 'createPlayer']);
Route::put('/players/{id}', [PlayerController::class, 'updatePlayer']);
Route::post('/players/{id}/games', [PlayerController::class, 'rollDice']);
Route::delete('/players/{id}/games', [PlayerController::class, 'deleteGames']);
Route::get('/players', [PlayerController::class, 'listPlayers']);
Route::get('/players/{id}/games', [PlayerController::class, 'listGames']);
Route::get('/players/ranking', [PlayerController::class, 'ranking']);
Route::get('/players/ranking/loser', [PlayerController::class, 'worstPlayer']);
Route::get('/players/ranking/winner', [PlayerController::class, 'bestPlayer']);
});