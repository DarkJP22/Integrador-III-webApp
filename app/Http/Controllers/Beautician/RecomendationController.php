<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Evaluation;
use App\Http\Controllers\Controller;
use App\Oprecomendation;
use App\Recomendation;
use Illuminate\Http\Request;

class RecomendationController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Appointment $appointment)
    {
       return $appointment->recomendations;
    }

    public function store(Appointment $appointment)
    {
        $data = $this->validate(request(), [
            'oprecomendation_id' => 'required',
            'name' => 'required',
            'category' => 'required',
        ]);

        $data['patient_id'] = $appointment->patient_id;

        $recomendation = $appointment->recomendations()->create($data);

        return $recomendation;
    }
    public function update(Appointment $appointment, Recomendation $recomendation)
    {
        $data = $this->validate(request(), [
            'oprecomendation_id' => 'required',
            'name' => 'required',
            'category' => 'required',
        ]);

        $data['patient_id'] = $appointment->patient_id;

        $recomendation->fill($data);
        $recomendation->save();

        return '';
    }

    public function destroy(Appointment $appointment, Recomendation $recomendation)
    {
      
        $recomendation->delete();

        return '';
    }
}
