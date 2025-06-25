<?php

namespace App\Http\Controllers\Lab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Proforma;
use App\Repositories\MedicRepository;
use App\User;

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
        if (!auth()->user()->hasRole('laboratorio')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');
        
        $office = auth()->user()->offices->first();
        
        $search['office'] = request('office') ? request('office') : $office->id;
        $selectedMedic = request('medic') ? User::find(request('medic')) : null;

        $proformas = Proforma::with('user')->search($search)->latest()->paginate(10);


        return view('lab.proformas.index', compact('proformas', 'search', 'selectedMedic'));
    }

  
}
