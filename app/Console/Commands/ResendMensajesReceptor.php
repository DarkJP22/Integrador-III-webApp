<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Receptor;
use App\Services\MensajeReceptorService;

class ResendMensajesReceptor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:resendReceptor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca todo los mensajes de receptor que tienen el status de no enviar y verifica si de verdad no fueron enviados y si no los envia';

    protected $mensajeReceptorService;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MensajeReceptorService $mensajeReceptorService)
    {
        $this->mensajeReceptorService = $mensajeReceptorService;
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $receptors = Receptor::where('sent_to_hacienda', 0)->get();
        $totalEnviadas = 0;
        foreach($receptors as $receptor){
            $result = $this->sendHacienda($receptor);
            
            if($result){
                $totalEnviadas++; 
            }
        }

        $this->info('Hecho, ' . $totalEnviadas . ' Mensajes receptor reenviados');

        
    }

    public function sendHacienda(Receptor $receptor)
    {
        if( $this->comprobarRecepcion($receptor) ){ //verificamos si ya fue enviado la factura y actualizamos status fe
            //\Log::info('Console - Mensaje Receptor ya habia sido enviado');
            return false;
        }
        
        $result = $this->mensajeReceptorService->sendDocument($receptor);

        if (!$result) {
            $receptor->update([
                'sent_to_hacienda' => 1,
                'status' => 1
            ]);

            return true;

        }

        return false;
    }

    public function comprobarRecepcion(Receptor $receptor)
    {
        //\Log::info('entro a comprobar recepcion');
        $config = $receptor->obligadoTributario;
        $clave = $receptor->Clave . '-' . $receptor->NumeroConsecutivoReceptor;
        $respHacienda = $this->mensajeReceptorService->recepcionDocument($clave, $config);
       
        
        if (isset($respHacienda->{'ind-estado'})) {

            if($receptor->status_fe != 'aceptado'){
                $receptor->status_fe = $respHacienda->{'ind-estado'};
            }

            if (isset($respHacienda->{'respuesta-xml'})) {
                $receptor->resp_hacienda = json_encode($this->mensajeReceptorService->decodeRespuestaXML($respHacienda->{'respuesta-xml'}));
            }

            $receptor->sent_to_hacienda = 1;
            $receptor->save();

            return true;
        }

        return false;
    }
}
