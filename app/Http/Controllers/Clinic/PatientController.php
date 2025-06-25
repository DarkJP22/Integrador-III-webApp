<?php

namespace App\Http\Controllers\Clinic;

use App\Actions\RegisterPatientFromClinic;
use App\Http\Controllers\Controller;
use App\Repositories\PatientRepository;
use App\Repositories\AppointmentRepository;


class PatientController extends Controller
{
    public function __construct(PatientRepository $patientRepo, AppointmentRepository $appointmentRepo)
    {

        $this->patientRepo = $patientRepo;
        $this->appointmentRepo = $appointmentRepo;

        $this->middleware('auth');


    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['province'] = request('province');


        $office = auth()->user()->offices->first();

        $patients = $this->patientRepo->findAllOfClinic($office, $search);


        return view('clinic.patients.index', compact('patients', 'office', 'search'));

    }

    public function store(RegisterPatientFromClinic $registerPatientFromClinic)
    {

        $patient = $registerPatientFromClinic([
            ...request()->all(),
            'complete_information' => 0
        ]);

        if (request()->wantsJson()) {
            return response($patient, 201);
        }

        return redirect('/clinic/patients');
    }


}
