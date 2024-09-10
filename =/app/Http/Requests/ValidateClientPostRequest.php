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
    

    public function rules()
    {
        return [
            'surnom' => 'required|string|max:255',
            'telephone_portable' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'user' => 'nullable|array',
            'user.login' => 'required_with:user|string|unique:users,login',
            'user.password' => 'required_with:user|string|min:8',
            'user.prenom' => 'required_with:user|string|max:255',
            'user.nom' => 'required_with:user|string|max:255',
            'user.photo' => 'required_with:user|string|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'surnom.required' => 'Le surnom est requis.',
            'telephone_portable.required' => 'Le numéro de téléphone est requis.',
            'user.login.required_with' => 'Le login est requis si un utilisateur est fourni.',
            'user.password.required_with' => 'Le mot de passe est requis si un utilisateur est fourni.',
            'user.prenom.required_with' => 'Le prénom est requis si un utilisateur est fourni.',
            'user.nom.required_with' => 'Le nom est requis si un utilisateur est fourni.',
            'user.photo.image' => 'La photo doit être une image.',
             'user.photo.mimes' => 'La photo doit être de type jpeg, png, jpg ou gif.',
            //'user.photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 411)
        );
    }

}
