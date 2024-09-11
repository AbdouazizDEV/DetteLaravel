<?php

namespace App\Repositories\Contracts;
use Illuminate\Support\Collection;
interface ArticleRepositoryInterface
{
    public function all($disponible = null, $perPage = 10);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function filterByAvailability(bool $isAvailable): Collection;

}
