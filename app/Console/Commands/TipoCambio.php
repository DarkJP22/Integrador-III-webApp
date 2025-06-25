<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Currency;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
class TipoCambio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:tipoCambio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene el tipo de cambio del dolar del banco centrar CR';

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
        $currencyDollar = Currency::whereCode('USD')->first();

        if ($currencyDollar) {
           
            $ch = curl_init('https://api.hacienda.go.cr/indicadores/tc/dolar');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $response = curl_exec($ch);
            curl_close($ch);
            
            //\Log::debug($response);

            $data = json_decode($response);
            
            if($data){
                $currencyDollar->update([
                    'exchange' => $data->compra->valor,
                    'exchange_venta' => $data->venta->valor
                ]);
            }

        }
    }
}

