<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ConfigFactura;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class ConfigFacturaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
      
    }


    /**
     * Actualizar Paciente
     */
    public function update(ConfigFactura $config)
    {
        
        $v = Validator::make(request()->all(), [
            'CodigoActividad' => 'required',
            'nombre' => 'required',
            'tipo_identificacion' => 'required',
            'identificacion' => 'required|numeric',
            'sucursal' => 'required|numeric',
            'pos' => 'required|numeric',
            'provincia' => 'required',
            'canton' => 'required',
            'distrito' => 'required',
            'otras_senas' => 'required',
            'email' => 'required|email',
            'atv_user' => 'required',
            'atv_password' => 'required',
            'pin_certificado' => 'required',
        ]);

        $v->sometimes('identificacion', 'digits:9', function ($input) {
            return $input->tipo_identificacion == '01';
        });

        $v->sometimes('identificacion', 'digits:10', function ($input) {
            return $input->tipo_identificacion == '02' || $input->tipo_identificacion == '04';
        });

        $v->sometimes('identificacion', 'digits_between:11,12', function ($input) {
            return $input->tipo_identificacion == '03';
        });

        $v->validate();
        
        $data = request()->all();
       
        $config->fill($data);
        $config->access_token = NULL;
        $config->refresh_token = NULL;
        $config->token_expires_at = NULL;
        $config->refresh_expires_at = NULL;
        $config->save();

        if (request('CodigoActividad')) {
           // return request('CodigoActividad');
            $config->activities()->delete();
            $config->saveActivities(request('CodigoActividad'));
        }

        $mimes = ['p12'];
        $fileUploaded = 'error';

        if (request()->file('certificado')) {
            $file = request()->file('certificado');

            $ext = $file->guessClientExtension();

            if (in_array($ext, $mimes)) {
                $fileUploaded = $file->storeAs('facturaelectronica/' . $config->id, 'cert.' . $ext, 'local');
            }
        }

        if (request()->file('certificado_test')) {
            $file = request()->file('certificado_test');

            $ext = $file->guessClientExtension();

            if (in_array($ext, $mimes)) {
                $fileUploaded = $file->storeAs('facturaelectronica/' . $config->id, 'test.' . $ext, 'local');
            }
        }

        if (request()->wantsJson()) {
            return response($config, 200);
        }

        flash('Configuracion de factura electronica Actualizada', 'success');

        return back();
    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy(ConfigFactura $config)
    {
        $this->authorize('update', $config);
     
        if (Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/cert.p12')) {
            Storage::delete('facturaelectronica/' . $config->id . '/cert.p12');
        }

        $config->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        flash('Configuracion Eliminada', 'success');

        return back();
    }
}
