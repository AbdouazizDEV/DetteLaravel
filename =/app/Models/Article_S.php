<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article_S extends Model
{
    use HasFactory;

    protected $table = 'article_S';

    protected $fillable = [
        'article_id',
        'qteS',
    ];

    // Relation avec le modèle Article
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
