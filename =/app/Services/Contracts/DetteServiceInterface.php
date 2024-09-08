<?php

namespace App\Services\Contracts;

use App\Models\Dette;

interface DetteServiceInterface
{
    public function createDette(array $data): Dette;
}
