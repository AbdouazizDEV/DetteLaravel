<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DetteService;
class ArchiveController extends Controller
{

    protected $detteService;
    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }
    

    

}