<?php

namespace App\Http\Controllers\Medic;

use App\Proforma;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ProformaController extends Controller
{
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
        $this->authorize('viewAny', Proforma::class);
        
        if( !auth()->user()->belongsToCentroMedico() && !auth()->user()->subscriptionPlanHasFe() ){ //|| (!auth()->user()->permissionCentroMedico()
            return redirect('/medic/changeaccounttype');
        }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['office'] = request('office');



        $proformas = auth()->user()->proformas()->with('user')->search($search)->latest()->paginate(10);


        return view('proformas.index', compact('proformas', 'search'));
    }

  
}
