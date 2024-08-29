<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255|unique:users,login,' . $this->route('id'),
        ];
    }
    /**
     * Messages d'erreur personnalisés.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'login.unique' => 'Ce login est déjà utilisé.',
        ];
    }
}
