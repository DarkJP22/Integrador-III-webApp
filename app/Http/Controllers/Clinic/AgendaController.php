<?php

namespace App\Http\Controllers\Clinic;

use Carbon\Carbon;
use App\Repositories\AppointmentRepository;
use App\Repositories\PatientRepository;
use App\Http\Controllers\Controller;
use App\Optreatment;
use App\Repositories\MedicRepository;

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

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $currentDate = request('date') ? request('date') : Carbon::now()->toDateString();
        $search['date'] = $currentDate;
        $search['hasSchedules'] = 1;
        $office = auth()->user()->offices->first();
        $search['q'] = $q = request('q');


        $medicsWithSchedules = $office->users()->whereHas('roles', function ($q) {
            $q->where('name', 'medico');
        });

        $medicsWithSchedules = $medicsWithSchedules->whereHas('schedules', function ($query) use ($search) {
            $query->whereDate('date', $search['date']);
        })->get();

        $medics = $this->medicRepo->findAllByOffice($office->id, $search);



        if (request('medic'))
            $medic = $this->medicRepo->findById(request('medic'));
        else
            $medic = null;

        $optreatments = Optreatment::all();

        return view('clinic.agenda.index', compact('medics', 'medicsWithSchedules', 'medic', 'office', 'currentDate', 'q', 'optreatments'));
    }
}
