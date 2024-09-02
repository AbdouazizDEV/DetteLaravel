<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'article_S' => 'required|array|min:1',
            'article_S.*.article_id' => 'required|exists:articles,id',
            'article_S.*.qteS' => 'required|integer|min:1',
        ];
    }
    public function messages(): array{
        return [
            'article_S.required' => 'Le tableau articleS est requis',
            'article_S.*.article_id.required' => 'L\'id de l\'article est requis',
            'article_S.*.article_id.exists' => 'L\'id de l\'article n\'existe pas',
            'article_S.*.qteS.required' => 'La quantité est requise',
            'article_S.*.qteS.integer' => 'La quantité doit être un entier',
            'article_S.*.qteS.min' => 'La quantité doit être supérieure ou égale à 1',
        ];
    }
}
