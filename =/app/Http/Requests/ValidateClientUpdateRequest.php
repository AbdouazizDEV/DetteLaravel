<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateClientUpdateRequest extends FormRequest
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
            'surnom' => [
                'sometimes',
                'max:255',
                Rule::unique('clients')->ignore($this->client)
            ],
            'telephone_portable' => [
                'sometimes',
                'regex:/^(\+?[0-9]{1,4})?([0-9]{10})$/',
                Rule::unique('clients')->ignore($this->client)
            ],
        ];
    }
    public function messages()
    {
        return [
            'surnom.unique' => 'Ce surnom est déjà utilisé.',
            'telephone_portable.regex' => 'Le format du numéro de téléphone est invalide.',
            'telephone_portable.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ];
    }
}
