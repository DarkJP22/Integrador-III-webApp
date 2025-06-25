<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       

        if (auth()->user()->hasRole('asistente')) {

            return redirect('/assistant/affiliations');
        }

        if (auth()->user()->hasRole('clinica')) {

            return redirect('/clinic/affiliations');
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
        $this->authorize('create', Affiliation::class);

      
        if (auth()->user()->hasRole('asistente')) {
            return view('assistant.affiliations.create');
        }
        if (auth()->user()->hasRole('clinica')) {
            return view('clinic.affiliations.create');
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
        $validated = request()->validate([
            'affiliation_plan_id' => 'required',
            'patient_id' => 'required',
            'inscription' => 'required',
            'patients' => 'nullable',
            'office_id' => 'required'

           
        ]);
        
    

        $affiliation = Affiliation::create($validated);

        if( isset($validated['patients']) ){
            $affiliation->patients()->sync($validated['patients']);
        }

        $affiliation->load('plan','patients', 'holder');

        if (request()->wantsJson()) {
            return response($affiliation, 201);
        }
      
       
    
        return redirect('/affiliations');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Affiliation $affiliation)
    {
        $this->authorize('view', $affiliation);

        $affiliation->load('patients','holder', 'plan');

        if (request()->wantsJson()) {
            return response($affiliation, 200);
        }

        if (auth()->user()->hasRole('asistente')) {

            return view('assistant.affiliations.edit', [
                'affiliation' => $affiliation,
               

            ]);
        }

        if (auth()->user()->hasRole('clinica')) {

            return view('clinic.affiliations.edit', [
                'affiliation' => $affiliation,
              

            ]);
        }


       
    }

    

    public function update(Affiliation $affiliation)
    {
        $validated = request()->validate([
            'affiliation_plan_id' => 'required',
            'patient_id' => 'required',
            'inscription' => 'required',
            'patients' => 'nullable',
            'office_id' => 'required'
           
        ]);

        

       
        $affiliation->fill($validated);
        $affiliation->save();

        if( isset($validated['patients']) ){
            $affiliation->patients()->sync($validated['patients']);
        }

        $affiliation->load('plan','patients', 'holder');

        if (request()->wantsJson()) {
            return response($affiliation, 200);
        }
       

        return redirect('/affiliations');
       
    }

     /**
     * Eliminar consulta(cita)
     */
    public function destroy(Affiliation $affiliation)
    {
        $result = $affiliation->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);
        }

        return redirect('/affiliations');
    }


     /**
     * Active a user.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function active(Affiliation $affiliation)
    {
        $affiliation->update([
            'active' => 1
        ]);

        return back();
    }

    /**
     * Inactive a user.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function inactive(Affiliation $affiliation)
    {
        $affiliation->update([
            'active' => 0
        ]);

        return back();
    }
    
}
