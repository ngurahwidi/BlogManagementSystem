<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $mappedUser = 
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ];
    
            return response()->json([
                'status'=> 'Success',
                'code'=> 201,
                'message' => 'User Created',
                'data' => $mappedUser
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400);
        }
      
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status'=> 'Error',
                    'code'=> 401,
                    'message' => 'Email or Password Invalid',
                    'data' => []
                ], 401);
            }

            $user = Auth::user();

            $mappedUser = 
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ];
    
            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Login Successful',
                'data' => $mappedUser,
                'access_token' => $token
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400);
        }
       
    }

    public function logout(Request $request)
    {
        try {
          JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status'=> 'Success',
                'code'=> 200,
                'message' => 'Successfully logged out',
                'data' => []
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=> 'Error',
                'code'=> 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400);
        }
       
    }
}