<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Speciality;

class MedicSpecialityController extends Controller
{

    public function index()
    {
        return Speciality::orderBy('name')->get();
        
    }
   
    
   
    
}
