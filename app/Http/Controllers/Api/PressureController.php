<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Pressure;
use App\Patient;
use App\Http\Controllers\Controller;

class PressureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Patient $patient)
    {

        $pressures = $patient->pressures()->orderBy('date_control', 'DESC')->limit(20)->get();

        return $pressures;
    }

    public function store(Patient $patient)
    {
        $data = request()->validate([
            'ps' => 'required|numeric',
            'pd' => 'required|numeric',
            'date_control' => 'required',
            'time_control' => 'required',
            'heart_rate' => 'nullable',
            'measurement_place' => 'nullable',
            'observations' => 'nullable'
        ]);


        $pressure = $patient->pressures()->create($data);

        return $pressure;
    }

    public function destroy(Pressure $pressure)
    {
        $result = $pressure->delete();

        if ($result === true) {

            return response([], 204);
        }

        return response(['message' => 'Error al eliminar'], 422);
    }
}
