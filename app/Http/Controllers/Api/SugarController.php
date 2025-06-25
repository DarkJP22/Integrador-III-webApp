<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Sugar;
use App\Patient;
use App\Http\Controllers\Controller;

class SugarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Patient $patient)
    {

        $sugars = $patient->sugars()->orderBy('date_control', 'DESC')->limit(20)->get();

        return $sugars;
    }

    public function store(Patient $patient)
    {
        request()->validate([
            'glicemia' => 'required|numeric',
            'date_control' => 'required',
            'time_control' => 'required',
            'condition' => 'nullable'
        ]);


        $sugar = $patient->sugars()->create(request()->all());

        return $sugar;
    }

    public function destroy(Sugar $sugar)
    {
        $result = $sugar->delete();

        if ($result === true) {

            return response([], 204);
        }

        return response(['message' => 'Error al eliminar'], 422);
    }
}
