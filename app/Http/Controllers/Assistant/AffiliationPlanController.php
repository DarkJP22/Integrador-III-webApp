<?php

namespace App\Http\Controllers\Assistant;

use Illuminate\Http\Request;
use App\AffiliationPlan;
use App\Http\Controllers\Controller;

class AffiliationPlanController extends Controller
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
        

        if (!auth()->user()->hasRole('clinica')) {
            
            return Redirect('/assistant/affiliations');

        }

        $office = auth()->user()->clinicsAssistants->first();
        $search['q'] = request('q');
        $search['office_id'] = $office->id;

        $plans = AffiliationPlan::search($search)->latest()->paginate(10);
        
            

        if (request()->wantsJson()) {
            return response($plans, 200);
        }


        return view('assistant.affiliationPlans.index', compact('plans', 'search'));


        
    }
}
