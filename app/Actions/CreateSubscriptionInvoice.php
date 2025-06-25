<?php

namespace App\Actions;

use App\Appointment;
use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Enums\AppointmentStatus;
use App\Enums\SubscriptionInvoicePaidStatus;
use App\Setting;
use App\Subscription;
use App\SubscriptionInvoice;
use Carbon\Carbon;

class CreateSubscriptionInvoice
{

    public function __invoke(
        Subscription $subscription,
        Carbon $billing_date_from,
        Carbon $billing_date_to,
        array $data = [],
        array $firstInvoiceItem = []
    ): SubscriptionInvoice {
//        $currentDate = Carbon::now()->setTime(0, 0, 0);
//        $previous_billing_date = $subscription->previous_billing_date ?? $subscription->created_at;

        $user = $subscription->user;
        $plan = $subscription->plan;
        $subscriptionMonthsFree = (int) Setting::getSetting('subscription_months_free') ?? 0;

        $invoice = SubscriptionInvoice::create([
            'invoice_date' => Carbon::now()->toDateString(),
            'due_date' => Carbon::now()->addDays(7)->toDateString(),
            'invoice_number' => 'INV-'.$user->id.'-'.Carbon::now()->format('Ymd'),
            'paid_status' => SubscriptionInvoicePaidStatus::UNPAID,
            'notes' => $billing_date_from->format('d/m/Y').' al '.$billing_date_to->format('d/m/Y'),
            'discount' => 0,
            'discount_val' => 0,
            'sub_total' => $subscription->cost,
            'total' => $subscription->cost,
            'tax' => 0,
            'currency_id' => $plan->currency_id,
            'customer_id' => $user->id,
            ...$data
        ]);

        $limitDiscountDate = $subscription->created_at->copy()->addMonths($subscriptionMonthsFree);
        $invoiceItemDiscountVal = 0;

        if($plan->commission_by_appointment){ // solo si es un plan detectable(commission by appointment)
            $invoiceItemDiscountVal = ($subscriptionMonthsFree && now()->lte($limitDiscountDate)) ? $subscription->cost : 0;
        }

        $invoiceItem = [
            'name' => 'Uso de Plataforma',
            'description' => $plan->title,
            'discount_type' => 'fixed',
            'price' => $subscription->cost,
            'quantity' => 1,
            'unit_name' => 'Unidad',
            'discount' => 0,
            'discount_val' => $invoiceItemDiscountVal,
            'tax' => 0,
            'total' => $subscription->cost - $invoiceItemDiscountVal,
            ...$firstInvoiceItem
        ];

        $invoiceItemsAppointments = [];

        if ($plan->commission_by_appointment) {

            $appointmentAndRequestData = app(GetAppointmentAndRequestsForCommission::class)->handle($user, $plan, $billing_date_from, $billing_date_to);

            $invoiceItemsAppointments = [
                [
                    'name' => 'Citas en línea efectivas',
                    'description' => 'Cobro por citas en línea efectivas',
                    'discount_type' => 'fixed',
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'quantity' => $appointmentAndRequestData->scheduledAppointmentsQuantity,
                    'unit_name' => 'Unidad',
                    'discount' => $appointmentAndRequestData->scheduledAppointmentsQuantity ? $plan->commission_discount : 0,
                    'discount_val' => $appointmentAndRequestData->scheduledAppointmentsQuantity ? $appointmentAndRequestData->commission_discount_val : 0,
                    'tax' => 0,
                    'total' => $appointmentAndRequestData->scheduledAppointmentsQuantity * $appointmentAndRequestData->cost_by_appointment_with_discount,
                ],
                [
                    'name' => 'Solicitudes de citas < '.$plan->commission_discount_range_in_minutes.' min',
                    'description' => 'Cobro por solicitudes de citas confirmadas',
                    'discount_type' => 'fixed',
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'quantity' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity,
                    'unit_name' => 'Unidad',
                    'discount' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity ? $plan->commission_discount : 0,
                    'discount_val' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity ? $appointmentAndRequestData->commission_discount_val : 0,
                    'tax' => 0,
                    'total' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity * $appointmentAndRequestData->cost_by_appointment_with_discount,
                ],
                [
                    'name' => 'Solicitudes de citas > '.$plan->commission_discount_range_in_minutes.' min',
                    'description' => 'Cobro por solicitudes de citas confirmadas',
                    'discount_type' => 'fixed',
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'quantity' => $appointmentAndRequestData->scheduledAppointmentRequestsOutRangeQuantity,
                    'unit_name' => 'Unidad',
                    'discount' => 0,
                    'discount_val' => 0,
                    'tax' => 0,
                    'total' => $appointmentAndRequestData->scheduledAppointmentRequestsOutRangeQuantity * $appointmentAndRequestData->cost_by_appointment,
                ],
                [
                    'name' => 'Solicitudes de citas no agendadas',
                    'description' => 'Cobro por solicitudes de citas no agendadas',
                    'discount_type' => 'fixed',
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'quantity' => $appointmentAndRequestData->pendingAppointmentRequestsQuantity,
                    'unit_name' => 'Unidad',
                    'discount' => 0,
                    'discount_val' => 0,
                    'tax' => 0,
                    'total' => $appointmentAndRequestData->pendingAppointmentRequestsQuantity * $appointmentAndRequestData->cost_by_appointment,
                ],


            ];

        }

        SubscriptionInvoice::createItems($invoice, [$invoiceItem, ...$invoiceItemsAppointments]);

        $invoiceDiscountVal = $invoice->items()->sum('discount_val');
        $invoiceTotal = $invoice->items()->sum('total');

        $invoice->fill([
            'discount_val' => $invoiceDiscountVal,
            'sub_total' => $invoiceTotal + $invoiceDiscountVal,
            'total' => $invoiceTotal,
            'paid_status' => $invoiceTotal > 0 ? $invoice->paid_status : SubscriptionInvoicePaidStatus::PAID,
            'paid_at' => $invoiceTotal > 0 ? null : now(),
        ]);

        $serial = (new SerialNumberFormatter())
            ->setModel($invoice)
            ->setCustomer($invoice->customer_id)
            ->setNextNumbers();

        $invoice->sequence_number = $serial->nextSequenceNumber;
        $invoice->customer_sequence_number = $serial->nextCustomerSequenceNumber;
        $invoice->invoice_number = $serial->getNextNumber();
        $invoice->save();

        $subscription->update([
            'previous_billing_date' => today(),
            'ends_at' => Carbon::now()->addMonths($plan->quantity),
        ]);

        return $invoice;
    }

}