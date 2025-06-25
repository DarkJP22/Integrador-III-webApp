<?php

namespace App\Console\Commands;

use App\Dosereminder;
use App\Events\DoseReminderEvent;
use App\Jobs\SendAppNotificationJob;
use Illuminate\Console\Command;
use App\MedicineReminder;
use Carbon\Carbon;

//use App\Notifications\AppointmentReminder as ReminderNotification;
use App\Medicine;
use App\Notifications\AppInformation;
use App\Pmedicine;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class DosesReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:dosesReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recordatorio de toma de medicamentos para el paciente';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client([
            'timeout' => 60,
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //dd(Carbon::now()->toDateString());
        $now = Carbon::now();
        $reminders = Dosereminder::with('patient', 'medicine')->where('active', 1)->where('end_at', '>=',
            $now->toDateString())->get();

        $countNotification = 0;

        foreach ($reminders as $reminder) {
            if (count($reminder->hours)) {

                foreach ($reminder->hours as $hour) {

                    $timeArray = explode(':', $hour);

                    if (!isset($timeArray[0]) || !isset($timeArray[1])) { // skip hour reminder
                        continue;
                    }

                    $dtReminder = $now->copy()->setTime($timeArray[0], $timeArray[1]);

                    if ($now->setSeconds(0)->toDateTimeString() === $dtReminder->toDateTimeString()){


                        $usersToPush = [];

                        $users_patient = $reminder->patient->user()->whereHas('roles', function ($query) {
                            $query->where('name', 'paciente');
                        })->get();

                        $title = 'Recordatorio de toma de medicamento';
                        $body = 'Esto es un recordatorio de toma de medicamento '.$reminder->medicine?->name.' a las '.$dtReminder->format('Y-m-d h:i:s A').' para el paciente '.$reminder->patient?->fullname;

                        $data = [
                            'type' => 'doseReminder',
                            'title' => $title,
                            'body' => $body,
                            'url' => '/notifications',
                            'resource_id' => $reminder->id,
                        ];

                        foreach ($users_patient as $user) {

                            if ($user->push_token) {

                                $usersToPush[] = $user->push_token;

                                $notificationItem = [
                                    ...$data,
                                    'user_id' => $user->id,
                                    'media' => ''
                                ];

                                try {

                                    $user->notify(new AppInformation($notificationItem));
                                } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                                {
                                    \Log::error($e->getMessage());
                                }
                            }

                            DoseReminderEvent::dispatch($user, $reminder);

                            // if($user->email){
                            //     try {

                            //         $user->notify(new ReminderNotification($reminder->appointment));


                            //     } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                            //     {
                            //         \Log::error($e->getMessage());
                            //     }    

                            // }

                        }

                        if (count($usersToPush)) {

                            SendAppNotificationJob::dispatch($title, $body, $usersToPush, $data)->afterCommit();


                        }


                        $countNotification++;

                        // \Log::info('diff in hours ' . Carbon::now()->diffInHours($dtReminder));
                    }


                }
            }

        }
        //\Log::info($countNotification . ' recordatorios de toma de medicamento enviados');
        $this->info('Hecho, '.$countNotification.' recordatorios de toma de medicamento enviados');
    }
}
