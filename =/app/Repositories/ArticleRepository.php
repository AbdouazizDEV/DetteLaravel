<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function all()
    {
        return Article::all();
    }

    public function find($id)
    {
        return Article::find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Article::create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $article = Article::find($id);
            if ($article) {
                $article->update($data);
            }
            return $article;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $article = Article::find($id);
            if ($article) {
                $article->delete();
            }
            return $article;
        });
    }
}
