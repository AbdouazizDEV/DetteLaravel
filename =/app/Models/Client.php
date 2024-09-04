<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // Définir la relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Définir les scopes globaux
    protected static function booted()
    {
        static::addGlobalScope('activeUser', function ($query) {
            if (request()->query('active') === 'oui') {
                $query->whereHas('user', function ($q) {
                    $q->where('active', true);
                });
            } elseif (request()->query('active') === 'non') {
                $query->whereHas('user', function ($q) {
                    $q->where('active', false);
                });
            }
        });
    }

    // Définir les scopes locaux
    public function scopeActiveUser($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('active', true);
        });
    }

    public function scopeInactiveUser($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('active', false);
        });
    }

    protected $fillable = ['surnom', 'telephone_portable', 'user_id'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'user_id' => 'integer',
    ];
}
