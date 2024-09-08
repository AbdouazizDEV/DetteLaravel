<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDetteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Assurez-vous de gérer correctement l'autorisation
    }

    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|numeric|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement.montant' => 'nullable|numeric|min:0|max:' . $this->montant,
        ];
    }

    public function messages()
    {
        return [
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant doit être supérieur ou égal à 0.',
            'clientId.required' => 'L\'ID du client est requis.',
            'clientId.exists' => 'Le client doit exister en base de données.',
            'articles.required' => 'Vous devez sélectionner au moins un article.',
            'articles.*.articleId.required' => 'L\'ID de l\'article est requis.',
            'articles.*.articleId.exists' => 'L\'article doit exister en base de données.',
            'articles.*.qteVente.required' => 'La quantité de vente est requise.',
            'articles.*.qteVente.numeric' => 'La quantité de vente doit être un nombre.',
            'articles.*.qteVente.min' => 'La quantité de vente doit être supérieure à 0.',
            'articles.*.prixVente.required' => 'Le prix de vente est requis.',
            'articles.*.prixVente.numeric' => 'Le prix de vente doit être un nombre.',
            'articles.*.prixVente.min' => 'Le prix de vente doit être supérieur ou égal à 0.',
            'paiement.montant.numeric' => 'Le montant du paiement doit être un nombre.',
            'paiement.montant.min' => 'Le montant du paiement doit être supérieur ou égal à 0.',
            'paiement.montant.max' => 'Le montant du paiement ne peut pas dépasser le montant de la dette.',
        ];
    }
}
