<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class AuthorizationCodesClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:authorizationCodesClear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mantenimiento de la tabla de codigos de autorización de registro';

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

        //\DB::table('expedient_codes')->where('created_by_purchase', 1)->where('status', 0)->where('exp_date','<', Carbon::today()->subDays(180))->delete();
        \DB::table('register_authorization_codes')->where('created_at', '<', Carbon::now()->subDays(3))->delete();
       
        //$this->info('Hecho, Notifications clear month ago '. Carbon::today()->subDays(30));
    }
}
