<?php

namespace App\Http\Controllers\Lab;

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Actions\RegisterPatient;
use App\Enums\LabAppointmentRequestStatus;
use App\Enums\OfficeType;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppPhoneMessageJob;
use App\LabAppointmentRequest;
use App\Office;
use App\Patient;
use App\Pharmacy;
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use App\Role;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentRequestsController extends Controller
{
    public function __construct(protected UserRepository $userRepo, protected PatientRepository $patientRepo)
    {
        $this->middleware('auth')->except('register', 'store');
        $this->userRepo = $userRepo;
        $this->patientRepo = $patientRepo;
    }

    public function index()
    {
        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['status'] = request('status');

        $appointmentRequests = LabAppointmentRequest::with('patient', 'office', 'user')
            ->search($search)
            ->when(filled($search['status']), function ($query) use ($search) {
                $query->where('status', $search['status']);
            })
            ->where('office_id', auth()->user()->offices->first()?->id)
            ->orderBy('created_at', 'DESC')
            ->paginate();

        return view('lab.appointmentRequests.index', [
            'appointmentRequests' => $appointmentRequests,
            'appointmentRequestStatuses' => LabAppointmentRequestStatus::options(),
            'search' => $search
        ]);
    }

    public function register()
    {
        return view('lab.appointmentRequests.register');
    }

    public function create()
    {
        return view('lab.appointmentRequests.create');
    }

    public function store()
    {
        $data = request()->all();
        $validatedData = $this->validate(request(), [
            'province' => ['required'],
            'canton' => ['required'],
            'district' => ['required'],
            'visit_location' => ['required'],
            'exams' => ['required'],

        ]);
        $data = array_merge($data, $validatedData);

        $patient = \DB::transaction(function () use ($data) {


            if (!$patient = Patient::where('ide', $data['ide'])->first()) {
                $patient = app(CreatePatient::class)([
                    ...$data,
                    'phone_country_code' => '+506',
                    'city' => $data['district'],
                ]);

            }

            $office = auth()->user()->offices->first();
            LabAppointmentRequest::create([
                'patient_id' => $patient->id,
                'office_id' => $office?->id, // solo para laboratorio de julio quesada
                'coords' => $data['coords'] ?? null,
                'patient_ide' => $patient->ide,
                'patient_name' => $patient->first_name,
                'phone_number' => $patient->phone_number,
                'province' => $patient->province ?? $data['province'],
                'canton' => $data['canton'],
                'district' => $data['district'],
                'visit_location' => $data['visit_location'],
                'exams' => $data['exams'],

            ]);

            if ($patient->phone_number) {

                SendAppPhoneMessageJob::dispatch("Estimado usuario de {$office?->name}. Le recordamos que para acceder a los beneficios del 50% descuento el usuario tiene que estar registrado en la aplicación móvil de 'Doctor Blue'. Si desea hacer el registro puede descargar la app en la PlayStore o App Store, o si lo prefiere, puede acceder en los siguientes enlaces Android: ". getUrlAppPacientesAndroid(). ' iOS: '. getUrlAppPacientesIos(), $patient->fullPhone)->afterCommit();
            }

            return $patient;
        });

        return $patient;
    }

    public function registerStore()
    {
        $data = $this->validate(request(), [
            'ide' => ['required', 'digits_between:9,12'],
            'first_name' => ['required', 'max:255'],
            //'phone_country_code' => ['required'],
            'phone_number' => ['required', 'digits_between:8,15'], //|unique:users',
            'birth_date' => ['required', 'date'],
            'coords' => ['nullable'],
            'gender' => ['required'],
            'province' => ['required'],
            'canton' => ['required'],
            'district' => ['required'],
            'visit_location' => ['required'],
            'responsable_ide' => [
                Rule::when(request('is_patient') === 'no', 'required'), 'nullable', 'digits_between:9,12'
            ],
            'responsable_name' => [Rule::when(request('is_patient') === 'no', 'required'), 'nullable'],
            'responsable_birth_date' => [Rule::when(request('is_patient') === 'no', 'required'), 'nullable', 'date'],
            'responsable_gender' => [Rule::when(request('is_patient') === 'no', 'required'), 'nullable'],
            'pharmacy_code' => ['nullable'],

        ]);

        $patient = \DB::transaction(function () use ($data) {
            $user = null;

            if (request('is_patient') === 'yes') {


                    if (!$patient = Patient::where('ide', $data['ide'])->first()) {
                        $patient = app(CreatePatient::class)([
                            ...$data,
                            'phone_country_code' => '+506',
                            'city' => $data['district'],
                        ]);
                    }

            } else {

                if ($user = User::where('ide', $data['responsable_ide'])->first()) {

                    $patient = $this->assignOrRegisterPatient($data, $user);
                }else{
                    if (!$patient = Patient::where('ide', $data['ide'])->first()) {
                        $patient = app(CreatePatient::class)([
                            ...$data,
                            'phone_country_code' => '+506',
                            'city' => $data['district'],
                        ]);
                    }
                }
            }

            $referenceIde = null;
            $referenceName = null;

            if (isset($data['pharmacy_code'])) {
                $referenceIde = Pharmacy::find($data['pharmacy_code'])?->ide;
                $referenceName = Pharmacy::find($data['pharmacy_code'])?->name;
            }
            $office = Office::where('type', OfficeType::LABORATORY)->where('id', 8)->first();
            LabAppointmentRequest::create([
                'user_id' => $user?->id,
                'patient_id' => $patient->id,
                'office_id' => $office?->id, // solo para laboratorio de julio quesada
                'coords' => $data['coords'],
                'patient_ide' => $patient->ide,
                'patient_name' => $patient->first_name,
                'phone_number' => $patient->phone_number,
                'province' => $patient->province ?? $data['province'],
                'canton' => $data['canton'],
                'district' => $data['district'],
                'visit_location' => $data['visit_location'],
                'reference_ide' => $referenceIde,
                'reference_name' => $referenceName,

            ]);

            if ($patient->phone_number) {

                SendAppPhoneMessageJob::dispatch("Estimado usuario de {$office?->name}. Le recordamos que para acceder a los beneficios del 50% descuento el usuario tiene que estar registrado en la aplicación móvil de 'Doctor Blue'. Si desea hacer el registro puede descargar la app en la PlayStore o App Store, o si lo prefiere, puede acceder en los siguientes enlaces Android: ". getUrlAppPacientesAndroid(). ' iOS: '. getUrlAppPacientesIos(), $patient->fullPhone)->afterCommit();
            }

            return $patient;
        });

        return $patient;
    }

    protected function assignOrRegisterPatient($data, $user)
    {
        if ($patient = Patient::where('ide', $data['ide'])->first()) {
            if (!$user->patients()->where('patients.id', $patient->id)->exists()) {
                $user->patients()->save($patient, ['authorization' => 1]);
            }
        } else {
            $patient = app(CreatePatient::class)([
                ...$data,
                'phone_country_code' => '+506',
                'city' => $data['district'],
            ]);

            $user->patients()->save($patient, ['authorization' => 1]);
        }

        return $patient;
    }

    public function status(LabAppointmentRequest $appointmentRequest)
    {

        $appointmentRequest->status = !$appointmentRequest->status->value;

        $appointmentRequest->save();

        return back();
    }

    public function updateAppointmentDate(LabAppointmentRequest $appointmentRequest)
    {
        $data = $this->validate(request(), [
            'appointment_date' => ['required', 'date']
        ]);

        return \DB::transaction(function () use ($appointmentRequest, $data) {
            $appointmentRequest->fill([
                ...$data,
                'status' => LabAppointmentRequestStatus::COMPLETED
            ]);

            $appointmentRequest->save();

            return $appointmentRequest;
        });
    }

    public function updateExams(LabAppointmentRequest $appointmentRequest)
    {
        $data = $this->validate(request(), [
            'exams' => ['nullable']
        ]);

        return \DB::transaction(function () use ($appointmentRequest, $data) {
            $appointmentRequest->fill([
                ...$data,
            ]);

            $appointmentRequest->save();

            return $appointmentRequest;
        });
    }

    public function updateVisitLocation(LabAppointmentRequest $appointmentRequest)
    {
        $data = $this->validate(request(), [
            'visit_location' => ['nullable']
        ]);

        return \DB::transaction(function () use ($appointmentRequest, $data) {
            $appointmentRequest->fill([
                ...$data,
            ]);

            $appointmentRequest->save();
            return $appointmentRequest;
        });
    }
}
