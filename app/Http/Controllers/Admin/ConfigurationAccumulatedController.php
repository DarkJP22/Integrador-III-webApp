<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;




class ConfigurationAccumulatedController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function update()
    {

       $data = $this->validate(request(), [
            'porc_accumulated' => ['required', 'numeric']
       ]);

       Setting::setSettings($data);

       flash('Configuracion Guardada', 'success');

       return redirect()->back();
    }


}
