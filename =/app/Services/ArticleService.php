<?php

namespace App\Services;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Services\Contracts\ArticleServiceInterface;

class ArticleService implements ArticleServiceInterface
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function all(array $filters = [])
    {
        if (isset($filters['disponible'])) {
            if ($filters['disponible'] === 'oui') {
                return $this->articleRepository->filterByAvailability(true);
            } elseif ($filters['disponible'] === 'non') {
                return $this->articleRepository->filterByAvailability(false);
            }
        }
        return $this->articleRepository->all();
    }


    public function find($id)
    {
        return $this->articleRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->articleRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->articleRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->articleRepository->delete($id);
    }
}
