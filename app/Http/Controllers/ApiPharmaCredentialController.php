<?php

namespace App\Http\Controllers;

use App\Apipharmacredential;
use Illuminate\Http\Request;
use App\Patient;
use Illuminate\Validation\Rule;

class ApiPharmaCredentialController extends Controller
{
    /**
     * Mostrar lista de pacientes de un doctor
     */
    public function index(Patient $patient)
    {
       

        if (request()->wantsJson()) {
            return response($patient->apipharmacredentials()->paginate(10), 200);
        }

      
    }

  

    public function store(Patient $patient)
    {

        $this->validate(request(), [
            'name' => 'nullable',
            'api_url' => ['required','url', Rule::unique('apipharmacredentials')->where(function ($query) use( $patient) {
                return $query->where('patient_id', $patient->id);
            })],
            'access_token' => ['required'],
           
        ]);

        $pharmacy = auth()->user()->pharmacies->first();
        $data = request()->all();
        $data['pharmacy_id'] = $pharmacy->id;
        
        $credential = $patient->apipharmacredentials()->create($data);

        if (request()->wantsJson()) {
            return response($credential, 201);
        }
       
        flash('Credencial Creado', 'success');

    }

   
    /**
     * Actualizar Paciente
     */
    public function update(Apipharmacredential $credential)
    {
        $this->validate(request(), [
            'name' => 'nullable',
            'api_url' => ['required','url', Rule::unique('apipharmacredentials')->where(function ($query) use($credential) {
                return $query->where('patient_id', $credential->patient_id);
            })->ignore($credential->id)],
            'access_token' => ['required'],
           
        ]);

        $credential->fill( request()->all() );
        $credential->save();

        if (request()->wantsJson()) {
            return response($credential, 200);
        }

        flash('Credencial Actualizado', 'success');

        return back();
    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy(Apipharmacredential $credential)
    {
        $result = $credential->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);
        }

        return Redirect('/apipharmacredentials');
    }


}
