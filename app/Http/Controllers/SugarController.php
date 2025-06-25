<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sugar;
use App\Patient;


class SugarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function store(Patient $patient)
    {
        request()->validate([
            'glicemia' => 'required|numeric',
            'date_control' => 'required',
            'time_control' => 'required'
        ]);


        $sugar = $patient->sugars()->create(request()->all());

        return $sugar;
    }

    public function destroy(Sugar $sugar)
    {
        $result = $sugar->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);

        }

        return Redirect('/sugars');
    }

}
