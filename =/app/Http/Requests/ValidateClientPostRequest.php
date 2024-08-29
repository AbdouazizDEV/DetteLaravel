<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateClientPostRequest extends FormRequest
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
            'surnom' => 'required|unique:clients,surnom|max:255',
            'telephone_portable' => [
                'required',
                'unique:clients,telephone_portable',
                'regex:/^(77|76|70|75|78)[0-9]{7}$/'
            ],
            'user_id' => 'exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'surnom.required' => 'Le surnom est obligatoire.',
            'surnom.unique' => 'Ce surnom est déjà utilisé.',
            'telephone_portable.required' => 'Le numéro de téléphone portable est obligatoire.',
            'telephone_portable.regex' => 'Le numéro de téléphone doit commencer par 77, 76, 70, 75 ou 78 et contenir exactement 9 chiffres.',
            'telephone_portable.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'user_id.required' => 'L\'utilisateur est obligatoire.',
            'user_id.exists' => 'Cet utilisateur n\'existe pas.'
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
       throw new HttpResponseException(
         response()->json([
            'message' => 'Erreur de validation',
            'errors' => $validator->errors()
         ], 422)
        );
    }

}
