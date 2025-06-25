<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Patient;
use App\Affiliation;
use App\Http\Controllers\Controller;

class PatientAffiliationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function index(Patient $patient)
    {

        $affiliation = $patient->affiliations()->with('holder','plan')->first();
        

        return $affiliation;
    }

    public function transactions(Affiliation $affiliation)
    {

        
        $transactions = $affiliation->transactions()->with('transactable')->paginate(10);
       

        return $transactions;
    }

    

}
