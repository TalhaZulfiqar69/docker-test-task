<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UserRegistrationRequest;


class AuthenticationController extends Controller
{
    //
    public function registration(UserRegistrationRequest $request) {
        try {
            $user = new User();
            $user->email = $request->input('email');
            $user->name = $request->input('name');
            $user->password = Hash::make($request->input('password'));
            $user->save();

            if ($user) {
                return response()->json([
                    'message' => 'Account created successfully.',
                    'success' => true,
                    'data' => null,
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Failed to create the account.',
                    'success' => false,
                    'data' => null,
                ], 500);
            }
        } catch (\Exception $e) {
            // Handle the exception, log it, and return an appropriate error response.
            return response()->json([
                'message' => 'An error occurred during account creation.',
                'success' => false,
                'data' => null,
            ], 500);
        }
    }

    public function login(Request $request) {
        // try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            $user = User::where('email', $request->input('email'))->first();
    
            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['User not found.'],
                ]);
            }
    
            if (!Hash::check($request->input('password'), $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['Credentials do not match.'],
                ]);
            }
    
            $user->token = $user->createToken('API')->plainTextToken;
    
            return response()->json([
                'message' => 'Login successful.',
                'success' => true,
                'data' => $user,
            ], 200);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => 'An error occurred during login.',
        //         'success' => false,
        //         'data' => null,
        //     ], 500);
        // }
    }
}
