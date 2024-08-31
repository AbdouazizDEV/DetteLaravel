<?php

namespace App\Policies;

use App\Models\User;

class ClientPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        // Create a new policy instance
    }
    public function create(User $user)
    {
        return $user->role === 'boutiquier';
    }

}
