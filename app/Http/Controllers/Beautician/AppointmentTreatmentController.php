<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentTreatmentController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
      
    }
    public function update(Appointment $appointment)
    {
      
       $data = $this->validate(request(), [
            'optreatment_id' => 'required',
           
       ]);

       $appointment->optreatment_id = $data['optreatment_id'];
       $appointment->is_esthetic = true;
       $appointment->save();
       
       return $appointment;
    }
    
}
