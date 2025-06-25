<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\ConfigFactura;


class ConfigFacturaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
      
    }

    /**
     * Guardar paciente
     */
    public function store(User $user)
    {

        $this->validate(request(), [
            'nombre' => 'required',
            'tipo_identificacion' => 'required',
            'identificacion' => 'required',
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
            //'certificado' => 'required',
        ]);

       


        $config = $user->configFactura()->create(request()->all());

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
            return response($config, 201);
        }

        flash('Configuracion de factura electronica Creada', 'success');

        return Redirect('admin/users/'.$user->id);
    }

   
}
