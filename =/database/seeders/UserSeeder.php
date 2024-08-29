<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::factory()->create([
            'login' => 'admin',
            'password' => bcrypt('password'), // Changez le mot de passe pour des raisons de sécurité
            'role' => 'admin',
        ]);

        User::factory()->create([
            'login' => 'boutiquier',
            'password' => bcrypt('password'), // Changez le mot de passe pour des raisons de sécurité
            'role' => 'boutiquier',
        ]);
    }
}
