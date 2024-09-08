<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = ['dette_id', 'montant', 'date'];

    public function dette()
    {
        return $this->belongsTo(Dette::class, 'dette_id');
    }
}
