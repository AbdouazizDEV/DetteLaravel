<?php

namespace App\Repositories\Contracts;

use App\Models\Dette;

interface DetteRepositoryInterface
{
    public function create(array $data): Dette;
}
