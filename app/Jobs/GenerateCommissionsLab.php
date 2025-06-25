<?php

namespace App\Jobs;

use App\Commission;
use App\CommissionTransaction;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateCommissionsLab implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('handle GenerateCommissionsLab');

        DB::transaction(function ()
        {
            $medics = User::whereHas('roles', function ($query) {
                $query->where('name', 'medico');
            })->where('active', 1)
            ->where('commission_affiliation', 1)
            ->get();
    
    
            $medics->each(function ($medic)
            {
                $transactions = CommissionTransaction::where('user_id', $medic->id)
                                    ->whereNull('paid_at')
                                    ->whereNull('commission_id')
                                    ->get();
    
                if($transactions->count()){
                    $commission = Commission::create([
                        'user_id' => $medic->id,
                        'Total' => $transactions->sum('Total'),
                    ]);
                    $transactions->each->update([
                        'commission_id' => $commission->id
                    ]);

                    \Log::info('medic: '. $medic->name.' transactions:'. $transactions->count());
                }
                
            });
            
        });
    }
}
