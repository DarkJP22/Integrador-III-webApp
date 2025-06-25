<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Labexam;
use App\Appointment;
use App\Repositories\PatientRepository;
use App\Repositories\AppointmentRepository;

class LabExamController extends Controller
{
    function __construct(PatientRepository $patientRepo, AppointmentRepository $appointmentRepo)
    {
        $this->middleware('auth');
        $this->patientRepo = $patientRepo;
        $this->appointmentRepo = $appointmentRepo;

    }

    /**
     * Agregar medicamentos a pacientes
     */
    public function index($id)
    {
        if (request('appointment_id')) {
            $appointment = $this->appointmentRepo->findById(request('appointment_id'));

            $labexams = $appointment->labexams()->where('patient_id', $id)->get();
        } else {
            $patient = $this->patientRepo->findById($id);
            $labexams = $patient->labexams()->get();
        }

        $labexams = $labexams->groupBy(function ($exam) {
            return $exam->date;
        })->toArray();

        $data = [];
        $exam = [];

        foreach ($labexams as $key => $value) {
            $exam['date'] = $key;
            $exam['exams'] = $value;
            $data[] = $exam;
        }

        return $data;
    }

    /**
     * Agregar medicamentos a pacientes
     */
    public function store($id)
    {
        $this->validate(request(), [
            'date' => 'required',
            'name' => 'required',
        ]);
        $data['date'] = request('date');
        $data['name'] = request('name');
        $data['patient_id'] = $id;

        $labexam = Labexam::create($data);

        $labexam->appointments()->attach(request('appointment_id')); // asociar la cita con el paciente

        return $labexam;
    }

    /**
     * Eliminar medicamentos a pacientes
     */
    public function destroy($appointmentId, $labExamId)
    {
        
        $exam = Labexam::find($labExamId);
        $appointment = Appointment::find($appointmentId);
      
        if (!$exam->appointments()->where('appointments.id', '<>', $appointmentId)->count()) {
            $exam->delete();

            return '';
        }

        $exam = $appointment->labexams()->detach($labExamId);

        return '';
    }

}
