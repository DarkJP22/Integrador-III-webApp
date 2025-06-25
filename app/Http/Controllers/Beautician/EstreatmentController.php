<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Estreatment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class EstreatmentController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
      
    }
    public function index(Appointment $appointment)
    {
       return $appointment->estreatments;
    }

    public function store(Appointment $appointment)
    {
      
       $data = $this->validate(request(), [
            'optreatment_id' => 'required',
            'name' => 'required',
            'category' => 'required',
           
       ]);

       $data['patient_id'] = $appointment->patient_id;

       $estreatment = $appointment->estreatments()->create($data);

        if(!$appointment->patient->optreatments->contains($estreatment->optreatment_id)){
            $appointment->patient->optreatments()->attach($estreatment->optreatment_id,['appointment_id' => $appointment->id]);
        }
       

       return $estreatment->load('optreatment');
    }
    public function update(Appointment $appointment, Estreatment $estreatment)
    {
        $data = $this->validate(request(), [
            'sessions' => 'required|numeric',
            'discount' => 'required|numeric',
            
    
            ]);
        

        $estreatment->fill($data);
        $estreatment->save();

        return '';
    }

    public function destroy(Appointment $appointment, Estreatment $estreatment)
    {
        if( $appointment->patient->optreatments()->where('appointment_id', $appointment->id)->get()->contains($estreatment->optreatment_id))
        {
            $appointment->patient->optreatments()->detach($estreatment->optreatment_id);
        }
        
        $estreatment->delete();

        return '';
    }

}
