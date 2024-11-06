<?php

namespace App\Http\Controllers;
use app\Models\Player;
use app\Models\Game;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function createPlayer(Request $request){
        $request->validate(['nickName' => 'required|string']);
        $player = Player::create(['nickName' => $request->nickName]);
        return response()->json($player, 201);
    }
    public function updatePlayer(Request $request, $id){
        $player = Player::findOrFail($id);
        $request->validate(['nickName' => 'required|string']);
        $player->update(['nickName' => $request->nickName]);
        return response()->json($player);
    }
    public function rollDice($id){
        $player = Player::findOrFail($id);
        $diceOne = rand(1, 6);
        $diceTwo = rand(1, 6); 
        $win = $diceOne + $diceTwo == 7; 
        $game = Game::create(['player_id' => $id, 'dice_one' => $diceOne, 'dice_two' => $diceTwo, 'win' => $win]);

        
        $totalGames = $player->games()->count();
        $totalWins = $player->games()->where('win', true)->count();
        $player->success_rate = $totalGames ? ($totalWins / $totalGames) * 100 : 0;
        $player->save();

        return response()->json($game, 201);
    }
    public function deleteGames($id){
        $player = Player::findOrFail($id);
        $player->games()->delete();
        $player->success_rate = 0; // Reiniciar porcentaje de éxito
        $player->save();

        return response()->json(['message' => 'Tiradas eliminadas']);
    }
    public function listPlayers(){
        return Player::all();
    }
    public function listGames($id){
        $player = Player::findOrFail($id);
        return $player->games;
    }
    public function ranking(){
        return Player::orderByDesc('success_rate')->get();
    }
    public function worstPlayer(){
        return Player::orderBy('success_rate')->first();
    }
    public function bestPlayer(){
        return Player::orderByDesc('success_rate')->first();
    }
}

