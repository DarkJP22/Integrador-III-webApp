<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Receptor;
use App\Services\MensajeReceptorService;

class ProcessMensajeReceptor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;

    protected $receptor; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Receptor $receptor)
    {
        $this->receptor = $receptor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MensajeReceptorService $mensajeReceptorService)
    {
        $result = $mensajeReceptorService->sendDocument($this->receptor);

        if (!$result) {
            $this->receptor->update([
                'sent_to_hacienda' => 1,
                
            ]);

            
        }
    }
}
