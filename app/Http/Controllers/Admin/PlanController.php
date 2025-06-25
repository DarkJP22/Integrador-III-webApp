<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Plan;
use Illuminate\Validation\Rule;



class PlanController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
        

       
    }
    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        $this->authorize('create', Plan::class);

        $search['q'] = request('q');



        $plans = Plan::search($search['q'])->paginate(10);



        return view('admin.plans.index', compact('plans', 'search'));

    }

    /**
     * Guardar paciente
     */
    public function store()
    {

        $this->validate(request(), [
            'title' => 'required|max:255',
            'cost' => 'required',
            'quantity' => 'required|numeric',

        ]);


        $data = request()->all();


        $plan = Plan::create($data);

        flash('Plan Creado', 'success');

        return Redirect('/admin/plans');
    }

    /**
     * Mostrar vista crear paciente
     */
    public function create()
    {
        $this->authorize('create', Plan::class);

        return view('admin.plans.create');

    }

    /**
     * Mostrar vista editar paciente
     */
    public function edit(Plan $plan)
    {
        $this->authorize('update', $plan);

       
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Actualizar Paciente
     */
    public function update(Plan $plan)
    {
        
        $this->validate(request(), [
            'title' => 'required|max:255',
            'cost' => 'required',
            'quantity' => 'required|numeric',
        ]);

        $data = request()->all();
       
        if(!isset($data['for_medic'])){
            $data['for_medic'] = 0;
        }
        if(!isset($data['for_clinic'])){
            $data['for_clinic'] = 0;
        }
        if(!isset($data['for_pharmacy'])){
            $data['for_pharmacy'] = 0;
        }

        $plan->fill($data);
        $plan->save();

        flash('Plan Actualizado', 'success');

        return Redirect('/admin/plans');
    }



    /**
     * Eliminar consulta(cita)
     */
    public function destroy(Plan $plan)
    {

        $plan->delete();

        flash('Plan Eliminado', 'success');

        return back();
    }



}
