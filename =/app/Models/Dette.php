<?php
// app/Models/Dette.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Dette extends Model
{
    use HasFactory;
    protected $table = 'dettes'; // Spécifiez explicitement le nom de la table

    protected $fillable = ['montant', 'clientId', 'date'];

    // Ajout des attributs calculés à l'array $appends
    protected $appends = ['montant_restant', 'montantDU'];

    protected static function booted()
    {
        static::addGlobalScope('statut', function (Builder $builder) {
            if (request()->has('statut')) {
                if (request('statut') === 'Solde') {
                    $builder->where('montant', 0);
                } elseif (request('statut') === 'NonSolde') {
                    $builder->where('montant', '>', 0);
                }
            }
        });
    }

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    // Relation avec les articles
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dettes', 'dette_id', 'article_id')
            ->withPivot('qteVente', 'prixVente')
            ->withTimestamps();
    }

    // Relation avec les paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'dette_id');
    }

    // Méthode calculée : montant restant
    public function getMontantRestantAttribute()
    {
        $totalPaiements = $this->paiements()->sum('montant');
        return $this->montant - $totalPaiements;
    }

    // Méthode calculée : montant dû
    public function getMontantDuAttribute()
    {
        return $this->montant; // Le montant initial de la dette
    }
}
