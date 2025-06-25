<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Evaluation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EvaluationNotesController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
      
    }

    public function store(Appointment $appointment)
    {
       $data = $this->validate(request(), [
            'category' => 'required',
            'notes' => 'sometimes'
            
       ]);
       
       $data['patient_id'] = $appointment->patient_id;
       $data['name'] = 'note';

       $note = $appointment->evaluations()->create($data);

       return $note;
    }

    public function update(Appointment $appointment, Evaluation $note)
    {
         $data = $this->validate(request(), [
            'category' => 'required',
            'notes' => 'sometimes'
       ]);
        
        $data['patient_id'] = $appointment->patient_id;
        $data['name'] = 'note';

        $note->fill($data);
        $note->save();

        return '';
    }

   
}
