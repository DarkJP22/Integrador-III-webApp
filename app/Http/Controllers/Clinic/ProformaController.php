<?php

namespace App\Http\Controllers\Clinic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Proforma;
use App\Repositories\MedicRepository;

class ProformaController extends Controller
{
    public function __construct(MedicRepository $medicRepo)
    {
        $this->middleware('auth');
        $this->medicRepo = $medicRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');
        
        $office = auth()->user()->offices->first();
        
        //$medics = $this->medicRepo->findAllByOffice($office->id);
        $medicos = $office->users()->whereHas('roles', function($q){
            $q->where('name', 'medico');
        })->get();

        $adminsClinic = $office->users()->whereHas('roles', function ($q) {
            $q->Where('name', 'clinica');
        })->get();


        $admins = $adminsClinic->map(function ($item, $key) {
            $item->name = $item->name." (Administrador)";
            return $item;
        });
      
        $medics = $medicos->merge($admins);
        
        $search['office'] = request('office') ? request('office') : $office->id;

        $proformas = Proforma::with('user')->search($search)->latest()->paginate(10);


        return view('clinic.proformas.index', compact('proformas', 'search', 'medics'));
    }

  
}
