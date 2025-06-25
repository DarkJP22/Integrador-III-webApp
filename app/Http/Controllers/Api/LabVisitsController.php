<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppPhoneMessageJob;
use App\LabAppointmentRequest;
use App\LabVisit;
use App\Notifications\NewAppointmentVisit;
use App\Office;
use App\Patient;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class LabVisitsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(Request $request)
    {

        $visits = LabVisit::query()
            ->search($request->input('q'))
            ->where('office_id', $request->input('office_id')) // gps laboratorio
            ->when($request->input('location'), function ($query, $location) {
                $query->where('location', $location);
            })
            ->get();

        return $visits;
    }

    public function registerVisit()
    {
        $data = $this->validate(request(), [
            'tipo_identificacion' => ['required'],
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
            'visit_location' => ['nullable'],
            'office_id' => ['required', 'exists:offices,id'],


        ]);

        $labAppointmentRequest = \DB::transaction(function () use ($data) {
            $user = auth()->user();

            if ($user->ide === $data['ide']) { // el paciente es el mismo usuario quien realiza la soliciutd


                if ($user && $user->ide) {

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
                }
            } else { //si el paciente es distinto al usuario quien solicita

                $patient = $this->assignOrRegisterPatient($data, $user);
            }


            return LabAppointmentRequest::create([
                'user_id' => $user->id,
                'patient_id' => $patient->id,
                'office_id' => $data['office_id'],
                'coords' => $data['coords'],
                'patient_ide' => $patient->ide,
                'patient_name' => $patient->first_name,
                'phone_number' => $patient->phone_number,
                'province' => $patient->province ?? $data['province'],
                'canton' => $data['canton'],
                'district' => $data['district'],
                'visit_location' => $data['visit_location'],


            ]);
        });

        try {
            $officeLab = Office::findOrFail($labAppointmentRequest->office_id);

            if ($officeLab) {

                $officeLab->administrators()->each->notify(new NewAppointmentVisit($labAppointmentRequest,
                    '/lab/appointment-requests'));
            }
        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
        {
            \Log::error($e->getMessage());
        }

        if ($officeLab?->phone) {
            $message = "Nueva visita solicitada. El paciente ".$labAppointmentRequest->patient_name." ha solicitado una visita";
            SendAppPhoneMessageJob::dispatch($message, $officeLab->fullPhone)->afterCommit();
        }

        return $labAppointmentRequest;
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
// Se deshabilitó ya que son los pacientes quienes se registran
//            if (!$userOfPatient = User::where('ide', $data['ide'])->first()) {
//                $userOfPatient = app(CreateUser::class)([
//                    ...$data,
//                    'name' => $data['first_name'],
//                    'password' => $data['phone_number'],
//                    'phone_country_code' => '+506',
//                ], Role::whereName('paciente')->first());
//            }
//
//            $userOfPatient->patients()->save($patient, ['authorization' => 1]);
            $user->patients()->save($patient, ['authorization' => 1]);
        }

        return $patient;
    }
}
