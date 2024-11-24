<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthenticationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;

Route::post("register", [AuthenticationController::class, "register"]);
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

Route::group(['namespace' => 'App\Http\Controllers\API'],function()
{
    // --------------- register and login ----------------//
    Route::controller(AuthenticationController::class)->group(function () {
        
        Route::post('register', 'register')->name('register');
        Route::post('login', 'login')->name('login');
        Route::post('login/out', 'logOut')->name('login/out');
    });
    // ------------------ get data ----------------------//
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('get-user', 'userInfo')->middleware('auth:api')->name('get-user');
    });
});