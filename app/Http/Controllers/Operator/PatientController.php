<?php

namespace App\Http\Controllers\Operator;

use App\Actions\RegisterPatient;
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
        if (!auth()->user()->hasRole('operador')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['province'] = request('province');


        $patients = $this->patientRepo->findAll($search);


        return view('operator.patients.index', compact('patients', 'search'));

    }

    public function store(RegisterPatient $registerPatient)
    {

        $patient = $registerPatient([
            ...request()->all(),
            'complete_information' => 0
        ]);

        if (request()->wantsJson()) {
            return response($patient, 201);
        }

        return redirect('/operator/patients');
    }


}
