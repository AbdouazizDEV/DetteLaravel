<?php

namespace App\Observers;

use App\Models\Dette;

class DetteObserver
{
    public function creating(Dette $dette)
    {
        // Code exécuté avant la création d'une dette
    }

    public function created(Dette $dette)
    {
        // Code exécuté après la création d'une dette
    }

    public function updating(Dette $dette)
    {
        // Code exécuté avant la mise à jour d'une dette
    }

    public function updated(Dette $dette)
    {
        // Code exécuté après la mise à jour d'une dette
    }

    public function deleting(Dette $dette)
    {
        // Code exécuté avant la suppression d'une dette
    }

    public function deleted(Dette $dette)
    {
        // Code exécuté après la suppression d'une dette
    }
}
