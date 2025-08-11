<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SubscriptionCharge::class,
        \App\Console\Commands\AppointmentReminder::class,
        \App\Console\Commands\SendPolls::class,
        \App\Console\Commands\OfficeLocationReminder::class,
        \App\Console\Commands\MedicinesReminder::class,
        \App\Console\Commands\NotificationsClear::class,
        \App\Console\Commands\TipoCambio::class,
        \App\Console\Commands\ResendDocuments::class,
        \App\Console\Commands\ExpedientCodesClear::class,
        \App\Console\Commands\AuthorizationCodesClear::class,
        \App\Console\Commands\DosesReminder::class,
        \App\Console\Commands\MaintenanceCancelAccounts::class,
        \App\Console\Commands\DisablePuntoVentaFarmacia::class,
        \App\Console\Commands\CommissionsLab::class,
        \App\Console\Commands\CreateAccountToOrfantPatients::class,
        \App\Console\Commands\inactiveAffiliation::class //Se agrega el comando para desactivar afiliaciones inactivas grupo G1
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('telescope:prune')->daily();
        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run')->daily()->at('02:00');
        $schedule->command('queue:prune-batches --unfinished=72')->daily();
        $schedule->command('app:inactive-affiliation')->daily(); // Se agrega el comando para desactivar afiliaciones inactivas grupo G1
        //$schedule->command('model:prune')->daily();
        $schedule->command(\App\Console\Commands\MaintenanceCancelAccounts::class)->daily();
        $schedule->command(\App\Console\Commands\ProcessAnonymousAccounts::class)->daily()->at('01:00');
        $schedule->command(\App\Console\Commands\ResendDocuments::class)
                  ->everyFifteenMinutes();
        $schedule->command(\App\Console\Commands\ResendMensajesReceptor::class)
            ->everyThirtyMinutes();
        
        $schedule->command(\App\Console\Commands\TipoCambio::class)
            ->daily()->at('01:00');
        
        $schedule->command(\App\Console\Commands\OfficeLocationReminder::class)
            ->everyThirtyMinutes(); //se hace asi por que este no es necesario enviar email

        $schedule->command(\App\Console\Commands\SubscriptionCharge::class)
            ->monthlyOn(1, '00:10');

        $schedule->command(\App\Console\Commands\MaintenanceAccountsWithOverdueInvoicesCommand::class)
            ->daily();

        $schedule->command(\App\Console\Commands\NotificationsClear::class)
            ->daily();

        $schedule->command(\App\Console\Commands\MedicinesReminder::class)
            ->daily();

        $schedule->command(\App\Console\Commands\ExpedientCodesClear::class)
            ->daily();

        $schedule->command(\App\Console\Commands\AuthorizationCodesClear::class)
            ->daily();
        
        $schedule->command(\App\Console\Commands\DosesReminder::class)
            ->everyMinute();
        
        $schedule->command(\App\Console\Commands\DisablePuntoVentaFarmacia::class)
            ->daily();

        $schedule->command(\App\Console\Commands\CommissionsLab::class)->twiceMonthly(1, 16, '00:00');

        //dailyAt
        $schedule->command(\App\Console\Commands\AppointmentReminder::class) //hay q verificar si en el nuevo servidor si funciona asi
                 ->everyThirtyMinutes();

        $schedule->call(function () { 
            Storage::disk('s3')->deleteDirectory('summaries');
        })->everyMinute();
        
        // $schedule->call(function () { // lo hacemos de esta forma porque no esta enviando los email
        //     //url contra la que atacamos
        //     $ch = curl_init(config('app.url') . '/appointments/reminders');
        //     //a true, obtendremos una respuesta de la url, en otro caso,
        //     //true si es correcto, false si no lo es
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     //establecemos el verbo http que queremos utilizar para la petición
        //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        //     //enviamos el array data
        //     //curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        //     //obtenemos la respuesta
        //     $response = curl_exec($ch);
        //     // Se cierra el recurso CURL y se liberan los recursos del sistema
        //     curl_close($ch);
        //     \Log::info('se llamó appointment/reminders: ' . $response);
        // })->everyThirtyMinutes();

        /* $schedule->command(\App\Console\Commands\SendPolls::class) //hay q verificar si en el nuevo servidor si funciona asi
                  ->daily();*/

//        $schedule->call(function () { // lo hacemos de esta forma porque no esta enviando los email
//            //url contra la que atacamos
//            $ch = curl_init(config('app.url') . '/polls/send');
//            //a true, obtendremos una respuesta de la url, en otro caso,
//            //true si es correcto, false si no lo es
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            //establecemos el verbo http que queremos utilizar para la petición
//            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//            //enviamos el array data
//            //curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
//            //obtenemos la respuesta
//            $response = curl_exec($ch);
//            // Se cierra el recurso CURL y se liberan los recursos del sistema
//            curl_close($ch);
//        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
