<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['montant', 'clientId', 'date'];
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

    // Si vous utilisez 'client_id' dans votre code, mappez-le correctement avec la colonne 'clientId'
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dettes', 'dette_id', 'article_id')
            ->withPivot('qteVente', 'prixVente')
            ->withTimestamps();
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'dette_id');
    }
}
