<?php

namespace App\Console\Commands;

use App\Patient;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GetFullName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:getFullName';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene el nombre completo del api de hacienda por medio de la cedula';

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
        $patients = Patient::whereNotNull('ide')->whereNull('last_name')->paginate(10);

        foreach($patients as $patient){

            $ch = curl_init('https://api.hacienda.go.cr/fe/ae?identificacion='.$patient->ide);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response);
            
            if(isset($data->nombre)){
                $patient->update([
                    'first_name' => $data->nombre,
                    
                ]);
            }
           
        }
    }
}
