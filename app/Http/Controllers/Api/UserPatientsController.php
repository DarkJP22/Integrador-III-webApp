<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateOrAssignAccount;
use App\Actions\CreateUserDiscount;
use App\Actions\RegisterPatient;
use App\Actions\ShareLinkAppMobileAction;
use App\Actions\UpdatePatient;
use App\Discount;
use App\Http\Requests\PatientRequest;
use App\Repositories\PatientRepository;
use Illuminate\Validation\Rule;
use App\Patient;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppPhoneMessageJob;
use App\PatientCode;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class UserPatientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected PatientRepository $patientRepo, protected UserRepository $userRepo)
    {
        $this->middleware('auth');
        // $this->patientRepo = $patientRepo;
        // $this->userRepo = $userRepo;
    }

    public function index()
    {
        $search['q'] = request('q');

        if (request('q')) {
            $patients = auth()->user()->patients()->search(request(['q']));
        } else {
            $patients = auth()->user()->patients();
        }


        return $patients->latest()->paginate(10);
    }

    public function show(Patient $patient)
    {
        $patient = auth()->user()->patients()->findOrFail($patient->id);

        return $patient;
    }

    public function getPatientByIde(string $ide)
    {

        $patient = Patient::where('ide', $ide)->first();

        return [
            'patient' => $patient,
            'belongsToMe' => auth()->user()->patients->contains($patient)
        ];
    }

    public function requestAuthorization(Patient $patient)
    {

        if ($patient->phone_number) {
            $data = PatientCode::generateFor($patient);
            $message = "Un usuario ha solicitado un código de confirmación para agregarte como paciente. Comparte este código ".$data->code." para autorizarlo";

            SendAppPhoneMessageJob::dispatch($message, $patient?->fullPhone)->afterCommit();
        }
    }

    /**
     * Guardar paciente
     */
    public function store(Request $request, RegisterPatient $registerPatient)
    {
        $patient = $registerPatient($request->all(), [
            'tipo_identificacion' => ['required'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required'],
            'province' => ['required'],
            'canton' => ['required'],
            'district' => ['required'],
        ]);

        return $patient->refresh();
    }

    /**
     * Actualizar Paciente
     */
    public function update(Request $request, Patient $patient, UpdatePatient $updatePatient)
    {

        return $updatePatient($patient, [
            ...$request->all(),
            'complete_information' => 1
        ]);

    }

    public function destroy(Patient $patient)
    {
        $patient = $this->patientRepo->delete($patient->id);

        if ($patient === true) {

            return response([], 204);
        }

        return response(['message' => 'No se puede eliminar paciente por que tiene citas asignadas'], 403);
    }

    public function createOrAssignAccount(ShareLinkAppMobileAction $actionSMS, CreateOrAssignAccount $createOrAssignAccount)
    {
        \Log::info('cuenta desde posfarmacia'.json_encode(request()->all()));

        $user = $createOrAssignAccount(request()->all());

        if ($user?->phone_number) {
            $message = request('message') ?? "Enfermarse puede suceder en cualquier momento... En Doctor Blue encuentras el médico, odontólogo o especialista que necesites, cerca de tu casa. Descárgala Gratis en Playstore o AppleStore. Android: ".getUrlAppPacientesAndroid().' IOS: '.getUrlAppPacientesIos();
            SendAppPhoneMessageJob::dispatch($message, $user->fullPhone)->afterCommit();
        }

        return $user;
    }


    public function getAccount($patientIde)
    {
        \Log::info('obtener la cuenta del paciente desde posfarmacia'.$patientIde);

        if ($user = User::where('ide', $patientIde)->first()) {
            $discounts = Discount::where('for_gps_users', 1)
                ->where('to', 'farmacia')->get();

            $data = [
                "user" => $user,
                "discount" => $discounts->first()
            ];

            return response($data, 200);
        }

        return response('Not Found', 404);
    }

    public function createDiscount(Request $request, $patientIde, CreateUserDiscount $action)
    {
        \Log::info('se crea el descuento de paciente con cuenta GPS desde posfarmacia - '.$patientIde);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_discount' => 'required|numeric',
            'total' => 'required|numeric',
            'CodigoMoneda' => 'required'
        ]);

        if ($validator->fails()) {
            return response('Validation Error', 422);
        }

        $data = $request->all();

        $user = User::where('ide', $patientIde)->first();

        if (!$user) {
            return response('Usuario No Existe en Doctor Blue', 422);
        }

        return response($action->create($user, $data), 201);
    }
}
