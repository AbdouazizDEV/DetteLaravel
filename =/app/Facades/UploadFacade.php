<?php

// app/Facades/Upload.php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UploadFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'uploadservice';
    }
}