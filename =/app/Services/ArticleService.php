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

    public function all()
    {
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
