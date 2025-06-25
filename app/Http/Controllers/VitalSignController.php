<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\VitalSign;

class VitalSignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
       

    }

    public function update($id)
    {

        $this->validate(request(), [
            'height' => 'numeric',
            'weight' => 'numeric',
            'mass' => 'numeric',
            'temp' => 'numeric',
            'respiratory_rate' => 'numeric',
            'blood' => 'numeric',
            'heart_rate' => 'numeric',
            'oxygen' => 'numeric',
            'blood_ps' => 'numeric',
            'blood_pd' => 'numeric',
            'glicemia' => 'numeric'

        ]);

        $vitalSign = VitalSign::findOrFail($id);
        $vitalSign->fill(request()->all());
        $vitalSign->save();

        return '';
    }
}
