<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validated_data = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
        ]);

        if (!Auth::attempt($validated_data)) {
            return response('Your credentials are incorrect', 500);
        }

        $user = User::where('email', $validated_data['email'])->first();
        $token = $user->createToken('authToken')->accessToken;
        return response([
            'user' => $user,
            'token' => $token
        ]);
    }   

    public function register(Request $request){
        $validated_data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        $validated_data['password'] = bcrypt($validated_data['password']);
        $user = User::create($validated_data);  
        $token = $user->createToken('authToken')->accessToken;
        return response([
            'user' => $user,
            'token' => $token
        ]);
    }
}
