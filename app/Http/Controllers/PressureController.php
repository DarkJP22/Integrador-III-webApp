<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pressure;
use App\Patient;

class PressureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function store(Patient $patient)
    {
        request()->validate([
            'ps' => 'required|numeric',
            'pd' => 'required|numeric',
            'heart_rate' => 'nullable',
            'date_control' => 'required',
            'time_control' => 'required',
            'measurement_place' => 'required',
            'observations' => 'nullable'
        ]);


        $pressure = $patient->pressures()->create(request()->all());

        return $pressure;
    }

    public function destroy(Pressure $pressure)
    {
        $result = $pressure->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);

        }

        return Redirect('/pressures');
    }

}
