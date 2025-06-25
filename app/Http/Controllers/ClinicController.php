<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ClinicRepository;
use App\Repositories\OfficeRepository;
use App\Repositories\MedicRepository;

class ClinicController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ClinicRepository $clinicRepo, OfficeRepository $officeRepo, MedicRepository $medicRepo)
    {
     
        $this->clinicRepo = $clinicRepo;
        $this->officeRepo = $officeRepo;
        $this->medicRepo = $medicRepo;
        $this->middleware('auth')->except('index');
        
    }


    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        $clinics = [];
        $search = [];

        if (request()->all()) {
            if (trim(request('q')) != '' || request('province') != '' || request('canton') != '' || request('district') != '' || request('lat') != '' || request('lon') != '') {

                $search['q'] = trim(request('q'));
                $search['province'] = request('province');
                $search['canton'] = request('canton');
                $search['district'] = request('district');
                $search['lat'] = request('lat');
                $search['lon'] = request('lon');


                $clinics = $this->clinicRepo->findAll($search);


             
            }
        }

        return view('clinics.index', compact('clinics', 'search'));

    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function reservation($office_id)
    {
        if (!auth()->user()->active) return redirect('/');


        $office = $this->officeRepo->findbyId($office_id);
        $medics = $this->medicRepo->findAllByOffice($office->id);

        if (request('medic'))
            $medic = $this->medicRepo->findById(request('medic'));
        else
            $medic = null;
        
       // if(!$medic->hasrole('medico')) return redirect('/');

        return view('clinics.reservation', compact('medics', 'medic', 'office'));
    }
}
