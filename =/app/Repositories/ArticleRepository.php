<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function all($disponible = null, $perPage = 10)
    {
        return Article::all();
    }

    /* public function filterByAvailability($isAvailable)
    {
        if ($isAvailable) {
            return Article::where('quantite_stock', '>', 0)->get();
        } else {
            return Article::where('quantite_stock', '=', 0)->get();
        }
    } */
    public function filterByAvailability(bool $isAvailable): Collection
    {
        return Article::where('quantite_stock', $isAvailable ? '>' : '=', 0)->get();
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
