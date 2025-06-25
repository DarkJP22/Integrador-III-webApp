<?php

namespace App\Http\Controllers\Api;

use App\Documentation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Patient;

class PatientDocumentationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Request $request, Patient $patient)
    {

        $documentations = Documentation::where('patient_id', $patient->id)
        ->limit(50)->get();
        
        return $documentations;
        
    }

    
   
    
   
    
}
