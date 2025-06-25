<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Evaluation;
use App\Http\Controllers\Controller;
use App\Opevaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
      
    }

    public function index(Appointment $appointment)
    {
       return $appointment->evaluations;
    }

    public function store(Appointment $appointment)
    {
       $data = $this->validate(request(), [
            'opevaluation_id' => 'required',
            'name' => 'required',
            'category' => 'required',
            'zone' => 'sometimes'
            
       ]);
       
       $data['patient_id'] = $appointment->patient_id;

       $evaluation = $appointment->evaluations()->create($data);

       return $evaluation;
    }

    public function update(Appointment $appointment, Evaluation $evaluation)
    {
         $data = $this->validate(request(), [
            'opevaluation_id' => 'required',
            'name' => 'required',
            'category' => 'required',
            'zone' => 'sometimes'
       ]);
        
        $data['patient_id'] = $appointment->patient_id;
        
        $evaluation->fill($data);
        $evaluation->save();

        return '';
    }

    public function destroy(Appointment $appointment, Evaluation $evaluation)
    {
      
        $evaluation->delete();

        return '';
    }

    // public function storeOption()
    // {
    //    $data = $this->validate(request(), [
    //         'category' => 'required',
    //         'name' => 'required|unique:opevaluations,name',

    //    ]);

    //    $option = Opevaluation::create($data);

    //    return $option;
    // }
}
