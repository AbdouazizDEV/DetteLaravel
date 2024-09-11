<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\ArticleServiceInterface;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        // Retrieve the 'disponible' filter from the query parameters
        $disponible = $request->query('disponible');
        
        // Pass it as an array to the ArticleService's all() method
        $filters = [];
        if ($disponible) {
            $filters['disponible'] = $disponible;
        }

        // Call the all() method with the filters array
        $articles = $this->articleService->all($filters);

        return compact('articles');
    }
    public function show($id)
    {
        return $this->articleService->find($id);
    }

    public function store(Request $request)
    {
        return $this->articleService->create($request->all());
    }

    public function update(Request $request, $id)
    {
        return $this->articleService->update($id, $request->all());
    }

    public function destroy($id)
    {
        return $this->articleService->delete($id);
    }
}
