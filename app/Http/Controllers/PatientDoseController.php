<?php

namespace App\Http\Controllers;

use App\Dosereminder;
use Illuminate\Http\Request;
use App\Patient;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\User;

class PatientDoseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
       
    }

    public function index(Patient $patient)
    {

        if (request()->wantsJson()) {
            return response($patient->dosereminders, 200);
        }

        return $patient->dosereminders;
    }

    /**
     * Guardar paciente
     */
    public function store(Patient $patient)
    {
        $validated = request()->validate([
            'medicine_id' => 'required',
            'medicine' => 'nullable',
            'schema' => 'required',
            'hours' => 'required',
            'start_at' => 'required',
            'days' => 'required',
        ]);

            $validated['end_at'] = Carbon::parse($validated['start_at'])->addDays($validated['days'])->toDateTimeString();
       
            $dose =  $patient->dosereminders()->create($validated);
           
        
      
     
        if (request()->wantsJson()) {
            return response($dose, 201);
        }
        

        flash('Recordatorio Credo', 'success');

        return Redirect()->back();
    }

    /**
     * Guardar paciente
     */
    public function update(Patient $patient, Dosereminder $dosereminder)
    {
        $validated = request()->validate([
            'medicine_id' => 'required',
            'medicine' => 'nullable',
            'schema' => 'required',
            'hours' => 'required',
            'start_at' => 'required',
            'days' => 'required',
          
        ]);

        $validated['end_at'] = Carbon::parse($validated['start_at'])->addDays($validated['days'])->toDateTimeString();

        $dosereminder->fill($validated);
        $dosereminder->save();
     
        if (request()->wantsJson()) {
            return response($dosereminder, 200);
        }
        

        flash('Recordatorio actualizado', 'success');

        return Redirect()->back();
    }

   
}
