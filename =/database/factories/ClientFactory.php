<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'surnom' => $this->faker->unique()->userName,
            'telephone_portable' => $this->faker->unique()->phoneNumber,
            'user_id' => null, // Par défaut, un client n'a pas de compte
        ];
    }
}
