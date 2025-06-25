<?php

namespace App\Console\Commands;

use App\Actions\SerialNumberFormatter;
use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Enums\SubscriptionInvoicePaidStatus;
use App\Invoice;
use App\Jobs\UserBillingJob;
use App\Mail\BillingAccountsFailedMail;
use App\Mail\BillingAccountsSuccessMail;
use App\SubscriptionInvoice;
use Illuminate\Console\Command;
use App\Repositories\IncomeRepository;
use Carbon\Carbon;
use App\Plan;
use App\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Collection;

class SubscriptionCharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:subscriptionCharge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cobro de subscripción';

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

        $jobs = User::query()
            ->where('active', 1)
            ->with('subscription.plan')
            ->whereHas('subscription', function ($query) {
                $query->whereDate('ends_at', today())
                    ->where('cost', '>', 0);
            })
            ->lazy()
            ->map(function ($user) {
                return new UserBillingJob($user);
            });

        if (!$jobs->count()) {
            $this->info('There are no users to generate');
            return;
        }

        $administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();

        Bus::batch($jobs)->allowFailures()
            ->then(function ($batch) use ($administrators) {
                \Log::info('All billing medics are created successfully!');

                //$user = User::find(1);
                foreach ($administrators as $administrator) {
                    Mail::to($administrator)->send(
                        new BillingAccountsSuccessMail()
                    );
                }
            })
            ->catch(function ($batch, $e) use ($administrators) {
                \Log::error('We failed to create invoices some of the accounts!'.$e->getMessage());
                foreach ($administrators as $administrator) {
                    Mail::to($administrator)->send(
                        new BillingAccountsFailedMail()
                    );
                }
            })
            ->finally(function ($batch) {
                \Log::info('Billing process completed!');
                //$user = User::find(1);
                // Mail::to($user)->send(
                // 'Billing process completed!'
                // );
            })
            ->onQueue('billing')
            ->dispatch();

        $this->info('Jobs UserBilling Despachados');

    }
}
