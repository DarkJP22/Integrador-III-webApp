<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Appointment;
use App\Jobs\SendAppNotificationJob;
use App\Repositories\AppointmentRepository;
use App\Notifications\NewAppointment;
use App\Optreatment;
use App\Record;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AppointmentController extends Controller
{
    protected $client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected AppointmentRepository $appointmentRepo)
    {
        $this->middleware('auth')->except('sendReminder');
        $this->client = new Client([
            'timeout' => 60,
        ]);

    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {

        $appointments = Appointment::where('created_by', auth()->id())->get();


        $initAppointments = $appointments->filter(function ($item, $key) {
            return $item->status > 0;
        });

        $scheduledAppointments = $appointments->filter(function ($item, $key) {
            return $item->status == 0;
        });



        return view('appointments.index', compact('initAppointments', 'scheduledAppointments'));

    }

    /**
     * Guardar consulta(cita)
     */
    public function store()
    {
        
        $appointment = $this->appointmentRepo->store(request()->all(), request('user_id'));

        if (!$appointment) {
            if (request()->wantsJson()) {
                return response(['message' => 'No se pudo crear la consulta!!!!'], 422);
            }
        }
        // estetica
        if(request()->has('optreatment_ids')){
            $treatments = Optreatment::whereIn('id', request('optreatment_ids'))->get();
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
        }
       

        //if(auth()->user()->isOperator()){
            
            if ($appointment->user->push_token) {
                $title = 'Nueva Cita Reservada';
                $message = 'Para el ' . Carbon::parse($appointment->start)->format('Y-m-d h:i:s A');
                $extraData = [
                    'type' => 'appointment',
                    'title' => $title,
                    'body' => $message,
                    'url' => '/notifications',
                    'resource_id' => $appointment->id
                ];
    
                SendAppNotificationJob::dispatch($title, $message, [$appointment->user->push_token], $extraData);
            }
         
            try {

                $appointment->user->notify(new NewAppointment($appointment));

                $appointment->user->assistants->each->notify(new NewAppointment($appointment));


            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            {
                \Log::error($e->getMessage());
            }   
                
            

        //}

        return $appointment->load('office', 'user', 'patient');
    }

    /**
     * Actualizar consulta(cita)
     */
    public function update($id)
    {
        $appointment = $this->appointmentRepo->update($id, request()->all());

        if(!$appointment){

            if (request()->wantsJson()) {
                return response(['message'=>'No se puede cambiar de dia la consulta ya que se encuentra iniciada!!'], 422);
            }
        }
        return $appointment->load('patient','user');
    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy($id)
    {
        $appointment = $this->appointmentRepo->findById($id);
        $patient = $appointment->patient;
        $result = $this->appointmentRepo->delete($id);
        $record = Record::where('subject_id', $id)
                ->where('subject_type', Appointment::class)
                ->where('description', 'deleted_appointment')
                ->latest()->first();

        if($record && request('reason')){
            $record->description = $record->description. ' - '. request('reason') . ' - Paciente('.$patient?->id.'): '. $patient?->first_name;
            $record->save();
        }

        // event(new AppointmentDeleted($appointment));
        // event(new AppointmentDeletedToAssistant($appointment));

        if (request()->wantsJson()) {

            if ($result === true) {

                return response([], 204);
            }

            return response(['message' => 'No se puede eliminar consulta ya que se encuentra iniciada!!'], 422);

        }


      

        return back();
    }

    



}
