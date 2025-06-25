<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Treatment;
use App\Medicine;

class TreatmentController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
      
    }

    public function store()
    {

        $treatment = Treatment::create(request()->all());

        $appointment = $treatment->appointment;
        
        if($appointment){
            $medicine = Medicine::create([
                'name' => request('name'),
                'receta' => request('comments'),
                'patient_id' => $appointment->patient_id,
                'user_id' => auth()->id(),
            ]);
        }

        return $treatment;

    }
    public function destroy($id)
    {

        $treatment = Treatment::findOrFail($id)->delete();


        return '';

    }
}
