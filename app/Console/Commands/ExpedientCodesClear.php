<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class ExpedientCodesClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:expedientCodesClear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mantenimiento de la tabla de codigos de expedientes';

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
        \DB::table('expedient_codes')->whereDate('exp_date', Carbon::today())->delete();
       
        //$this->info('Hecho, Notifications clear month ago '. Carbon::today()->subDays(30));
    }
}
