<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AppointmentRepository;
use App\Appointment;
use App\Repositories\PatientRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected PatientRepository $patientRepo)
    {
        $this->middleware('auth');
       
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $patientIds = $request->has('patient_id') ? [$request->patient_id] : $user->patients->pluck('id');

        $scheduledAppointments = Appointment::with('user', 'office', 'patient')->whereIn('patient_id', $patientIds)->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") >= ?', [now()])->orderBy('start', 'DESC')->limit(50)->get();
        $initAppointments = Appointment::with('user', 'office', 'patient')->whereIn('patient_id', $patientIds)->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") < ?', [now()])->orderBy('start', 'DESC')->limit(50)->get();


        $data = [
            'scheduledAppointments' => $scheduledAppointments,
            'initAppointments' => $initAppointments,

        ];


        return $data;
        
    }

    public function show(Request $request, Appointment $appointment)
    {
        $appointment->load('user', 'office', 'patient.labresults', 'diagnostics', 'diseaseNotes', 'labexams', 'treatments', 'physicalExams', 'vitalSigns', 'evaluations', 'estreatments', 'recomendations');


        return $appointment;
        
    }

     /**
     * imprime resumen de la consulta
     */
    public function pdf(Appointment $appointment)
    {
        
        $history = $this->patientRepo->findById($appointment->patient->id)->history;
        $data = [
            'appointment' => $appointment->load('office','patient.medicines', 'diseaseNotes', 'physicalExams', 'labexams','treatments', 'diagnostics'),
            'history' => $history
        ];

        $pdf = \PDF::loadView('appointments.pdf', $data);//->setPaper('a4', 'landscape');
        $fileName = $appointment->id. Str::random(20).'.pdf';

        Storage::disk('s3')->put('/summaries/'.$fileName, $pdf->output());

        return Storage::disk('s3')->url('/summaries/'.$fileName);

      
    }
   
    
   
    
}
