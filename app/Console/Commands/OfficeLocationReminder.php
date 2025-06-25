<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Office;
use Carbon\Carbon;

class OfficeLocationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:officeLocationReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recordatorio de actualizacion de ubicacion de la clinica o consultorio';

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
        $offices = Office::where('notification', 1)->get();
        $countNotification = 0;

        foreach ($offices as $office) {
            if ($office->notification_date && $office->notification_date != '0000-00-00 00:00:00') {
               
           


                $dtOffice = Carbon::createFromFormat('Y-m-d H:i:s', $office->notification_date);
           

                if ((int)Carbon::now()->diffInMinutes($dtOffice) == 0) {

                    $office->notification = 1;
                    $office->save();
                  
                    $countNotification++;

                   // Log::info(Carbon::now()->diffInMinutes($dtOffice));
                }



            }

        }
        //\Log::info($countNotification . ' notificaciones de actualizacion de clinicas enviadas');
        $this->info('Hecho, ' . $countNotification . ' notificaciones de actualizacion de clinicas enviadas');
    }
}
