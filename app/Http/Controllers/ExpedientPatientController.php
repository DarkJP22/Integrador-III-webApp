<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentResource;
use Illuminate\Http\Request;
use App\Patient;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ExpedientPatientController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function show(Patient $patient)
    {
        $this->authorize('availableExpedient', auth()->user());
        $this->authorize('update', $patient);

       

        $pressures = $patient->pressures()->orderBy('created_at','DESC')->get();
        $sugars = $patient->sugars()->orderBy('created_at', 'DESC')->get();
        $medicines = $patient->medicines()->orderBy('created_at', 'DESC')->get();
        $allergies = $patient->history->allergies->load('user.roles');

        $appointments = $patient->appointments()
                        ->with('user', 'patient.medicines', 'diagnostics', 'diseaseNotes', 'physicalExams', 'treatments', 'labexams')
                        ->where('status', 1)
                        ->orderBy('start', 'DESC')
                        ->limit(3)
                        ->get();
       

        return view('expedients.show', compact('patient', 'pressures', 'sugars','medicines','allergies', 'appointments'));
    }

    public function clinicHistory(Patient $patient)
    {
       
        // $pressures = $patient->pressures()->orderBy('created_at','DESC')->get();
        // $sugars = $patient->sugars()->orderBy('created_at', 'DESC')->get();
        // $medicines = $patient->medicines()->orderBy('created_at', 'DESC')->get();
        // $allergies = $patient->history->allergies->load('user.roles');

        $appointments = $patient->appointments()
                        ->with('user', 'patient.medicines', 'diagnostics', 'diseaseNotes', 'physicalExams', 'treatments', 'labexams')
                        ->when(request('q'), function (Builder $query)
                        {
                            $query->whereHas('user', function ($query) {
                                $query->where('name', 'like', '%' . request('q') . '%')
                                    ->orWhere('ide', 'like', '%' . request('q') . '%');
                            });
                        })
                         
                        ->where('status', 1)
                        ->orderBy('start', 'DESC')
                        ->paginate(3);

        return AppointmentResource::collection($appointments)->additional(['meta' => [
                    'next_page_url' => $appointments->nextPageUrl(),
                    'prev_page_url' => $appointments->previousPageUrl(),
                 
                ]]);;
       

     
    }
}
