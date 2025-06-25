<?php

namespace App\Console\Commands;

use App\Notifications\GeneralAdminNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\FarmaciaService;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;


class DisablePuntoVentaFarmacia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:disablePOS';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inabilita el acceso al punto de venta de farmacias';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected FarmaciaService $farmaciaService)
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
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'farmacia');
        })->where('active', 1)->whereHas('subscription', function (Builder $query) {
            $query->where('cost', '>', 0)
                  ->where('ends_at','<', Carbon::now());
        })->with('subscription','settings', 'pharmacies')->get();
       
        $countPOS = 0;
        $cuentasDesabilitadas = [];
        $currentDate = Carbon::now();

        foreach ($users as $user) {

            if ( $user->subscription->ends_at->lt($currentDate) && (int)$user->subscription->ends_at->diffInDays($currentDate) > 1 && floatval($user->subscription->cost) > 0) { //la fecha de la subs de finalizado es igual a la fecha actual

               $result = $this->farmaciaService->disablePOS($user->pharmacies->first());
               
                if($result != 503){
                    $countPOS++;
                    $cuentasDesabilitadas[] = $user->id;
                }
               
               

            }
        }

        if($countPOS){
            $administrators = User::whereHas('roles', function ($query) {
                $query->where('name', 'administrador');
            })->get();

            $data['message'] = $countPOS . ' POS desabilitados. Usuarios: '. implode(',', $cuentasDesabilitadas);
            
            Notification::send($administrators, new GeneralAdminNotification($data));
        }


        //\Log::info($countPOS . ' POS desabilitados');
        $this->info('Hecho, ' . $countPOS . ' POS desabilitados');
    }
}
