<?php

namespace App\Console\Commands;

use App\AffiliationUsers;
use Carbon\Carbon;
use Illuminate\Console\Command;

class inactiveAffiliation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:inactive-affiliation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $afiliaciones = AffiliationUsers::where('active', 'Approved')->get();

foreach ($afiliaciones as $afiliacion) {
    $fechaActivacion = $afiliacion->updated_at;

    // Calculamos vencimiento según tipo
    switch ($afiliacion->type_affiliation) {
        case 'Monthly':
            $vence = $fechaActivacion->addMonth();
            break;
        case 'Semi-Annually':
            $vence = $fechaActivacion->addMonths(6);
            break;
        case 'Annually':
            $vence = $fechaActivacion->addYear();
            break;
        default:
            continue 2; // Salta si no se reconoce el tipo
    }

    if (Carbon::now()->greaterThanOrEqualTo($vence)) {
        $afiliacion->active = 'Denied';
        $afiliacion->save();
    }
}
        $this->info('Comando de desactivación de afiliaciones inactivas ejecutado correctamente.');
    }
}
