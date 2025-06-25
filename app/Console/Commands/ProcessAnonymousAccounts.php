<?php

namespace App\Console\Commands;

use App\Actions\PrepareDataForUser;
use App\Jobs\CreateUserDownloadData;
use App\Patient;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ProcessAnonymousAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:processAnonymousAccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera información para cuentas anónimas zip y elimina a los 10 años las cuentas inactivas';

    /**
     * Execute the console command.
     */
    public function handle(PrepareDataForUser $prepareDataForUser): void
    {

        $userIdesToDelete = [];

        $jobs = User::query()
            ->where('active', 0)
            ->where('cancel_at', '<', now()->subYears(10)->startOfDay())
            ->lazy()
            ->map(function ($user) use (&$userIdesToDelete, $prepareDataForUser) {
                $userIdesToDelete[] = $user->ide;

                $dataToGenerate = $prepareDataForUser->handle($user, 'anonymous-users-data');

                return new CreateUserDownloadData($dataToGenerate, $sendNotification = false);
            })
            ->filter()
            ->toArray();

        if (!count($jobs)) {
            $this->info('There are no anonymous accounts to generate');
            return;
        }

        Bus::batch($jobs)->allowFailures()
            ->then(function ($batch) {
                \Log::info('All Anonymous data are created successfully!');

            })
            ->catch(function ($batch, $e) {
                \Log::error('We failed to create anonymous data some of the accounts!'.$e->getMessage());
            })
            ->finally(function ($batch) use ($userIdesToDelete) {
               self::deleteUsers($userIdesToDelete);
            })
            ->dispatch();


        $this->info('Mantenimiento Cuentas Anónimas Inactivas: '.count($userIdesToDelete).' cuentas inactivas anónimas se eliminaron');
    }

    protected static function deleteUsers(array $userIdesToDelete): void
    {

        DB::transaction(function () use ($userIdesToDelete) {
            Patient::whereIn('ide', $userIdesToDelete)->delete();
            User::whereIn('ide', $userIdesToDelete)->delete();
        });


    }
}
