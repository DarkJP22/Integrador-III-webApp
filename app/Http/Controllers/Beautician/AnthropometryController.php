<?php

namespace App\Http\Controllers\Beautician;

use App\Anthropometry;
use App\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnthropometryController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
      
    }
    public function store(Appointment $appointment)
    {
      
       $data = $this->validate(request(), [
            'items' => 'nullable|array',
           
       ]);
     
       $data['patient_id'] = $appointment->patient_id;
    
       $anthropometry = $appointment->anthropometry()->create($data);

       return $anthropometry;
    }
    public function update(Appointment $appointment, Anthropometry $anthropometry)
    {
        $data = $this->validate(request(), [
                'items' => 'nullable|array',
    
            ]);

        $data['patient_id'] = $appointment->patient_id;

        $anthropometry->fill($data);
        $anthropometry->save();

        return '';
    }
}
