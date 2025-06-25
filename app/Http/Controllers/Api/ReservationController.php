<?php

namespace App\Http\Controllers\Api;

use App\Appointment;
use App\Events\AppointmentCreatedEvent;
use App\Notifications\AppointmentConfirmation;
use Illuminate\Http\Request;
use App\Repositories\AppointmentRepository;
use App\Notifications\NewAppointment;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppNotificationJob;
use App\Jobs\SendAppPhoneMessageJob;
use App\Record;
use App\Reminder;
use App\Repositories\IncomeRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ReservationController extends Controller
{
    protected $appointmentRepo;
    protected $incomeRepo;
    protected $client;

    public function __construct(AppointmentRepository $appointmentRepo, IncomeRepository $incomeRepo)
    {
        $this->middleware('auth');
        $this->appointmentRepo = $appointmentRepo;
        $this->incomeRepo = $incomeRepo;
        $this->client = new Client([
            'timeout' => 60,
        ]);
       
    }

    /**
     * Lista de todas las citas de un doctor sin paginar
     */
    public function store(Request $request)
    {
        
        $data = $this->validate(request(), [
            'title' => 'required',
            'date' => 'required',
            'start' => 'required',
            'end' => 'required',
            'patient_id' => 'required',
            'user_id' => 'required',
            'office_id' => 'required',
            'allDay' => 'sometimes',
            'backgroundColor' => 'sometimes',
            'borderColor' => 'sometimes',
        ]);

        $data['backgroundColor'] = request('backgroundColor') ?? '#374850';
        $data['borderColor'] = request('borderColor') ?? '#374850';

        $appointmentsToday = $request->user()->appointmentsToday();

        if ($appointmentsToday >= 2) {
            if (request()->wantsJson()) {
                return response(['message' => 'No se puede crear más de dos citas al dia!!'], 422);
            }
        }


        $appointment = $this->appointmentRepo->store($data, request('user_id'));

        
//        if ($appointment->isCreatedByPatient())
//            $dataIncome['type'] = 'I'; // cobro por cita generada de paciente
//        else
//            $dataIncome['type'] = 'P'; // pendiente
//
//        $dataIncome['medic_type'] = $appointment->user->specialities->count() > 0 ? 'S' : 'G';
//        $dataIncome['amount'] = getAmountPerAppointmentAttended();
//        $dataIncome['appointment_id'] = $appointment->id;
//        $dataIncome['office_id'] = $appointment->office_id;
//        $dataIncome['date'] = $appointment->date;
//        $dataIncome['month'] = $appointment->date->month;
//        $dataIncome['year'] = $appointment->date->year;
//
//
//
//        $income = $this->incomeRepo->store($dataIncome, $appointment->user_id);
     
        $appointment->load('patient', 'office', 'user');

        $ids_patients = $appointment->user->patients()->pluck('patient_id')->all();

        $ids_patients[] = $appointment->patient->id;

        $appointment->user->patients()->sync($ids_patients);

        // broadcast pusher
        try {
            AppointmentCreatedEvent::dispatch($request->user(), $appointment);
        } catch (\Exception $ex) {
            \Log::error('ERROR BROADCAST: '.json_encode($ex->getMessage()));
        }

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

            $appointment->user->notify(new NewAppointment($appointment));

            $appointment->user->assistants->each->notify(new NewAppointment($appointment));

            if ($appointment->patient->email) {
                $appointment->patient->notify(new AppointmentConfirmation($appointment));
            }


        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
        {
            \Log::error($e->getMessage());
        }    

        if($appointment->user?->phone_number){
            $message = "Nueva Cita Reservada. El paciente ". $appointment->patient?->fullname." ha reservado una cita para el " . Carbon::parse($appointment->start)->format('Y-m-d h:i:s A');
            SendAppPhoneMessageJob::dispatch($message, $appointment->user?->fullPhone)->afterCommit();
        }
            
        
        

        Reminder::create([
            'reminder_time' => Carbon::parse($appointment->start)->subDay()->format('Y-m-d h:i:s'),
            'resource_id' => $appointment->id,
            'resource_type' => get_class($appointment),
        
        ]);
           
        $appointmentsToday = auth()->user()->appointmentsToday();

        $resp = [
            'appointment' => $appointment,
            'appointmentsToday' => $appointmentsToday
        ];

        return $resp;



    }


    /**
     * Eliminar consulta(cita) ajax desde calendar
     */
    public function destroy($id)
    {
        $appointment = $this->appointmentRepo->findById($id);
        $patient = $appointment->patient;
        $appointment->reminders()->delete();

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

        if (!$appointment) {

            return response(['message' => 'No se puede eliminar consulta ya que se encuentra iniciada!!', 'appointmentsToday' => $appointmentsToday], 422);


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
