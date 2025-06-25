<?php

namespace App\Jobs;

use App\Actions\CreateSubscriptionInvoice;
use App\Actions\SerialNumberFormatter;
use App\Appointment;
use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Enums\AppointmentStatus;
use App\Enums\SubscriptionInvoicePaidStatus;
use App\Notifications\SubscriptionInvoiceGenerated;
use App\SubscriptionInvoice;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UserBillingJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(protected User $user)
    {
    }

    public function handle(): void
    {

        $subscription = $this->user->subscription;
        $plan = $subscription->plan;
        $previous_billing_date = $subscription->previous_billing_date ?? $subscription->created_at;

        if ($plan) {
            $subscriptionInvoice = app(CreateSubscriptionInvoice::class)($subscription, $previous_billing_date, now()->startOfDay());

            $this->user->notify(new SubscriptionInvoiceGenerated($subscriptionInvoice));

            if ($this->user->push_token) {
                $title = 'Nueva factura de cobro por subscripción';
                $message = 'Generada el '.$subscriptionInvoice->invoice_date;
                $extraData = [
                    'type' => 'subscription-invoice',
                    'title' => $title,
                    'body' => $message,
                    'url' => '/medic/subscription/invoices',

                ];

                SendAppNotificationJob::dispatch($title, $message, [$this->user->push_token], $extraData)->afterCommit();

            }

        }


    }
}
