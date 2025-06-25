<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Patient;
use App\PatientCode;
use App\Repositories\MedicRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\User;

class PatientPermissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected MedicRepository $medicRepo)
    {
        $this->middleware('auth');
    }

    /**
     * Guardar paciente
     */
    public function store(Patient $patient, User $medic = null)
    {

        $userToAssign = ($medic) ? $medic : auth()->user();

        if (!$userToAssign->patients()->where('patients.id', $patient->id)->exists()) {
            $userToAssign->patients()->attach($patient);
        }

        if (request()->wantsJson()) {
            return response([], 200);
        }


        flash('Paciente Agregado a tu lista', 'success');

        return Redirect()->back();
    }

    /**
     * Guardar paciente
     */
    public function authorization(Patient $patient, User $medic = null)
    {

        $this->validate(request(), [

            'code' => 'required'


        ]);

        $code = PatientCode::query()
            ->where('code', request('code'))
            ->where('was_used', false)
            ->where('created_at', '>', Carbon::now()->subHours(2))
            ->first();

        if (!$code) {

            if (request()->wantsJson()) {
                return response([
                    'message' => 'Código no existe o ha expirado',
                    'errors' => [
                        'code' => ['Código no existe o ha expirado']
                    ]
                ], 422);
            }

            throw ValidationException::withMessages([
                'code' => ['Código no existe o ha expirado'],
            ]);

            return back();

        }

        $patient = $code->patient;

        $userToAutho = ($medic) ? $medic : auth()->user();

        $userToAutho->patients()->updateExistingPivot($patient->id, ['authorization' => 1, 'authorized_at' => Carbon::now(), 'authorization_code' => $code->code]);

        $code->update(['was_used' => true]);
        //\DB::table('patient_codes')->where('patient_id', $patient->id)->delete(); no borrar para rastreo cuestiones legales


        if (request()->wantsJson()) {
            return response([], 200);
        }


        flash('Paciente Autorizado', 'success');

        return Redirect()->back();
    }

    /**
     * Guardar paciente
     */
    public function addauthorization(Patient $patient, User $medic = null)
    {

        $this->validate(request(), [

            'code' => 'required'


        ]);

        $code = PatientCode::where('code', request('code'))->where('created_at', '>', Carbon::now()->subHours(2))->first();

        if (!$code) {

            if (request()->wantsJson()) {
                return response(['message' => 'Codigo no existe o ha expirado'], 422);
            }

            throw ValidationException::withMessages([
                'code' => ['Codigo no existe o ha expirado'],
            ]);

            return back();

        }

        if (auth()->user()->isClinic() || auth()->user()->isPharmacy() || auth()->user()->isAssistant() || auth()->user()->isBeautician()) {


            if (auth()->user()->isClinic()) {

                $adminClinic = auth()->user();

                $office = $adminClinic->offices->first();

                if (!$office->patients()->where('patients.id', $patient->id)->exists()) {
                    $office->patients()->save($patient, ['authorization' => 1]);
                }

                $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);

                foreach ($medics as $medicClinic) { //se agregan a todos los medicos de la clinica

                    if (!$medicClinic->patients()->where('patients.id', $patient->id)->exists()) {
                        $medicClinic->patients()->save($patient, ['authorization' => 1]);
                    }
                }
            }

            if (auth()->user()->isAssistant() || auth()->user()->isBeautician()) {

                //$boss_assistant = \DB::table('assistants_users')->where('assistant_id', auth()->id())->first();

                //$boss = User::find($boss_assistant->user_id);

                if (auth()->user()->isAssistant()) {
                    $office = auth()->user()->clinicsAssistants->first();
                } else {
                    $office = auth()->user()->offices->first();
                }

                if (!$office->patients()->where('patients.id', $patient->id)->exists()) {
                    $office->patients()->save($patient, ['authorization' => 1]);
                }

                $medics = $this->medicRepo->findAllByOfficeWithoutPaginate($office->id);

                foreach ($medics as $medicClinic) { //se agregan a todos los medicos de la clinica

                    if (!$medicClinic->patients()->where('patients.id', $patient->id)->exists()) {
                        $medicClinic->patients()->save($patient, ['authorization' => 1]);
                    }
                }


            }


            if (auth()->user()->isPharmacy()) {

                $adminPharmacy = auth()->user();

                $pharmacy = $adminPharmacy->pharmacies->first();

                if (!$pharmacy->patients()->where('patients.id', $patient->id)->exists()) {

                    $pharmacy->patients()->save($patient, ['authorization' => 1]);

                    $pharmacredential = $pharmacy->pharmacredential;

                    if ($pharmacredential && !$patient->apipharmacredentials()->where('pharmacy_id', $pharmacy->id)->exists()) {

                        $patient->apipharmacredentials()->create([
                            "name" => $pharmacredential->name,
                            "api_url" => $pharmacredential->api_url,
                            "access_token" => $pharmacredential->access_token,
                            "pharmacy_id" => $pharmacredential->pharmacy_id
                        ]);

                    }
                }

            }


        } else { // si es medico

            $userToAssign = ($medic) ? $medic : auth()->user();

            if (!$userToAssign->patients()->where('patients.id', $patient->id)->exists()) {
                $userToAssign->patients()->attach($patient);
            }

            $patient = $code->patient;

            $userToAutho = ($medic) ? $medic : auth()->user();

            $userToAutho->patients()->updateExistingPivot($patient->id, ['authorization' => 1]);

        }

        \DB::table('patient_codes')->where('patient_id', $patient->id)->delete();


        if (request()->wantsJson()) {
            return response([], 200);
        }


        flash('Paciente Autorizado', 'success');

        return Redirect()->back();
    }
}
