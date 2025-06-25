<?php

namespace App\Http\Controllers\Api;

use App\Anthropometry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Patient;

class PatientAnthropometryController extends Controller
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

        $anthropometry = Anthropometry::where('patient_id', $patient->id)
        ->first();
        
        return $anthropometry;
        
    }

    
   
    
   
    
}
