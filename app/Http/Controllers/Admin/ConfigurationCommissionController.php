<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;




class ConfigurationCommissionController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function update()
    {

       $data = $this->validate(request(), [
            'porc_commission' => ['required', 'numeric'],
            'porc_reference_commission' => ['required', 'numeric']
       ]);

       Setting::setSettings($data);

       flash('Configuracion Guardada', 'success');

       return redirect()->back();
    }


}
