<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AffiliationPlan;

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       

        if (auth()->user()->hasRole('asistente')) {

            return redirect('/assistant/affiliationplans');
        }

        if (auth()->user()->hasRole('clinica')) {

            return redirect('/clinic/affiliationplans');
        }

        return Redirect('/');

       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', AffiliationPlan::class);

      
        if (auth()->user()->hasRole('asistente')) {
            return view('assistant.affiliationPlans.create');
        }
        if (auth()->user()->hasRole('clinica')) {
            return view('clinic.affiliationPlans.create');
        }
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required',
            'period' => 'required',
            'cuota' => 'required|numeric',
            'discount' => 'required|numeric',
            'discount_lab' => 'required|numeric',
            'persons' => 'required|numeric',
            

        ]);
        
        $data = request()->all();
        $office = auth()->user()->isAssistant() ? auth()->user()->clinicsAssistants->first() : auth()->user()->offices->first();
        $data['office_id'] = $office->id;

        $affiliationPlan = AffiliationPlan::create($data);

      
       
    
        return redirect('/affiliationplans');

    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AffiliationPlan $plan)
    {
        $this->authorize('view', $plan);


        if (request()->wantsJson()) {
            return response($plan, 200);
        }

        if (auth()->user()->hasRole('asistente')) {

            return view('assistant.affiliationPlans.edit', [
                'plan' => $plan,
               

            ]);
        }

        if (auth()->user()->hasRole('clinica')) {

            return view('clinic.affiliationPlans.edit', [
                'plan' => $plan,
              

            ]);
        }


       
    }

    public function update(AffiliationPlan $plan)
    {
        $this->validate(request(), [
            'name' => 'required',
            'period' => 'required',
            'cuota' => 'required|numeric',
            'discount_lab' => 'required|numeric',
            'persons' => 'required|numeric',
            

        ]);

        $data = request()->all();

        // $office = auth()->user()->isAssistant() ? auth()->user()->clinicsAssistants->first() : auth()->user()->offices->first();
        // $data['office_id'] = $office->id;

        $plan->fill($data);
        $plan->save();
       

        return redirect('/affiliationplans');
       
    }

     /**
     * Eliminar consulta(cita)
     */
    public function destroy(AffiliationPlan $plan)
    {
        $result = $plan->delete();


        return back();
    }
}
