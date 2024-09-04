<?php

namespace App\Services\Contracts;

interface AuthentificationServiceInterface
{
    public function authenticate(array $credentials);
    public function logout();
    //public function register(array $data): array;

}
