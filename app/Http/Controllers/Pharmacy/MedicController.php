<?php

namespace App\Http\Controllers\Pharmacy;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Speciality;
use App\Repositories\MedicRepository;
use Carbon\Carbon;
use App\Http\Controllers\Controller;


class MedicController extends Controller
{
    public function __construct(MedicRepository $medicRepo) {
        
        $this->medicRepo = $medicRepo;
      
        $this->middleware('auth');

        View::share('specialities', Speciality::all());
    }
    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('farmacia')) {
            return redirect('/');
        }
        
        $search['q'] = request('q');

        $search['province'] = request('province');
        $search['canton'] = request('canton');
        $search['district'] = request('district');

    

        $medics = $this->medicRepo->findAll($search);



        return view('pharmacy.medics.index', compact('medics', 'search'));

    }

    

}
