<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\api\v1\BaseController;
use App\Http\Requests\api\Auth\LoginRequest;
use App\Http\Requests\api\Auth\RegisterRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    // Register a new user
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->success(['user' => $user], 'User registered', 201);
    }

    // Login a user
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->error('Unauthorized', 'Invalid credentials', 401);
        }

        return $this->success($this->tokenPayload($token), 'Login successful');
    }

    // Get authenticated user details
    public function profile()
    {
        return $this->success(['user' => auth()->user()]);
    }

    // Logout user
    public function logout()
    {
        auth('api')->logout();
        return $this->success([], 'Logged out');
    }
    private function tokenPayload(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ];
    }
}
