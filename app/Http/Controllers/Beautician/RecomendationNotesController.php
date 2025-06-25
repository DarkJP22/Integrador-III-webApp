<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Http\Controllers\Controller;
use App\Recomendation;
use Illuminate\Http\Request;

class RecomendationNotesController extends Controller
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

       $note = $appointment->recomendations()->create($data);

       return $note;
    }

    public function update(Appointment $appointment, Recomendation $note)
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
