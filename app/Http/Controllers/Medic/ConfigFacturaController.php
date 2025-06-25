<?php

namespace App\Http\Controllers\Medic;

use App\Enums\OfficeType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\ConfigFactura;
use App\Office;
use App\Activity;
use Illuminate\Support\Facades\Validator;

class ConfigFacturaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
      
    }

    public function edit(Office $office)
    {
        if(!auth()->user()->subscriptionPlanHasFe()) {return redirect('/medic/offices'); }
        
        if(!auth()->user()->hasOffice($office->id)) {return redirect('/medic/offices'); }

        if($office->type !== OfficeType::MEDIC_OFFICE) { return redirect('/medic/offices'); }


        $configFactura = $office->configFactura->first();
        $configFactura?->load('activities');
        //$this->authorize('update', $configFactura);

        return view('medic.offices.editConfigFactura', compact('office', 'configFactura'));
    }

    /**
     * Guardar paciente
     */
    public function store(Office $office)
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

        $config = $office->configFactura()->create($data);

        if (request('CodigoActividad')) {
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
            return response($config, 201);
        }

        flash('Configuracion de factura electronica Creada', 'success');

        return Redirect('medic/offices');
    }

   
}
