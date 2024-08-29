<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['surnom', 'telephone_portable', 'user_id'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'user_id' => 'integer',
    ];

    // Relation 1-to-1 avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
