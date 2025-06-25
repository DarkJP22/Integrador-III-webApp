<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Invoice;
use App\Services\FacturaService;

class ResendDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:resendDocuments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca todo los documentos que tienen el status de no enviar y verifica si de verdad no fueron enviados y si no los envia';

    protected $facturaService;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FacturaService $facturaService)
    {
        $this->facturaService = $facturaService;
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $invoices = Invoice::where('sent_to_hacienda', 0)->get();
        $totalEnviadas = 0;
        foreach($invoices as $invoice){
            $result = $this->sendHacienda($invoice);
            
            if($result){
                $totalEnviadas++; 
            }
        }

        $this->info('Hecho, ' . $totalEnviadas . ' documentos reenviados');

        
    }

    public function sendHacienda(Invoice $invoice)
    {
        if( $this->comprobarRecepcion($invoice) ){ //verificamos si ya fue enviado la factura y actualizamos status fe
            //\Log::info('Console - Documento Electronico ya habia sido enviado');
            return false;
        }
        
        $result = $this->facturaService->sendDocument($invoice);

        if (!$result) {
            $invoice->update([
                'sent_to_hacienda' => 1,
                'status' => 1
            ]);

            return true;

        }

        return false;
    }

    public function comprobarRecepcion(Invoice $invoice)
    {
        //\Log::info('entro a comprobar recepcion');
        $config = $invoice->obligadoTributario;
        $clave = $invoice->clave_fe;
        $respHacienda = $this->facturaService->recepcionDocument($clave, $config);
       
        
        if (isset($respHacienda->{'ind-estado'})) {

            if($invoice->status_fe != 'aceptado'){
                $invoice->status_fe = $respHacienda->{'ind-estado'};
            }

            if (isset($respHacienda->{'respuesta-xml'})) {
                $invoice->resp_hacienda = json_encode($this->facturaService->decodeRespuestaXML($respHacienda->{'respuesta-xml'}));
            }

            $invoice->sent_to_hacienda = 1;
            $invoice->save();

            return true;
        }

        return false;
    }
}
