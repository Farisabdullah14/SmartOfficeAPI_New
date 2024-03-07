<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request){

        $credentials = $request->only('name', 'pass');

        $user = User::where('username', $credentials['name'])->first();

        if (!$user || $credentials['pass'] !== $user->password) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'user' => $user->id,
            'name' => $user->name,
        ]);
    }}
