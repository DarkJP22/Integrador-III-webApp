<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Allergy;
use App\Patient;

class AllergyController extends Controller
{
    public function store(Patient $patient)
    {
        request()->validate([
            'name' => 'required'

        ]);

        if($patient){

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


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);

        }

        return Redirect('/allergys');
    }
}
