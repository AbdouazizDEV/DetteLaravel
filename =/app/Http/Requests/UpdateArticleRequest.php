<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'libelle' => 'sometimes|required|string|max:255',
            'prix' => 'sometimes|required|numeric|min:0',
            'qteStock' => 'sometimes|required|integer|min:0',
        ];
    }
    //message
    public function messages(){

        return [
            'libelle.required' => 'Le libelle est obligatoire.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être supérieur ou égal à 0.',
            'qteStock.required' => 'La quantité en stock est obligatoire.',
            'qteStock.integer' => 'La quantité en stock doit être un entier.',
            'qteStock.min' => 'La quantité en stock doit être supérieur ou égal à 0.',
        ];  //messages d'erreurs ici ici vous pouvez ajouter autant de messages que vous voulez ici par exemple pour
    }


}
