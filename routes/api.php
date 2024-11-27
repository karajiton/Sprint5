<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthenticationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PlayerController;

Route::post("register", [AuthenticationController::class, "register"]);
Route::post("login", [AuthenticationController::class, "login"]);
//Route::middleware('auth:api')->group(function () {
Route::put('/players/{id}', [AuthenticationController::class, 'updateUser']);
Route::post('/players/{id}/games', [PlayerController::class, 'rollDice']);
Route::delete('/players/{id}/games', [PlayerController::class, 'deleteGames']);
Route::get('/players/{id}/games', [PlayerController::class, 'listGames']);

//});

//Route::middleware('auth:api', 'role:admin')->group(function (){
Route::get('/players', [PlayerController::class, 'listPlayers']);
    Route::get('/players/ranking', [PlayerController::class, 'ranking']);
    Route::get('/players/ranking/loser', [PlayerController::class, 'worstPlayer']);
    Route::get('/players/ranking/winner', [PlayerController::class, 'bestPlayer']);
//});