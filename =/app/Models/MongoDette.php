<?php
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MongoDette 
{
    protected $connection = 'mongodb'; // Assurez-vous que c'est la bonne connexion
    protected $collection = 'dettes_archive'; // Le nom de la collection MongoDB
    protected $fillable = ['id_dette', 'montant_dette', 'total_paye', 'date_archive'];
}
