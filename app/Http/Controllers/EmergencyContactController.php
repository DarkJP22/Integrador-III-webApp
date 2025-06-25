<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Patient;
use App\EmergencyContact;

class EmergencyContactController extends Controller
{
    /**
     * Mostrar lista de pacientes de un doctor
     */
    public function index(Patient $patient)
    {
       

        if (request()->wantsJson()) {
            return response($patient->emergencyContacts()->paginate(10), 200);
        }

      
    }

  

    public function store(Patient $patient)
    {

        $this->validate(request(), [
            'name' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'],
           
        ]);

        $emergencyContact = $patient->emergencyContacts()->create(request()->all());

        if (request()->wantsJson()) {
            return response($emergencyContact, 201);
        }
       
        flash('Contacto Creado', 'success');

    }

   
    /**
     * Actualizar Paciente
     */
    public function update(EmergencyContact $emergencyContact)
    {
        $this->validate(request(), [
            'name' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'],
           
        ]);

        $emergencyContact->fill( request()->all() );
        $emergencyContact->save();

        if (request()->wantsJson()) {
            return response($emergencyContact, 200);
        }

        flash('Contacto Actualizado', 'success');

        return back();
    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy(EmergencyContact $emergencyContact)
    {
        $result = $emergencyContact->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);
        }

        return Redirect('/emergencyContact');
    }


}
