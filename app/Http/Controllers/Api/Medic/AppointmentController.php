<?php

namespace App\Http\Controllers\Api\Medic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Appointment;

class AppointmentController extends Controller
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

    public function index(Request $request)
    {

        $user = $request->user();

        $scheduledAppointments = Appointment::with('user', 'office', 'patient')
            ->search(request(['q']))
            ->when(request('status'), fn($query, $status) =>  $query->whereIn('status', $status) )
            ->where('user_id', $user->id)
            //->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") >= ?', [now()])
            ->orderBy('start', 'DESC')
            ->limit(50)
            ->get();
        $initAppointments = Appointment::with('user', 'office', 'patient')
            ->search(request(['q']))->where('user_id', $user->id)
            ->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") < ?', [now()])
            ->orderBy('start', 'DESC')
            ->limit(50)
            ->get();


        $data = [
            'scheduledAppointments' => $scheduledAppointments,
            'initAppointments' => $initAppointments,

        ];


        return $data;
    }

    /**
     * Mostrar vista de actualizar consulta(cita)
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['user', 'office', 'patient', 'diagnostics', 'diseaseNotes', 'labexams', 'treatments', 'physicalExams', 'vitalSigns', 'evaluations', 'estreatments', 'recomendations', 'patient.labresults' => function ($query) {
            $query->limit(10)->orderBy('date', 'desc');
        }]);


        return $appointment;

        // $appointment = $this->appointmentRepo->findById($id)->load('diseaseNotes', 'physicalExams', 'diagnostics', 'treatments');
        // $patient = $appointment->patient;
        // $medicines = ($appointment->medicines) ? $appointment->medicines : [];
        // $vitalSigns = ($appointment->vitalSigns) ? $appointment->vitalSigns : [];
        // $labexams = $appointment->labexams()->where('patient_id', $patient->id)->limit(10)->get();
        // $labresults = $patient->labresults()->limit(10)->get();


        // $labexams = $labexams->groupBy(function ($exam) {
        //     return $exam->date;
        // })->toArray();

        // $dataExams = [];
        // $exam = [];

        // foreach ($labexams as $key => $value) {

        //     $exam['date'] = $key;
        //     $exam['exams'] = $value;
        //     $dataExams[] = $exam;
        // }

        // //$files = Storage::disk('s3')->files("patients/". $patient->id ."/files");
        // $files = $patient->archivos()->where('appointment_id', $id)->get();
        // $data = [
        //     'appointment' => $appointment,
        //     'files' => $files,
        //     'medicines' => $medicines,
        //     'vitalSigns' => $vitalSigns,
        //     'labexams' => $dataExams,
        //     'labresults' => $labresults
        // ];

        // return $data;

    }

    public function update(Appointment $appointment)
    {
        $data = $this->validate(request(), [
            'status' => ['sometimes']
        ]);

        $appointment->fill($data)->save();

        return $appointment->load('user', 'office', 'patient');
    }
}
