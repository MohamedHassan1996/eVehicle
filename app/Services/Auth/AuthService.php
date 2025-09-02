<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\InactiveAccountException;
use App\Exceptions\Auth\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService
{
    public function __construct()
    {
        // Constructor injection for UserPermissionService
    }
    public function login(array $data)
    {
        $user = User::where('username', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new InvalidCredentialsException();
        }


        if (!$user->isActive()) {
            throw new InactiveAccountException();
        }



        // Generate a new token (DO NOT return it directly)
        $token = $user->createToken('auth_token')->plainTextToken;


        return [
            'profile' => $user,
            'tokenDetails' => [
                'token' => $token,
                'expiresIn' => 60 * 60 * 8
            ],
        ];

    }

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            //$user->tokens()->delete(); // Revoke all tokens
            $user->currentAccessToken()->delete();
        }
    }
}
