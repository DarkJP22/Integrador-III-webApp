<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class NotificationsClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:notificationsClear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mantenimiento de la tabla de notificaciones e invitaciones';

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

        \DB::table('notifications')->where('created_at','<', Carbon::today()->subDays(30))->delete();
        \DB::table('notifications_pharmacy_limit')->where('created_at', '<', Carbon::today()->subDays(30))->delete();
        \DB::table('notifications_clinic_limit')->where('created_at', '<', Carbon::today()->subDays(30))->delete();

        \DB::table('patient_invitations')->where('created_at', '<', Carbon::today()->subDays(30))->delete();
        //$this->info('Hecho, Notifications clear month ago '. Carbon::today()->subDays(30));
    }
}
