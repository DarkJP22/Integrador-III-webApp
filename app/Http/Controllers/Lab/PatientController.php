<?php

namespace App\Http\Controllers\Lab;

use App\Actions\RegisterPatientFromLab;
use App\Http\Controllers\Controller;
use App\Patient;

class PatientController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');

    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('laboratorio')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['province'] = request('province');


        $patients = Patient::query()
            ->Search($search)
            ->latest()
            ->paginate();

        $office = auth()->user()->offices->first();

        return view('lab.patients.index', compact('patients', 'search', 'office'));

    }

    public function store(RegisterPatientFromLab $registerPatientFromLab)
    {

        $patient = $registerPatientFromLab([
            ...request()->all(),
            'complete_information' => 0
        ]);

        if (request()->wantsJson()) {
            return response($patient, 201);
        }

        return redirect('/lab/patients');
    }


}
