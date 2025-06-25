<?php

namespace App\Http\Controllers\Assistant;

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
        if (!auth()->user()->hasRole('asistente')) {
            return redirect('/');
        }
        
        $search['q'] = request('q');

        $office = auth()->user()->clinicsAssistants->first();


        $medics = $this->medicRepo->findAllByOffice($office->id, $search);



        return view('assistant.medics.index', compact('medics', 'office', 'search'));

    }

    

}
