<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;
    // Définir les scopes globaux
        protected static function booted()
        {
            static::addGlobalScope('quantity', function ($query) {
                $query->where('quantite_stock', '>', 0);
            });
        }
    protected $fillable = ['libelle', 'prix', 'quantite_stock'];

    protected $dates = ['deleted_at'];

    // Masquer les champs suivants lors de la conversion en JSON
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
}
