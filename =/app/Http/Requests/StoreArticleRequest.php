<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //on ne retoure trus dque si le role du user connecter est admin
        //return auth()->check() && auth()->user()->role === 'admin';
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
            'libelle' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0',
        ];
    }
    public function messages(){
        return [
            'libelle.required' => 'Le libelle est obligatoire.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être supérieur ou égal à 0.',
            'quantite_stock.required' => 'La quantité en stock est obligatoire.',
            'quantite_stock.integer' => 'La quantité en stock doit être un entier.',
            'quantite_stock.min' => 'La quantité en stock doit être supérieur ou égal à 0.',
        ];  //messages d'erreurs ici ici vous pouvez ajouter autant de messages que vous voulez ici par exemple pour le champ libelle, prix, qteStock etc. ici vous pouvez aussi ajouter
    }
                                                                                                                                                        
}
