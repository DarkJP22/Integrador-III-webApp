<?php

namespace App\Http\Controllers\Operator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Repositories\AppointmentRepository;
use App\Patient;
use App\Repositories\PatientRepository;
use App\Appointment;
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
    public function __construct(AppointmentRepository $appointmentRepo, PatientRepository $patientRepo, MedicRepository $medicRepo)
    {
        $this->middleware('auth');
        $this->appointmentRepo = $appointmentRepo;
        $this->patientRepo = $patientRepo;
        $this->medicRepo = $medicRepo;

    }

    public function index(User $medic, Office $office)
    {
        if (!auth()->user()->hasRole('operador')) {
            return redirect('/');
        }

        if (!$medic->hasrole('medico')) return redirect('/');
        if (!$medic->verifyOffice($office->id)) return redirect('/');


        return view('operator.agenda.index', compact('medic', 'office'));
    }

   
}
