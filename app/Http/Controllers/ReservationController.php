<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Jobs\SendAppNotificationJob;
use App\Notifications\AppointmentConfirmation;
use Illuminate\Http\Request;
use App\Office;
use App\User;
use App\Repositories\AppointmentRepository;
use App\Repositories\IncomeRepository;
use App\Notifications\NewAppointment;
use App\Record;
use Edujugon\PushNotification\PushNotification;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ReservationController extends Controller
{
    protected $client;

    public function __construct(protected AppointmentRepository $appointmentRepo, protected IncomeRepository $incomeRepo)
    {
        $this->middleware('auth');
    
        $this->client = new Client([
            'timeout' => 60,
        ]);

       
    }

    /**
     * Lista de todas las citas de un doctor sin paginar
     */
    public function store()
    {
        $appointmentsToday = auth()->user()->appointmentsToday();

        if ($appointmentsToday >= 2) {
            if (request()->wantsJson()) {
                return response(['message' => 'No se puede crear más de dos citas al dia!!'], 422);
            }
        }


        $appointment = $this->appointmentRepo->store(request()->all(), request('user_id'));

            if ($appointment->isCreatedByPatient())
                $dataIncome['type'] = 'I'; // cobro por cita generada de paciente
            else
                $dataIncome['type'] = 'P'; // pendiente

            $dataIncome['medic_type'] = $appointment->user->specialities->count() > 0 ? 'S' : 'G';
            $dataIncome['amount'] = getAmountPerAppointmentAttended();
            $dataIncome['appointment_id'] = $appointment->id;
            $dataIncome['office_id'] = $appointment->office_id;
            $dataIncome['date'] = $appointment->date;
            $dataIncome['month'] = $appointment->date->month;
            $dataIncome['year'] = $appointment->date->year;



            $income = $this->incomeRepo->store($dataIncome, $appointment->user_id);
        
     
        $appointment->load('patient', 'office', 'user');

        $ids_patients = $appointment->user->patients()->pluck('patient_id')->all();

        $ids_patients[] = $appointment->patient_id;
       
        $appointment->user->patients()->sync($ids_patients);

        $appointment->user->patients()->updateExistingPivot($appointment->patient_id, ['authorization' => 1]);

        if ($appointment->user->push_token) {

            $title = 'Nueva Cita Reservada';
            $message = 'Para el ' . Carbon::parse($appointment->start)->format('Y-m-d h:i:s A');
            $extraData = [
                'type' => 'appointment',
                'title' => $title,
                'body' => $message,
                'url' => '/medic/appointments',
                'resource_id' => $appointment->id
            ];

            SendAppNotificationJob::dispatch($title, $message, [$appointment->user->push_token], $extraData)->afterCommit();

        }


        try {
            // Notificacion al médico
            $appointment->user->notify( new NewAppointment($appointment) );
           
            //$appointment->user->assistants->each->notify(new NewAppointment($appointment));
            $appointment->office->clinicsAssistants->each->notify(new NewAppointment($appointment,'/assistant/agenda?medic='. $appointment->user_id.'&date='. $appointment->date->toDateString()));
            
             if ($appointment->patient->email) {
                 $appointment->patient->notify(new AppointmentConfirmation($appointment));
             }

        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
        {
            \Log::error($e->getMessage());
        }    
        
        

       
           
        $appointmentsToday = auth()->user()->appointmentsToday();

        $resp = [
            'appointment' => $appointment,
            'appointmentsToday' => $appointmentsToday
        ];

        return $resp;



    }

    /**
     * Actualizar consulta(cita)
     */
    public function update($id)
    {

        $appointment = $this->appointmentRepo->update($id, request()->all());

        if (!$appointment) {

            if (request()->wantsJson()) {
                return response(['message' => 'No se puede cambiar de dia la consulta ya que se encuentra iniciada!!'], 422);
            }
        }

        $appointment['patient'] = $appointment->patient;
        $appointment['user'] = $appointment->user;

        if (request('medic_id')) //agregar paciente del usuario al medico tambien
        {
            $medic = User::find(request('medic_id'));

            $ids_patients = $medic->patients()->pluck('patient_id')->all();

            $ids_patients[] = $appointment->patient->id;

            $medic->patients()->sync($ids_patients);
        }




        return $appointment;

    }

    /**
     * Eliminar consulta(cita) ajax desde calendar
     */
    public function destroy($id)
    {
        $appointment = $this->appointmentRepo->findById($id);
        $patient = $appointment->patient;
        $appointment = $this->appointmentRepo->delete($id);
        $appointmentsToday = auth()->user()->appointmentsToday();

        $record = Record::where('subject_id', $id)
                ->where('subject_type', Appointment::class)
                ->where('description', 'deleted_appointment')
                ->latest()->first();

        if($record){
            $record->description = $record->description. ' - Eliminado por el mismo paciente - Paciente('.$patient?->id.'): '. $patient?->first_name;
            $record->save();
        }

        if ($appointment !== true) {
            if (request()->wantsJson()) {
                return response(['message' => 'No se puede eliminar consulta ya que se encuentra iniciada!!', 'appointmentsToday' => $appointmentsToday], 422);
            }

        }

        $data = [
            'resp' => 'ok',
            'appointmentsToday' => $appointmentsToday
        ];

        //event(new AppointmentDeleted($appointment));
       // event(new AppointmentDeletedToAssistant($appointment));

        return $data;

    }
}
