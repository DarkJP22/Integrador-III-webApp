<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Appointment;
use Carbon\Carbon;
use App\User;
use App\Notifications\Poll;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SendPolls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:sendPolls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de la encuesta para feedback al medico o clinica';

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
        $appointments = Appointment::where('status', 1)->get();
        $countNotification = 0;

        foreach ($appointments as $appointment) {
            if ($appointment->date != '0000-00-00 00:00:00') {
                $patientId = $appointment->patient_id;
                $medicId = $appointment->user_id;
                $appointment_date = Carbon::parse($appointment->date);

                $users = User::whereHas('roles', function ($query) {
                    $query->where('name', 'paciente');
                });

                $user = $users->whereHas('patients', function ($query) use ($patientId) {
                    $query->where('patients.id', $patientId);
                })->first();

              
                if ($user && (int)Carbon::now()->diffInDays($appointment_date) == 7) {

                    if ($user->email) {

                        try {

                            $user->notify(new Poll($medicId));


                        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                        {
                            \Log::error($e->getMessage());
                        }    

                        
                        

                        $countNotification++;

                       // \Log::info(Carbon::now()->diffInDays($appointment_date));
                    }
                }



            }

        }
       // \Log::info($countNotification . ' encuestas de citas enviadas');
        $this->info('Hecho, ' . $countNotification . ' encuestas de citas enviadas');
    }
}
