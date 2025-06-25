<?php

namespace App\Http\Controllers\Api\Medic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AppointmentRepository;
use App\Appointment;
use App\Patient;

class PatientAppointmentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->middleware('auth');
        $this->appointmentRepo = $appointmentRepo;
       
    }

    public function index(Request $request, Patient $patient)
    {


        $scheduledAppointments = Appointment::with('user', 'office', 'patient')->where('patient_id', $patient->id)->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") >= ?', [now()])->orderBy('start', 'DESC')->limit(50)->get();
        $initAppointments = Appointment::with('user', 'office', 'patient')->where('patient_id', $patient->id)->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") < ?', [now()])->orderBy('start', 'DESC')->limit(50)->get();


        $data = [
            'scheduledAppointments' => $scheduledAppointments,
            'initAppointments' => $initAppointments,

        ];


        return $data;
        
    }

    // public function show(Request $request, Appointment $appointment)
    // {
    //     $appointment->load('user', 'office', 'patient', 'diagnostics', 'diseaseNotes', 'labexams', 'treatments', 'physicalExams', 'vitalSigns', 'evaluations', 'estreatments', 'recomendations');


    //     return $appointment;
        
    // }
   
    
   
    
}
