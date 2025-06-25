<?php

namespace App\Http\Controllers;

use App\Actions\RegisterPatient;
use App\Actions\RegisterPatientFromAssistant;
use App\Actions\RegisterPatientFromClinic;
use App\Actions\RegisterPatientFromLab;
use App\Actions\RegisterPatientFromPharmacy;
use App\Actions\ShareLinkAppMobileAction;
use App\Actions\UpdatePatient;
use App\Actions\UpdatePatientByAdmin;
use App\Appointment;
use App\Enums\AppointmentStatus;
use App\Repositories\PatientRepository;
use App\Patient;
use App\Repositories\AppointmentRepository;
use Illuminate\Validation\Rule;
use App\Http\Requests\PatientUserRequest;
use App\User;
use App\Repositories\MedicRepository;
use App\PatientInvitation;
use App\EmergencyContact;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected PatientRepository $patientRepo,
        protected AppointmentRepository $appointmentRepo,
        protected MedicRepository $medicRepo
    ) {
        $this->middleware('auth');
    }

    /**
     * Mostrar lista de pacientes de un doctor
     */
    public function index()
    {

        $this->authorize('create', Patient::class);

        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $inita = null;

        if (request('inita')) {
            $inita = 1;
        }

        $patients = $this->patientRepo->findAll($search);

        if (request()->wantsJson()) {
            return response($patients, 200);
        }

        return view(auth()->user()->userRole().'.patients.index', compact('patients', 'search', 'inita'));
    }

    public function store(PatientUserRequest $request, ShareLinkAppMobileAction $actionSMS)
    {

        $v = Validator::make($request->all(), [
            'tipo_identificacion' => 'nullable',
            'ide' => 'nullable',

        ]);

        $v->sometimes('tipo_identificacion', 'required', function ($input) {
            return $input->ide != '';
        });

        $v->sometimes('ide', 'digits:9', function ($input) {
            return $input->tipo_identificacion == '01';
        });

        $v->sometimes('ide', 'digits:10', function ($input) {
            return $input->tipo_identificacion == '02' || $input->tipo_identificacion == '04';
        });

        $v->sometimes('ide', 'digits_between:11,12', function ($input) {
            return $input->tipo_identificacion == '03';
        });

        $v->validate();


        $data = $request->all();
        $data['authorization'] = 1;

        $patient = $this->patientRepo->store($data);

        // if($patient->email || $patient->ide){  //se desabilito 16/06/2021 por que el paciente es el se crea la cuenta

        //     $user_patient = $this->patientRepo->createUser($patient, $data);

        //     $actionSMS->execute($patient, $user_patient->phone_country_code.$user_patient->phone_number);

        // }

        if (request('account')) {

            $accountToAssign = User::where('email', request('account'))->first();

            $accountToAssign->patients()->attach($patient);

        }

        if (auth()->user()->isBeautician()) {


            $office = auth()->user()->offices->first();

            $office->patients()->save($patient, ['authorization' => $data['authorization']]);

            $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);

            foreach ($medics as $medicClinic) { //se agregan a todos los medicos de la clinica

                $medicClinic->patients()->save($patient, ['authorization' => $data['authorization']]);

            }


        }

        if (auth()->user()->isAssistant()) {

            $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->user()->id)->first();


            if (auth()->user()->isClinicAssistant($assistantUser->user_id)) {
                $adminClinica = User::find($assistantUser->user_id);

                $office = $adminClinica->offices->first();

                $office->patients()->save($patient, ['authorization' => $data['authorization']]);

                $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);

                foreach ($medics as $medicClinic) { //se agregan a todos los medicos de la clinica

                    $medicClinic->patients()->save($patient, ['authorization' => $data['authorization']]);

                }

            }
        }

        if (auth()->user()->isClinic()) {

            $office = auth()->user()->offices->first();

            $office->patients()->save($patient, ['authorization' => $data['authorization']]);

            $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);

            foreach ($medics as $medicClinic) { //se agregan a todos los medicos de la clinica

                $medicClinic->patients()->save($patient, ['authorization' => $data['authorization']]);

            }
        }

        if (auth()->user()->isPharmacy()) {

            $adminPharmacy = auth()->user();

            $pharmacy = $adminPharmacy->pharmacies->first();

            $pharmacy->patients()->save($patient, ['authorization' => $data['authorization']]);


        }

        if (request('invitation')) {

            $code = PatientInvitation::generateFor($patient);

            $status = $code->send('+506'.request('invitation'));

        }
        if (request('contact_name') && request('contact_phone_number')) {

            $contact = EmergencyContact::create([
                'name' => request('contact_name'),
                'patient_id' => $patient->id,
                'phone_country_code' => request('contact_phone_country_code'),
                'phone_number' => request('contact_phone_number'),

            ]);

        }

        if (request()->wantsJson()) {
            return response($patient, 201);
        }

        flash('Paciente Creado', 'success');

        if (auth()->user()->isPharmacy()) {
            return Redirect('/pharmacy/patients/'.$patient->id);
        }

        if (auth()->user()->isBeautician()) {
            return Redirect('/beautician/patients/'.$patient->id);
        }

        return Redirect('/general/patients/'.$patient->id);
    }

    public function create()
    {
        $this->authorize('create', Patient::class);

        return view(auth()->user()->userRole().'.patients.create');
    }

    public function edit($id)
    {

        $tab = request('tab');

        $patient = $this->patientRepo->findById($id);

        $this->authorize('update', $patient);

        //$search['status'] = 1;
        $search['order'] = 'start';
        $totalAppointments = $this->appointmentRepo->findAllByPatient($id, $search);

        $initAppointments = $totalAppointments->filter(function ($item, $key) {
            return $item->status == AppointmentStatus::STARTED;
        });

        $scheduledAppointments = $totalAppointments->filter(function ($item, $key) {
            return $item->status == AppointmentStatus::SCHEDULED;
        });

        $summaryAppointments = Appointment::with('user')->latest()->paginate(3);

        $appointments = [];//$patient->appointments->load('user', 'diagnostics'); // se comento por que es informacion repetida(diagnosticos) en las tres ultimas consultas


        $files = $patient->archivos;

        return view(auth()->user()->userRole().'.patients.edit',
            compact('patient', 'files', 'initAppointments', 'scheduledAppointments', 'tab', 'appointments',
                'summaryAppointments'));
    }

    /**
     * Actualizar Paciente
     */
    public function update(Patient $patient, UpdatePatientByAdmin $updatePatient)
    {

        $updatePatient($patient, [
            ...request()->all(),

        ]);

        flash('Paciente Actualizado', 'success');

        return back();

    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy($id)
    {
        $result = $this->patientRepo->delete($id);

        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'No se puede eliminar paciente por que tiene citas asignadas'], 422);

        }

        ($result === true) ? flash('Paciente eliminado correctamente!',
            'success') : flash('No se puede eliminar paciente por que tiene citas asignadas', 'error');

        return back();
    }

    public function createorupdatepatient(UpdatePatient $updatePatient)
    {

        $data = [
            'first_name' => request('name'),
            'email' => request('email'),
            'phone_country_code' => '+506',
            'phone_number' => request('phone'),
            'ide' => request('identificacion'),
            'tipo_identificacion' => request('tipo_identificacion'),
        ];


        $creators = [
            'medic' => RegisterPatient::class,
            'clinic' => RegisterPatientFromClinic::class,
            'lab' => RegisterPatientFromLab::class,
            'assistant' => RegisterPatientFromAssistant::class,
            'pharmacy' => RegisterPatientFromPharmacy::class,
        ];

        $patient = Patient::where('ide', request('identificacion'))->first();

        if ($patient) {
            $patient = $updatePatient($patient, $data);
        
        } else {
            $patient = app($creators[auth()->user()->userRole()])([
                ...$data,
                'complete_information' => 0
            ]);
        }

        if (request()->wantsJson()) {
            return response($patient, 200);
        }

    }


}
