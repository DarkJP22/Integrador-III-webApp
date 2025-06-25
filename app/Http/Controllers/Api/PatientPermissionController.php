<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use Illuminate\Http\Request;
use App\Patient;
use App\PatientCode;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class PatientPermissionController extends Controller
{


    /**
     * Guardar paciente
     */
    public function authorization(Patient $patient)
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

            return;
        }

        $patient = $code->patient;

        if (!auth()->user()->patients()->where('patients.id', $patient->id)->exists()) {
            auth()->user()->patients()->attach($patient, ['authorization' => 1, 'authorized_at' => now(), 'authorization_code' => $code->code]);
        }else{
            auth()->user()->patients()->updateExistingPivot($patient->id, ['authorization' => 1, 'authorized_at' => now(), 'authorization_code' => $code->code]);
        }

        $code->update(['was_used' => true]);

        //auth()->user()->patients()->updateExistingPivot($patient->id, ['authorization' => 1]);


        if (request()->wantsJson()) {
            return PatientResource::make($patient);//response($patient, 200);
        }
    }
}
