<?php

namespace App\Http\Controllers\Api\Medic;

use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppNotificationJob;
use App\Jobs\SendAppPhoneMessageJob;
use App\Notifications\ConfirmationAppointmentRequest;
use App\Reminder;
use Illuminate\Support\Carbon;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AppointmentRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {


        $scheduledAppointments = AppointmentRequest::with('user', 'medic', 'patient')
            ->search(request(['q']))
            ->when(request('status'), fn($query, $status) => count(array_filter($status)) ? $query->whereIn('status', $status) : $query)
            ->where('medic_id', auth()->user()->id)
            //->whereRaw('DATE_FORMAT(date, "%Y-%m-%d") >= ?', [now()->toDateString()])
            ->where(function ($query) {
                $query->whereNull('scheduled_date')
                    ->orWhereRaw('DATE_FORMAT(scheduled_date, "%Y-%m-%d") >= ?', [now()->toDateString()]);
            })
            ->orderBy('start', 'ASC')
            ->orderBy('date', 'DESC')
            ->limit(50)
            ->get();

        $initAppointments = AppointmentRequest::with('user', 'medic', 'patient')
            ->search(request(['q']))
            ->when(request('status'), fn($query, $status) => count(array_filter($status)) ? $query->whereIn('status', $status) : $query)
            ->where('medic_id', auth()->user()->id)
            ->whereRaw('DATE_FORMAT(scheduled_date, "%Y-%m-%d") < ?', [now()->toDateString()])
            ->orderBy('start', 'ASC')
            ->orderBy('date', 'DESC')
            ->limit(50)
            ->get();


        $data = [
            'scheduledAppointments' => $scheduledAppointments,
            'initAppointments' => $initAppointments,

        ];


        return $data;
    }

    public function show(AppointmentRequest $appointmentRequest)
    {
        $appointmentRequest->load('user', 'medic', 'patient', 'office');


        return $appointmentRequest;
    }

    public function update(AppointmentRequest $appointmentRequest)
    {
        $data = $this->validate(request(), [
            'status' => ['sometimes'],
            'scheduled_date' => ['sometimes', 'date'],
            'start' => ['sometimes', 'date'],
            'end' => ['sometimes', 'date'],
        ]);

        $appointmentRequest->fill($data)->save();


        if ($appointmentRequest->status === AppointmentRequestStatus::SCHEDULED) {

            $appointmentRequest->update([
                'scheduled_at' => now()
            ]);

            $date = Carbon::parse($appointmentRequest->start)->format('Y-m-d h:i:s A');

            if ($appointmentRequest->user?->push_token) {
                $title = 'Confirmación de solicitud de Cita';
                $message = 'Generada el '.$date;
                $extraData = [
                    'type' => 'confirmation-appointment-request',
                    'title' => $title,
                    'body' => $message,
                    'url' => '/notifications',
                    'resource_id' => $appointmentRequest->id
                ];

                SendAppNotificationJob::dispatch($title, $message, [$appointmentRequest->user?->push_token], $extraData)->afterCommit();

            }

            if ($appointmentRequest->patient?->whatsapp_number) {
                $message = "Confirmación de solicitud de Cita a nombre de {$appointmentRequest->patient?->first_name} el {$date}";
                SendAppPhoneMessageJob::dispatch($message, $appointmentRequest->patient?->fullWhatsappPhone)->afterCommit();
            }

            try {
                $appointmentRequest->user?->notify(new ConfirmationAppointmentRequest($appointmentRequest));
            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            {
                \Log::error($e->getMessage());
            }

            $appointmentRequest->reminders()->delete();

            Reminder::create([
                'reminder_time' => Carbon::parse($appointmentRequest->start)->subDay()->format('Y-m-d h:i:s'),
                'resource_id' => $appointmentRequest->id,
                'resource_type' => get_class($appointmentRequest),

            ]);
        }


        return $appointmentRequest->load('user', 'medic', 'patient', 'office');
    }
}
