<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $fields= $request->validate([
            'email'=> 'required|email',
            'password'=> 'required|string',
        ]);
        $user=User::where('email',$fields['email'])->first();
      
        $hashedPassword = hash('sha256', $fields['password']);
        if(!$user || !($hashedPassword ===$user->password)){
            return response([
                "message"=>"informations erronées"
            ],401);
        }
        $token=$user->createToken('appToken')->plainTextToken;
        $obj=[
            "id"=> $user->id,
            "name"=> $user->name,
            "email"=> $user->email,
            "role"=> $user->role,
            "token"=>$token,
        ];
        return response(
          $obj
        ,200);

    }


    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return [
          "message"=>"logged out"
        ];
      }
}
