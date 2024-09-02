<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    
    public function isAdmin(User $user)
    {
        return $user->role === 'admin';
    }

    public function isBoutiquier(User $user)
    {
        // dd($user->role);
        return $user->role === 'boutiquier';
    }

   /*  public function isClient(User $user)
    {
        return $user->role->name === 'client';
    } */
}