<?php

namespace App\Console\Commands;

use App\Actions\CreateUser;
use App\Patient;
use App\Role;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CreateAccountToOrfantPatients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:createAccountToOrfantPatients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Account User to Orfant Patients';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $totalUsersCreated = 0;
        $totalUsersNotCreated = 0;
        $totalUsersWithoutAccount = Patient::whereDoesntHave('user', function(Builder $query){
                $query->whereHas('roles', function(Builder $query){
                    $query->where('name', 'paciente');
                });
        })->count();


        $createUser = app(CreateUser::class);
      
        Patient::whereDoesntHave('user', function(Builder $query){
                $query->whereHas('roles', function(Builder $query){
                    $query->where('name', 'paciente');
                });
        })
        ->eachById(function($patient) use(&$totalUsersCreated, &$totalUsersNotCreated, $createUser){
           
          
                $data = $patient->toArray();

                try {
                    $user = $createUser([
                        ...$data,
                        'password' => (isset($data['password']) && $data['password']) ? $data['password'] : $data['phone_number'],
                        'name' => $data['first_name']
                    ], Role::where('name', 'paciente')->first());
        
                    $user->patients()->save($patient, ['authorization' => 1]); // se asigna a la cuenta del paciente
        
                    $totalUsersCreated++;
                  
                } catch (\Exception) {
                    $totalUsersNotCreated++;
                }
                

         
           
        });

        $this->info('Hecho, ' . $totalUsersWithoutAccount . ' usuarios sin cuenta. '.$totalUsersCreated . ' usuarios creados. '. $totalUsersNotCreated.' usuario no creados' );

    }
}
