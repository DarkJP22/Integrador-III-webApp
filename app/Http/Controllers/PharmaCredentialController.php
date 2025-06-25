<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Pharmacredential;
use App\Pharmacy;
use Illuminate\Validation\Rule;

class PharmaCredentialController extends Controller
{
    /**
     * Mostrar lista de pacientes de un doctor
     */
    public function show(Pharmacy $pharmacy)
    {
       

        if (request()->wantsJson()) {
            return response($pharmacy->pharmacredential, 200);
        }

      
    }

  

    public function store(Pharmacy $pharmacy)
    {

        $this->validate(request(), [
            'name' => 'nullable',
            'api_url' => ['required','url', Rule::unique('pharmacredentials')->where(function ($query) use( $pharmacy) {
                return $query->where('pharmacy_id', $pharmacy->id);
            })],
            'access_token' => ['required'],
           
        ]);

     
        $data = request()->all();
        
        $credential = $pharmacy->pharmacredential()->create($data);

        if (request()->wantsJson()) {
            return response($credential, 201);
        }
       
        flash('Credencial Creado', 'success');

    }

   
    /**
     * Actualizar Paciente
     */
    public function update(Pharmacredential $credential)
    {
        $this->validate(request(), [
            'name' => 'nullable',
            'api_url' => ['required','url', Rule::unique('pharmacredentials')->where(function ($query) use($credential) {
                return $query->where('pharmacy_id', $credential->pharmacy_id);
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
    public function destroy(Pharmacredential $credential)
    {
        $result = $credential->delete();


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'Error al eliminar'], 422);
        }

        return Redirect('/pharmacredentials');
    }


}
