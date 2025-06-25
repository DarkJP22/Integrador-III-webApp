<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Estreatment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstreatmentNotesController extends Controller
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

       $note = $appointment->estreatments()->create($data);

       return $note;
    }

    public function update(Appointment $appointment, Estreatment $note)
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
