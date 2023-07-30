<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        try{

            $credentials = $request->validate([

                'email' => ['required', 'email'],
                'password' => ['required'],

            ]);

            if (Auth::attempt($credentials)) {

                $user = $request->user();
                $token = $user->createToken('authToken')->plainTextToken;

                return response()->json([

                    'message' => 'Login successful.',
                    'token' => $token,

                ], 200);

            }else{

                throw new \Exception('The provided credentials do not match our records.');

            }

        }catch (\Exception $e) {

            return response()->json([

                'message' => $e->getMessage(),

            ], 500);

        }
    }

}
