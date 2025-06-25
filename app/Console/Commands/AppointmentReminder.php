<?php

namespace App\Console\Commands;

use App\Jobs\SendAppNotificationJob;
use App\Jobs\SendAppPhoneMessageJob;
use App\Notifications\ReminderNotification;
use Illuminate\Console\Command;
use App\Reminder;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AppointmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:appointmentReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recordatorio de cita al paciente';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $reminders = Reminder::has('resource')->with('resource')->where('active', 1)->get();

        $reminders->each(function ($reminder) {
            $date = Carbon::parse($reminder->resource->start)->format('Y-m-d h:i:s A');
            $dtReminder = Carbon::parse($reminder->reminder_time);

            //\Log::info('greaterThanOrEqualTo ' . Carbon::now()->greaterThanOrEqualTo($dtReminder));

            if (Carbon::now()->greaterThanOrEqualTo($dtReminder)) {

                $reminder->active = 0;
                $reminder->save();

                $usersPatients = $reminder->resource->patient->user()->whereHas('roles', function ($query) {
                    $query->where('name', 'paciente');
                })->get();

                $usersToPush = $usersPatients->pluck('push_token')->filter();

                if (count($usersToPush)) {
                    $title = 'Recordatorio Cita';
                    $message = 'Esto es un recordatorio que tienes un cita el ' . $date;
                    $extraData = [
                        'type' => 'reminder-appointment',
                        'title' => $title,
                        'body' => $message,
                        'url' => '/notifications',
                        'resource_id' => $reminder->id
                    ];

                    SendAppNotificationJob::dispatch($title, $message, $usersToPush, $extraData)->afterCommit();
                }

                if ($reminder->resource->patient?->whatsapp_number) {
                    $message = "Esto es un recordatorio que tienes un cita el a nombre de {$reminder->resource->patient?->first_name} el {$date}";
                    SendAppPhoneMessageJob::dispatch($message, $reminder->resource->patient?->fullWhatsappPhone)->afterCommit();
                }

                try {
                    Notification::send($usersPatients, new ReminderNotification($reminder));
                } catch (Exception $e)  //Swift_RfcComplianceException
                {
                    \Log::error($e->getMessage());
                }
            }
        });

        $this->info('Hecho recordatorios de citas enviadas');
    }
}
