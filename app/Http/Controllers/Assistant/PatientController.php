<?php

namespace App\Http\Controllers\Assistant;

use App\Actions\RegisterPatientFromAssistant;
use App\Actions\ShareLinkAppMobileAction;
use App\Http\Controllers\Controller;
use App\Repositories\PatientRepository;
use App\Patient;
use App\Repositories\AppointmentRepository;
use App\User;
use App\Http\Requests\PatientUserRequest;
use Illuminate\Validation\ValidationException;
use App\Repositories\MedicRepository;
use App\PatientInvitation;
use App\EmergencyContact;
use Illuminate\Support\Facades\Validator;


class PatientController extends Controller
{
    public function __construct(
        PatientRepository $patientRepo,
        AppointmentRepository $appointmentRepo,
        MedicRepository $medicRepo
    ) {

        $this->patientRepo = $patientRepo;
        $this->appointmentRepo = $appointmentRepo;
        $this->medicRepo = $medicRepo;

        $this->middleware('auth');


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
        $search['province'] = request('province');


        $office = auth()->user()->clinicsAssistants->first();


        $patients = $this->patientRepo->findAllOfClinic($office, $search);


        return view('assistant.patients.index', compact('patients', 'office', 'search'));

    }

    /**
     * Guardar paciente
     */
    public function show(Patient $patient)
    {

        $search['status'] = 1;
        $search['order'] = 'start';
        $initAppointments = $this->appointmentRepo->findAllByPatient($patient->id, $search);

        $search['status'] = 0;
        $scheduledAppointments = $this->appointmentRepo->findAllByPatient($patient->id, $search);

        $summaryAppointments = $initAppointments->take(3);


        $files = $patient->archivos;

        return view('assistant.patients.edit',
            compact('patient', 'files', 'initAppointments', 'scheduledAppointments'));


    }

    public function store(RegisterPatientFromAssistant $registerPatientFromAssistant)
    {
        $patient = $registerPatientFromAssistant([
            ...request()->all(),
            'complete_information' => 0
        ]);

        if (request()->wantsJson()) {
            return response($patient, 201);
        }

        return redirect('/assistant/patients');
    }


}
