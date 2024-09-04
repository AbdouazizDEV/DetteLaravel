<?php

namespace App\Services;

use App\Services\Contracts\AuthentificationServiceInterface;
use Illuminate\Support\Facades\Auth;

class PassportAuthentificationService implements AuthentificationServiceInterface
{
    public function authenticate(array $credentials): array
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            return ['token' => $token, 'user' => $user];
        }

        return ['error' => 'Invalid credentials'];
    }

    public function logout(): void
    {
        Auth::user()->token()->revoke();
    }
}
