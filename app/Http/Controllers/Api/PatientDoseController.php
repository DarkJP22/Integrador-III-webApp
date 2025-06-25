<?php

namespace App\Http\Controllers\Api;

use App\Dosereminder;
use Illuminate\Http\Request;
use App\Patient;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Medicine;

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

    /**
     * Guardar paciente
     */
    public function store($patientIde)
    {
        //\Log::info('request form posfarmacia, ' . json_encode(request()->all()) . ' dosis');
        $patient = Patient::where('ide', $patientIde)->first();

        if($patient){

            $medicine = Medicine::where('patient_id', $patient->id)
                                ->where('name', request('Detalle'))->first();

            if(!$medicine){

                $pharmacy = auth()->user()->pharmacies->first();
                $medicine = $patient->medicines()->create([
                    'name' => request('Detalle'),
                    'receta' => request('receta'),
                    'date_purchase' => request('created_at'),
                    'user_id' => auth()->user()->id,
                    'creator_id' => $pharmacy->id,
                    'creator_type' => get_class($pharmacy),
                    'remember_days' => 28,
                    'remember' => 0,
                    'requested_units' => 1,
                    'active_remember_for_days' => 30,
                ]);


                $validated = [
                    'medicine_id' => $medicine->id,
                    'schema' => request('dose_schema'),
                    'hours' => request('dose_hours'),
                    'start_at' => request('dose_start_at'),
                    'days' => request('dose_days'),
                ];

                $validated['end_at'] = Carbon::parse($validated['start_at'])->addDays((int)$validated['days'])->toDateTimeString();
       
                $dose =  $patient->dosereminders()->create($validated);
        

                if (request()->wantsJson()) {
                    return response($dose, 201);
                }
                

            }
        }/*else{
            \Log::info('paciente no encontrado para agregar dosis, ' . $patientIde);
        }*/
       

      
    }


    /**
     * Guardar paciente
     */
    public function update(Patient $patient, Dosereminder $dosereminder)
    {

        $dosereminder->fill(request()->all());
        $dosereminder->save();
     
        if (request()->wantsJson()) {
            return response($dosereminder, 200);
        }
        
    }

   
}
