<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'telephone_portable' => ['required', 'regex:/^(\+?[0-9]{1,4})?([0-9]{10})$/', 'unique:clients,telephone_portable'],
        ];
    }
    public function messages()
    {
        return [
            'surnom.required' => 'Le surnom est obligatoire.',
            'surnom.unique' => 'Ce surnom est déjà utilisé.',
            'telephone_portable.required' => 'Le numéro de téléphone portable est obligatoire.',
            'telephone_portable.regex' => 'Le format du numéro de téléphone est invalide.',
            'telephone_portable.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ];
    }
}
