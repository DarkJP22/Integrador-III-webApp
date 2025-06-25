<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Allergy;
use App\Patient;
use App\Http\Controllers\Controller;

class AllergyController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Patient $patient)
    {

        $allergies = $patient->history->allergies->load('user.roles');

        return $allergies;
    }

    public function store(Patient $patient)
    {
        request()->validate([
            'name' => 'required'

        ]);

        if ($patient) {

            $history = $patient->history;

            return Allergy::create([
                'name' => request('name'),
                'history_id' => $history->id,
                'user_id' => auth()->id(),
            ])->load('user.roles');
        }


        return response(['message' => 'No se encontro paciente para guardar la alergia'], 404);
    }

    public function destroy(Allergy $allergy)
    {
        $result = $allergy->delete();

        if ($result === true) {

            return response([], 204);
        }

        return response(['message' => 'Error al eliminar'], 422);
    }
}
