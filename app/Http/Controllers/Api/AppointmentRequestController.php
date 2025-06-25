<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateAppointmentRequest;
use App\Events\AppointmentRequestCreatedEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppNotificationJob;
use App\Jobs\SendAppPhoneMessageJob;
use App\Notifications\NewAppointmentRequestNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class AppointmentRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generateUrl()
    {
        $data = $this->validate(request(), [
            'medic_id' => ['required', 'exists:users,id']
        ]);
        return URL::temporarySignedRoute(
            'appointmentRequestForm', now()->addMinutes(120), ['medic' => $data['medic_id'], 'user' => auth()->user()->id]
        );
    }

    public function store(User $medic, User $user, CreateAppointmentRequest $createAppointmentRequest)
    {
        \Log::info('CreateAppointmentRequest '.json_encode(request()->all()));
        $appointmentRequest = $createAppointmentRequest($medic, $user, request()->all());

        // broadcast pusher
        try {
            AppointmentRequestCreatedEvent::dispatch($user, $appointmentRequest);
        } catch (\Exception $ex) {
            Log::error('ERROR BROADCAST: '.json_encode($ex->getMessage()));
        }

        $operator = User::whereHas('roles', function ($query) {
            $query->where('name', 'operador');
        })->first();

        $operator?->notify(new NewAppointmentRequestNotification($appointmentRequest, '/operator/appointment-requests'));

        if ($medic->push_token) {
            $title = 'Nueva solicitud de Cita';
            $message = 'Generada el '.Carbon::parse($appointmentRequest->date)->format('Y-m-d h:i:s A');
            $extraData = [
                'type' => 'appointment-request',
                'title' => $title,
                'body' => $message,
                'url' => '/medic/appointments',
                'resource_id' => $appointmentRequest->id
            ];

            SendAppNotificationJob::dispatch($title, $message, [$medic->push_token], $extraData)->afterCommit();

        }

        if ($appointmentRequest->office?->whatsapp_number) {
            $message = "Hola deseo reservar una cita a nombre de {$appointmentRequest->patient?->first_name}. Ingresa a la app ponerte en contacto con el paciente";
            SendAppPhoneMessageJob::dispatch($message, $appointmentRequest->office?->fullWhatsappPhone)->afterCommit();
        }


    }

}
