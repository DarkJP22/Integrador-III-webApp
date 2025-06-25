<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Receptor;
use App\Services\MensajeReceptorService;

class ProcessMensajesReceptor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
   
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MensajeReceptorService $mensajeReceptorService)
    {
        $mensajes = Receptor::where('sent_to_hacienda', 0)->where('created_xml', 0)->get();

       foreach ($mensajes as $receptor) {
        
        $result = $mensajeReceptorService->sendDocument($receptor);

            if (!$result) {
                $receptor->update([
                    'sent_to_hacienda' => 1,
                    'status' => 1
                ]);
            }

       }
    }
}
