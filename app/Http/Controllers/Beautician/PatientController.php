<?php

namespace App\Http\Controllers\Beautician;

use App\Actions\RegisterPatientFromBeautician;
use App\Actions\ShareLinkAppMobileAction;
use App\Appointment;
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
use Illuminate\Database\Eloquent\Builder;


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
        $search['q'] = request('q');
        $inita = null;

        if (request('inita')) {
            $inita = 1;
        }

        // $patients = $this->patientRepo->findAll($search);
        $office = auth()->user()->offices->first();


        $patients = $this->patientRepo->findAllOfClinic($office, $search);

        if (request()->wantsJson()) {
            return response($patients, 200);
        }

        return view('beautician..patients.index', compact('patients', 'search', 'inita'));

    }

    public function store(RegisterPatientFromBeautician $registerPatientFromBeautician)
    {

        $patient = $registerPatientFromBeautician([
            ...request()->all(),
            'complete_information' => 0
        ]);

        if (request()->wantsJson()) {
            return response($patient, 201);
        }

        return redirect('/beautician/patients');
    }

    public function edit($id)
    {
        $tab = request('tab');

        $patient = $this->patientRepo->findById($id);

        $this->authorize('update', $patient);

        //$search['status'] = 1;
        $search['order'] = 'start';
        $totalAppointments = Appointment::where('is_esthetic', 1)->orderBy('date',
            'DESC')->paginate(10);//$this->appointmentRepo->findAllByPatient($id, $search);

        $appointments = $totalAppointments->filter(function ($item, $key) {
            return $item->status == 1;
        });


        return view('beautician.patients.edit', compact('patient', 'tab', 'appointments'));
    }


}
