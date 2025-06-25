<?php

namespace App\Http\Controllers\Medic;

use App\Actions\RegisterPatient;
use App\Http\Controllers\Controller;


class PatientController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');

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

        return redirect('/medic/patients');
    }


}
