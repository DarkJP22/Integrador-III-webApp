<?php

namespace App\Http\Controllers\Pharmacy;

use App\Repositories\AppointmentRepository;
use App\Repositories\PatientRepository;
use App\Http\Controllers\Controller;
use App\Repositories\MedicRepository;
use App\User;
use App\Office;

class AgendaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        AppointmentRepository $appointmentRepo,
        PatientRepository $patientRepo,
        MedicRepository $medicRepo
    ) {
        $this->middleware('auth');
        $this->appointmentRepo = $appointmentRepo;
        $this->patientRepo = $patientRepo;
        $this->medicRepo = $medicRepo;

    }

    public function index(User $medic, Office $office)
    {
        if (!auth()->user()->hasRole('farmacia')) {//farmacia
            return redirect('/');
        }

        $medic->load('roles');

        if (!$medic->hasRole('medico')) {
            return redirect('/');
        }
        if (!$medic->verifyOffice($office->id)) {
            return redirect('/');
        }


        return view('pharmacy.agenda.index', compact('medic', 'office'));
    }


}
