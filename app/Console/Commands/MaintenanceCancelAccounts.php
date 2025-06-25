<?php

namespace App\Console\Commands;

use App\Patient;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MaintenanceCancelAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:encryptCancelAccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encripta información sensible a los 5 años';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle(): void
    {

        $count = 0;

        DB::transaction(function () use (&$count) {
            User::query()
                ->where('active', 0)
                ->whereBetween('cancel_at', [now()->subYears(5)->startOfDay(), now()->subYears(5)->endOfDay()])
                ->each(function ($user) use (&$count) {

                    $ide = $user->ide;
                    $hashedIde = Hash::make($ide);

                    $user->update([
                        'ide' => $hashedIde,
                        'email' => $hashedIde,
                        'name' => $hashedIde,
                    ]);

                    Patient::where('ide', $ide)->update([
                        'ide' => $hashedIde,
                        'first_name' => $hashedIde,
                        'email' => $hashedIde,
                    ]);

                    if($user->hasRole('paciente')){

                        $user->patients->each( function($patient) use (&$count, $hashedIde){
                            $patient->user->each->update([
                                'ide' => $hashedIde,
                                'first_name' => $hashedIde,
                                'email' => $hashedIde,
                            ]);
                            $patient->update([
                                'ide' => $hashedIde,
                                'first_name' => $hashedIde,
                                'email' => $hashedIde,
                            ]);
                        });


                    }


                    $count++;
                });

        });

        $this->info('Mantenimiento Cuentas Inactivas: '.$count.' cuentas inactivas se quitó información sensible');



    }


}
