<?php
// app/Http/Requests/UpdateStockRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'article_S' => 'required|array|min:1',
            'article_S.*.article_id' => 'required|exists:articles,id',
            'article_S.*.qteS' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'article_S.required' => 'Le tableau des articles est requis.',
            'article_S.min' => 'Le tableau des articles doit contenir au moins un article.',
            'article_S.*.article_id.required' => 'L\'ID de l\'article est requis.',
            'article_S.*.article_id.exists' => 'L\'ID de l\'article doit exister en base de données.',
            'article_S.*.qteS.required' => 'La quantité à ajouter est requise.',
            'article_S.*.qteS.integer' => 'La quantité à ajouter doit être un entier.',
            'article_S.*.qteS.min' => 'La quantité à ajouter doit être positive.',
        ];
    }
}
