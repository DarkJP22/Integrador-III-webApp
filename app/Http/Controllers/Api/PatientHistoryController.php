<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Patient;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
class PatientHistoryController extends Controller
{
    
    /**
     * Agregar medicamentos a pacientes
     */
    public function index(Patient $patient)
    {
       
        $history = $patient->history;
        $appointments = $patient->appointments()->with('user', 'diagnostics', 'diseaseNotes', 'labexams', 'treatments', 'physicalExams', 'vitalSigns')->where('status', 1)->orderBy('start', 'DESC')->limit(3)->get();//$patient->appointments->load('user','diagnostics');
        $labresults = $patient->labresults()->limit(10)->get();
        $labexams = $patient->labexams()->limit(10)->get();

        $labexams = $labexams->groupBy(function ($exam) {
            return Carbon::parse($exam->date)->toDateString();
        })->toArray();

        $dataExams = [];
        $exam = [];

        foreach ($labexams as $key => $value) {

            $exam['date'] = $key;
            $exam['exams'] = $value;
            $dataExams[] = $exam;
        }



        $data = [
            'history' => $history,
            'appointments' => $appointments,
            'labresults' => $labresults,
            'labexams' => $dataExams

        ];

        return $data;

    }
}
