<?php

namespace App\Http\Controllers\Beautician;

use App\Anthropometry;
use Carbon\Carbon;
use App\Repositories\AppointmentRepository;
use App\Patient;
use App\Repositories\PatientRepository;
use App\Appointment;
use App\Documentation;
use App\Http\Controllers\Controller;
use App\Opevaluation;
use App\Oprecomendation;
use App\Optreatment;
use App\Record;
use App\Repositories\MedicRepository;

class AgendaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected AppointmentRepository $appointmentRepo, protected PatientRepository $patientRepo, protected MedicRepository $medicRepo)
    {
        $this->middleware('auth');

    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {

        if (!auth()->user()->hasRole('esteticista')) {
            return redirect('/');
        }


        $search['q'] = request('q');
        $search['office'] = request('office'); //Session::get('office_id');
        $search['date'] = request('date') ? request('date') : Carbon::now()->toDateString();

        $search['dir'] = 'ASC';

        $appointments = $this->appointmentRepo->findAllByDoctor(auth()->id(), $search);


        if (request()->wantsJson()) {
            return response($appointments, 200);
        }

        return view('beautician.agenda.index', compact('appointments', 'search'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('esteticista')) {
            return redirect('/');
        }

        // por si le da desde el formulario de paciente crear la cita a este paciente sin tener que buscarlo
        $p = null;
        $clinic_id = request('clinic');


        if (request('p')) {
            $p = Patient::find(request('p'));
        }


        $appointments = $this->appointmentRepo->findAllByDoctor(auth()->id());

        $month = Carbon::now()->month;

        $carbon = Carbon::now()->startOfMonth();
        $weeks_array = [];
        $selectWeeks = [];
        while (intval($carbon->month) == intval($month)) {
            $weeks_array[$carbon->weekOfMonth][$carbon->dayOfWeek] = $carbon->toDateString();
            $carbon->addDay();
        }
        foreach ($weeks_array as $key => $week) {

            $itemSelect = [
                'name' => 'Semana ' . $key . ' (' . head($week) . ' | ' . last($week) . ')',
                'value' => $key
            ];
            $selectWeeks[] = $itemSelect;
        }



        return view('beautician.agenda.create', compact('appointments', 'p', 'selectWeeks', 'clinic_id'));
    }

    /**
     * Mostrar vista de actualizar consulta(cita)
     */
    public function edit(Appointment $appointment)
    {
        if (!auth()->user()->hasRole('esteticista')) {
            return redirect('/');
        }

        $this->authorize('update', $appointment);

        $patient = $appointment->patient;
        $optionsEvaluation = Opevaluation::select('id', 'category', 'name', 'office_id')->where('office_id', $appointment->office_id)->get()->groupBy('category');
        $optionsTreatment = Optreatment::select('id', 'category', 'name', 'CodigoMoneda', 'price', 'discount', 'office_id')->where('office_id', $appointment->office_id)->get()->groupBy('category');
        $optionsRecomendation = Oprecomendation::select('id', 'category', 'name', 'office_id')->where('office_id', $appointment->office_id)->get()->groupBy('category');


        $patient->load('optreatments');
        $appointments = $patient->appointments()->where('is_esthetic', 1)->with('optreatment')->orderByRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") ASC')->get();

        $appointment = $this->appointmentRepo->update_status($appointment->id, 1);
        $appointment->forceFill([
            'is_esthetic' => 1
        ])->save();

        $evaluations = $appointment->evaluations()
            ->where('name', '<>', 'note')
            ->get();
        $evaluationNotes = $appointment->evaluations()
            ->where('name', 'note')
            ->get();
        $anthropometry = Anthropometry::where('patient_id', $patient->id)
            ->first();
        $documentations = Documentation::where('patient_id', $patient->id)
            ->get();
        $treatments = $appointment->estreatments()
            ->with('optreatment')
            ->where('name', '<>', 'note')
            ->get();
        $treatmentNotes = $appointment->estreatments()
            ->where('name', 'note')
            ->get();

        $recomendations = $appointment->recomendations()
            ->where('name', '<>', 'note')
            ->get();
        $recomendationNotes = $appointment->recomendations()
            ->where('name', 'note')
            ->get();

        $tab = request('tab');

        return view('beautician.agenda.edit', compact('appointment', 'patient', 'tab', 'optionsEvaluation', 'optionsTreatment', 'optionsRecomendation', 'evaluations', 'anthropometry', 'documentations', 'treatments', 'recomendations', 'appointments', 'evaluationNotes', 'treatmentNotes', 'recomendationNotes'));
    }

    public function finish(Appointment $appointment)
    {

        $appointment->update([
            'finished' => 1
        ]);

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return 'finished';
    }

    /**
     * Actualizar consulta(cita)
     */
    public function update($id)
    {
        $appointment = $this->appointmentRepo->update($id, request()->all());

        return $appointment->load('patient', 'user');
    }

    public function revalorizar(Appointment $appointment)
    {

        $appointment->update([
            'revalorizar' => 1,
            'finished' => 1
        ]);

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return 'revalorizada';
    }

    public function bill(Appointment $appointment)
    {
        $appointment->update([
            'billed' => 1
        ]);

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return 'billed';
    }
    public function noshows(Appointment $appointment)
    {

        $appointment->update([
            'status' => 2
        ]);

        $appointment->income()->delete();


        if (request()->wantsJson()) {
            return response([], 204);
        }

        return back();
    }

    /**
     * imprime resumen de la consulta
     */
    public function print(Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        //$appointment = $this->appointmentRepo->findById($id);
        $patient = $this->patientRepo->findById($appointment->patient->id);
        $patient->load('optreatments');

        $evaluations = $appointment->evaluations()
            ->where('name', '<>', 'note')
            ->get()->groupBy('category');
        $evaluationNotes = $appointment->evaluations()
            ->where('name', 'note')
            ->get()->groupBy('category');
        $anthropometry = Anthropometry::where('patient_id', $patient->id)
            ->first();
        $documentations = Documentation::where('patient_id', $patient->id)
            ->get();
        $treatments = $appointment->estreatments()
            ->with('optreatment')
            ->where('name', '<>', 'note')
            ->get()->groupBy('category');
        $treatmentNotes = $appointment->estreatments()
            ->where('name', 'note')
            ->get()->groupBy('category');

        $recomendations = $appointment->recomendations()
            ->where('name', '<>', 'note')
            ->get()->groupBy('category');
        $recomendationNotes = $appointment->recomendations()
            ->where('name', 'note')
            ->get()->groupBy('category');


        // dd($anthropometry->toArray());


        return view('beautician.appointments.print', compact('appointment', 'patient', 'evaluations', 'anthropometry', 'recomendations', 'treatments', 'evaluationNotes', 'treatmentNotes', 'recomendationNotes'));
    }


    /**
     * imprime resumen de la consulta
     */
    public function pdf(Appointment $appointment)
    {
        $this->authorize('update', $appointment);


        $patient = $this->patientRepo->findById($appointment->patient->id);
        $patient->load('optreatments');

        $evaluations = $appointment->evaluations()
            ->where('name', '<>', 'note')
            ->get()->groupBy('category');
        $evaluationNotes = $appointment->evaluations()
            ->where('name', 'note')
            ->get()->groupBy('category');
        $anthropometry = Anthropometry::where('patient_id', $patient->id)
            ->first();
        $documentations = Documentation::where('patient_id', $patient->id)
            ->get();
        $treatments = $appointment->estreatments()
            ->with('optreatment')
            ->where('name', '<>', 'note')
            ->get()->groupBy('category');
        $treatmentNotes = $appointment->estreatments()
            ->where('name', 'note')
            ->get()->groupBy('category');

        $recomendations = $appointment->recomendations()
            ->where('name', '<>', 'note')
            ->get()->groupBy('category');
        $recomendationNotes = $appointment->recomendations()
            ->where('name', 'note')
            ->get()->groupBy('category');

        $data = [
            'appointment' => $appointment,
            'patient' => $patient,
            'anthropometry' => $anthropometry,
            'evaluations' => $evaluations,
            'treatments' => $treatments,
            'recomendations' => $recomendations,
            'evaluationNotes' => $evaluationNotes,
            'treatmentNotes' => $treatmentNotes,
            'recomendationNotes' => $recomendationNotes,
        ];

        $pdf = \PDF::loadView('beautician.appointments.pdf', $data); //->setPaper('a4', 'landscape');

        return $pdf->download('summary.pdf');
    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy($id)
    {

        $result = $this->appointmentRepo->delete($id);

        $record = Record::where('subject_id', $id)
            ->where('subject_type', Appointment::class)
            ->where('description', 'deleted_appointment')
            ->latest()->first();

        if ($record && request('reason')) {
            $record->description = $record->description . ' - ' . request('reason');
            $record->save();
        }


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'No se puede eliminar consulta ya que se encuentra iniciada!!'], 422);
        }


        return back();
    }



    public function getClinic()
    {
        if (auth()->user()->isAssistant()) {
            $clinic = auth()->user()->clinicsAssistants->first();
        } else {
            $clinic = auth()->user()->offices->first();
        }

        return $clinic;
    }
}
