<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Enums\TypeOfHealthProfessional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Repositories\AppointmentRepository;
use App\Patient;
use App\Repositories\PatientRepository;
use App\Appointment;

class AgendaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(public AppointmentRepository $appointmentRepo, public PatientRepository $patientRepo)
    {
        $this->middleware('auth');

    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        $this->authorize('viewAny', Appointment::class);

        if (!auth()->user()->hasRole('medico')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['office'] = request('office');//Session::get('office_id');
        $search['date'] = request('date') ? request('date') : Carbon::now()->toDateString();

        $search['dir'] = 'ASC';

        $appointments = $this->appointmentRepo->findAllByDoctor(auth()->id(), $search);

        // if ($search['office']) {
        //     return view('medic.appointments.index', compact('appointments', 'search'));
        // } else {
        //     return view('medic.appointments.historical', compact('appointments', 'search'));
        // }


        if (request()->wantsJson()) {
            return response($appointments, 200);
        }

        return view('agenda.index', compact('appointments', 'search'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('medico')) {
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
                'name' => 'Semana '.$key.' ('.head($week).' | '.last($week).')',
                'value' => $key
            ];
            $selectWeeks[] = $itemSelect;
        }


        return view('agenda.create', compact('appointments', 'p', 'selectWeeks', 'clinic_id'));
    }

    /**
     * Mostrar vista de actualizar consulta(cita)
     */
    public function edit($id)
    {
        if (!auth()->user()->hasRole('medico')) {
            return redirect('/');
        }

        if (auth()->user()->type_of_health_professional !== TypeOfHealthProfessional::MEDICO) {
            return redirect('/');
        }


        if (!auth()->user()->hasSubscription()) {
            return redirect('/');
        } // verifica que tiene subscription

        if (auth()->user()->monthlyCharge()->count()) {
            return redirect('/');
        } //verifica que tiene pagos pendientes

        $appointment = $this->appointmentRepo->findById($id);

        $this->authorize('update', $appointment);

        $appointment = $this->appointmentRepo->update_status($id, 1);

        $patient = $this->patientRepo->findById($appointment->patient->id);

        $history = $patient->history;
        $appointments = $patient->appointments()->with('user', 'patient.medicines', 'diagnostics', 'diseaseNotes', 'physicalExams', 'treatments', 'labexams',
            'vitalSigns')->where('appointments.id', '!=', $appointment->id)->where('status', AppointmentStatus::STARTED)->where('appointments.date', '<=', $appointment->date)->orderBy('start',
            'DESC')->limit(3)->get();

        $files = [];//$patient->archivos()->where('appointment_id', $id)->get(); //Storage::disk('s3')->files("patients/". $appointment->patient->id ."/files");

        $tab = request('tab');

        return view('agenda.edit', compact('appointment', 'files', 'history', 'appointments', 'patient', 'tab'));
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
        $history = $this->patientRepo->findById($appointment->patient->id)->history;

        return view('appointments.print', compact('appointment', 'history'));
    }

    /**
     * imprime resumen de la consulta
     */
    public function printTreatment(Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        //$appointment = $this->appointmentRepo->findById($id);
        $history = $this->patientRepo->findById($appointment->patient->id)->history;

        return view('appointments.print-treatment', compact('appointment'));
    }

    /**
     * imprime resumen de la consulta
     */
    public function pdf(Appointment $appointment)
    {
        $this->authorize('update', $appointment);


        $history = $this->patientRepo->findById($appointment->patient->id)->history;
        $data = [
            'appointment' => $appointment->load('office', 'patient.medicines', 'diseaseNotes', 'physicalExams', 'labexams', 'treatments', 'diagnostics'),
            'history' => $history
        ];
        // dd($data);
        //\PDF::setOptions(['orientation' => 'landscape']);
        $pdf = \PDF::loadView('appointments.pdf', $data);//->setPaper('a4', 'landscape');

        return $pdf->download('summary.pdf');


    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy($id)
    {

        $result = $this->appointmentRepo->delete($id);


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'No se puede eliminar consulta ya que se encuentra iniciada!!'], 422);

        }


        return back();
    }

    /**
     * imprime resumen de la consulta
     */
    public function printAgenda()
    {
        if (!auth()->user()->hasRole('asistente') && !auth()->user()->hasRole('clinica') && !auth()->user()->hasRole('operador')) {
            return redirect('/');
        }

        if (!auth()->user()->hasRole('operador')) {
            $clinic = $this->getClinic();
            if (!$clinic->hasMedic(request('medic'))) { // si es parte de la clinica el medico.. permitirle imprimir
                return redirect('/');
            }

        }


        $search['date'] = request('date') ? request('date') : Carbon::now()->toDateString();
        $search['dir'] = 'ASC';


        $appointments = $this->appointmentRepo->findAllByDoctor(request('medic'), $search, $limit = 50);


        return view(auth()->user()->userRole().'.agenda.print', compact('appointments', 'search'));
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

    /**
     * imprime resumen de la consulta
     */
    public function printAppointment(Appointment $appointment)
    {
        if (!auth()->user()->hasRole('asistente') && !auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $clinic = $this->getClinic();

        if (!$clinic->hasMedic($appointment->user_id)) { // si es parte de la clinica el medico.. permitirle imprimir
            return redirect('/');
        }


        return view(auth()->user()->userRole().'.agenda.printOne', compact('appointment'));
    }

    public function confirm(Appointment $appointment)
    {

        $appointment->update([
            'confirmed' => 1
        ]);

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return 'confirmed';
    }

    public function unconfirm(Appointment $appointment)
    {

        $appointment->update([
            'confirmed' => 0
        ]);

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return 'unconfirmed';
    }


}
