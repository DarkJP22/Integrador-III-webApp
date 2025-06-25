<?php

namespace App\Http\Controllers\Beautician;

use App\Appointment;
use App\Http\Controllers\Controller;
use App\Optreatment;
use App\Patient;
use App\Repositories\AppointmentRepository;
use App\Schedule;
use Illuminate\Support\Carbon;


class PatientAgendaController extends Controller
{
    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->middleware('auth');
        $this->appointmentRepo = $appointmentRepo;
    }

    public function index(Patient $patient)
    {
        $appointments = $patient->appointments()->where('is_esthetic', 1)->with('estreatments')->orderByRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") ASC')->get();

        return $appointments;
    }

    public function edit(Patient $patient)
    {
        if (!auth()->user()->hasRole('esteticista') && !auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        if (auth()->user()->isAssistant()) {
            $office = auth()->user()->clinicsAssistants->first();
        } else {
            $office = auth()->user()->offices->first();
        }


        $appointments = $patient->appointments()->where('is_esthetic', 1)->with('estreatments')->orderByRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") ASC')->get();
        $esteticistas = $office->users()->whereHas('roles', function ($query) {
            $query->where('name', 'esteticista');
        })->where('active', 1)->get();

        $viewRole = 'beautician.agenda.treatments';

        if (auth()->user()->isAssistant()) {
            $viewRole = 'assistant.agenda.treatments';
        }
        if (auth()->user()->isClinic()) {
            $viewRole = 'clinic.agenda.treatments';
        }

        $optreatments = Optreatment::all();

        return view($viewRole, compact('patient', 'office', 'appointments', 'esteticistas', 'optreatments'));
    }

    public function store(Patient $patient)
    {
        $data = $this->validate(request(), [
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'user_id' => ['required'],
            'room_id' => ['required'],
            'patient_id' => ['required'],
            'office_id' => ['required'],
            'optreatment_ids' => ['required'],
        ]);

        //$user = User::findOrFail($data['user_id']);
        $dateStart = Carbon::parse(request('start'));
        $dateEnd = Carbon::parse(request('end'));
        $date = $dateStart->toDateString();
        $start = $dateStart->toDateTimeString();
        $end = $dateEnd->toDateTimeString();


        if (!Schedule::where('office_id', $data['office_id'])
            ->where('user_id', $data['user_id'])
            ->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") <= ?', [$start])
            ->whereRaw('DATE_FORMAT(end, "%Y-%m-%d %H:%i:%s") >= ?', [$end])
            ->count()) {
            return response(['message' => 'Parece que no puedes reservar en esta fecha y hora ya que no hay horario programado'], 422);
        }

        if ($appoinments = Appointment::where('office_id', $data['office_id'])
            ->where('room_id', $data['room_id'])
            ->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") <= ?', [$start])
            ->whereRaw('DATE_FORMAT(end, "%Y-%m-%d %H:%i:%s") >= ?', [$end])
            ->count()
        ) {
            return response(['message' => 'Parece que no puedes reservar en esta fecha y hora ya que la sala esta ocupada'], 422);
            // throw ValidationException::withMessages([
            //     'room_id' => [__('Parece que no puedes reservar en esta fecha y hora ya que la sala esta ocupada')],
            // ]);
        }
        $treatments = Optreatment::whereIn('id', $data['optreatment_ids'])->get();
        $data['date'] = $date;
        $data['start'] = Carbon::parse(request('start'))->toDateTimeLocalString();
        $data['end'] = Carbon::parse(request('end'))->toDateTimeLocalString();
        $data['title'] = 'Seg. de ' . $treatments->implode('name', ', ');
        $data['backgroundColor'] = '#374850';
        $data['borderColor'] = '#374850';
        $data['is_esthetic'] = true;


        $appointment = $this->appointmentRepo->store($data, $data['user_id']);

        foreach ($treatments as $treatment) {
            $appointment->estreatments()->create([
                'patient_id' => $appointment->patient_id,
                'optreatment_id' => $treatment->id,
                'name' => $treatment->name,
                'category' => $treatment->category
            ]);

            if (!$appointment->patient->optreatments->contains($treatment->id)) {
                $appointment->patient->optreatments()->attach($treatment->id, ['appointment_id' => $appointment->id]);
            }
        }



        return $appointment->load('estreatments');
    }

    public function update(Patient $patient, Appointment $appointment)
    {
        $data = $this->validate(request(), [
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'room_id' => ['required'],
            'user_id' => ['required'],
            'patient_id' => ['required'],
            'office_id' => ['required'],
            'optreatment_ids' => ['required'],
        ]);
        //$user = User::findOrFail($data['user_id']);
        $dateStart = Carbon::parse(request('start'));
        $dateEnd = Carbon::parse(request('end'));
        $date = $dateStart->toDateString();
        $start = $dateStart->toDateTimeString();
        $end = $dateEnd->toDateTimeString();


        if (!Schedule::where('office_id', $data['office_id'])
            ->where('user_id', $data['user_id'])
            ->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") <= ?', [$start])
            ->whereRaw('DATE_FORMAT(end, "%Y-%m-%d %H:%i:%s") >= ?', [$end])
            ->count()) {
            return response(['message' => 'Parece que no puedes reservar en esta fecha y hora ya que no hay horario programado'], 422);
        }

        if ($appoinments = Appointment::where('office_id', $data['office_id'])
            ->where('id', '<>', $appointment->id)
            ->where('room_id', $data['room_id'])
            ->whereRaw('DATE_FORMAT(start, "%Y-%m-%d %H:%i:%s") <= ?', [$start])
            ->whereRaw('DATE_FORMAT(end, "%Y-%m-%d %H:%i:%s") >= ?', [$end])
            ->count()
        ) {
            return response(['message' => 'Parece que no puedes reservar en esta fecha y hora ya que la sala esta ocupada'], 422);
            // throw ValidationException::withMessages([
            //     'room_id' => [__('Parece que no puedes reservar en esta fecha y hora ya que la sala esta ocupada')],
            // ]);
        }

        if ($appointment->isStarted()) {

            return response(['message' => 'No se puede actualizar la cita ya que se encuentra iniciada!!'], 422);
        }
        
        $treatments = Optreatment::whereIn('id', $data['optreatment_ids'])->get();

        $data['date'] = $date;
        $data['start'] = Carbon::parse(request('start'))->toDateTimeLocalString();
        $data['end'] = Carbon::parse(request('end'))->toDateTimeLocalString();
        $data['is_esthetic'] = true;
        $data['title'] = 'Seg. de ' . $treatments->implode('name', ', ');

        $appointment->fill($data);
        $appointment->save();

        $appointment->estreatments()->delete();

       

        foreach ($treatments as $treatment) {
            $appointment->estreatments()->create([
                'patient_id' => $appointment->patient_id,
                'optreatment_id' => $treatment->id,
                'name' => $treatment->name,
                'category' => $treatment->category
            ]);

            if (!$appointment->patient->optreatments->contains($treatment->id)) {
                $appointment->patient->optreatments()->attach($treatment->id, ['appointment_id' => $appointment->id]);
            }
        }


        return $appointment->load('estreatments');
    }
    public function destroy(Patient $patient, Appointment $appointment)
    {

        $result = $this->appointmentRepo->delete($appointment->id);


        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'No se puede eliminar la cita ya que se encuentra iniciada!!'], 422);
        }
    }
}
