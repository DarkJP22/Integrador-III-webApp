<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;



class ConfigurationController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        
      
    }


    /**
     * Actualizar informacion basica del medico
     */
    public function update()
    {
      
        $data = request()->validate([
            'amount_attended' => 'sometimes|numeric',
            'amount_expedient' => 'sometimes|numeric',
            'subscription_months_free' => 'sometimes|numeric',
            'call_center' => 'sometimes|string',
            'url_app_pacientes_android' => 'sometimes|nullable|string',
            'url_app_pacientes_ios' => 'sometimes|nullable|string',
            'porc_accumulated' => 'sometimes|numeric',
            'porc_discount_accumulated' => 'sometimes|numeric',
            'porc_commission' => 'sometimes|numeric',
            'porc_reference_commission' => 'sometimes|numeric',
            'fixed_commission_general' => 'sometimes|numeric',
            'fixed_commission_specialist' => 'sometimes|numeric',
            'lab_exam_discount' => 'sometimes|numeric',
        ]);

        Setting::setSettings($data);
     

       
        return back();


    }


}
