<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionInvoicePaidStatus;
use App\User;
use Illuminate\Console\Command;

class MaintenanceAccountsWithOverdueInvoicesCommand extends Command
{
    protected $signature = 'gps:maintenance-accounts-overdue-invoices';

    protected $description = 'Mantenimiento Cuentas Con Facturas Vencidas';

    public function handle(): void
    {
        User::query()
            ->where('active', 1)
            ->where('disabled_by_payment', 0)
            ->whereHas('subscriptionInvoices', function ($query) {
                $query->where('due_date', '<', today())
                    ->where(function ($query) {
                        $query->where('paid_status', SubscriptionInvoicePaidStatus::REFUSED)
                            ->orWhere('paid_status', SubscriptionInvoicePaidStatus::UNPAID);
                    });
            })
            ->eachById(function ($user) {
                $user->update(['disabled_by_payment' => 1, 'api_token' => null]);
            });

        $this->info('Mantenimiento Cuentas Con Facturas Vencidas');
    }
}
