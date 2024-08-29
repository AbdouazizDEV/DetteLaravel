<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
class ClientWithUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::factory()->count(3)->create()->each(function ($user) {
            Client::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
