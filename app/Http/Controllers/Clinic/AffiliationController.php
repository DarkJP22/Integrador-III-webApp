<?php

namespace App\Http\Controllers\Clinic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Affiliation;

class AffiliationController extends Controller
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

    public function index()
    {
        

        if (!auth()->user()->hasRole('clinica') && !auth()->user()->hasRole('asistente')) {
            
            return Redirect('/');

        }
        $office = auth()->user()->offices->first();
        $search['q'] = request('q');
        $search['office_id'] = $office->id;

        $affiliations = Affiliation::search($search)->with('holder')->latest()->paginate(10);
        
            

        if (request()->wantsJson()) {
            return response($affiliations, 200);
        }


        return view('clinic.affiliations.index', compact('affiliations', 'search'));


      
        

       
    }

    
}
