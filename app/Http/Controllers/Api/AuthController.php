<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register new user and return sanctum token
     * Body: name, email, password, password_confirmation (optional)
     */
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user = User::create([
            'name' => $v->validated()['name'],
            'email' => $v->validated()['email'],
            'password' => Hash::make($v->validated()['password']),
        ]);

        // create a token for API usage
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login user and return sanctum token
     * Body: email, password
     */
    public function login(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            // use a generic message
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        // create token (single-use name)
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Authenticated',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout (revoke current token)
     * Requires auth:sanctum middleware
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // revoke current access token
        $current = $request->user()->currentAccessToken();
        if ($current) {
            $current->delete();
        } else {
            // fallback: delete all tokens (if currentAccessToken not available)
            $request->user()->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * (Optional) return current authenticated user
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
