<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AuthenticationController extends Controller
{
    /** register new account */
    public function register(Request $request)
    {
       
        $request->validate([
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:8',
        ]);
        
        $name = $request->name ?? 'anonimo';
        
        if ($name !== 'anonimo') {
            $request->validate([
                'name' => 'string|unique:users,name',
            ]);
        }
        
        // Crear el usuario
        $user = new User();
        $user->name     = $name; // Usa el nombre predeterminado o el proporcionado
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->save();
        $user->assignRole('player');
        $data = [];
        $data['response_code']  = '200';
        $data['status']         = 'success';
        $data['message']        = 'success Register';
        return response()->json($user);
    }

    /**
     * Login Req
     */
    public function login (Request $request){
        $request->validate([
            "email" => "required|email|string",
            "password" => "required"
        ]);

        $user = User::where("email", $request->email)->first();
        if(!empty($user)){

            if(Hash::check($request->password, $user->password)){

              $token = $user->createToken('myToken')->accessToken;
                return response()->json([
                    "status" => true,
                    "message" => "Login succesful",
                    "token" => $token,
                    "data" => []
                ],200);
            }else{
                return response()->json([
                    "status" => false,
                    "message" => "Password didn't match",
                    "data" => []
                ],401);
            }
        }else{
            return response()->json([
                "status" => false,
                "message" => "invalid Email value",
                "data" => []
            ],401);
        }
    }

    /** user info */
    public function userInfo() 
    {
        try {
            $userDataList = User::latest()->paginate(10);
            $data = [];
            $data['response_code']  = '200';
            $data['status']         = 'success';
            $data['message']        = 'success get user list';
            $data['data_user_list'] = $userDataList;
            return response()->json($data);
        } catch(\Exception $e) {
            Log::info($e);
            $data = [];
            $data['response_code']  = '400';
            $data['status']         = 'error';
            $data['message']        = 'fail get user list';
            return response()->json($data);
        }
    }
    public function updateUser(Request $request, $id)
    {
        
        
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        // Encontrar al jugador por ID
        $player = User::find($id);
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        // Actualizar los datos del jugador
        $player->name = $validatedData['name'] ?? $player->name;
        $player->email = $validatedData['email'] ?? $player->email;
        if (isset($validatedData['password'])) {
            $player->password = Hash::make($validatedData['password']);
        }
        $player->save();

        return response()->json(['message' => 'Player updated successfully', 'player' => $player]);
    }
}
