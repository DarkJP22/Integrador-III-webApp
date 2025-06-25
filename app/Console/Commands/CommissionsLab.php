<?php

namespace App\Console\Commands;

use App\Jobs\GenerateCommissionsLab;
use Illuminate\Console\Command;


class CommissionsLab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:generateCommissionsLab';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera las comisiones a pagar cada 1 y 15 del mes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        GenerateCommissionsLab::dispatch();
       
        $this->info('Job Generate Commissions Lab Despachado');
       
    }
}
