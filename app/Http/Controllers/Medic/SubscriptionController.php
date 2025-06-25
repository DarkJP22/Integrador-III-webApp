<?php

namespace App\Http\Controllers\Medic;

use App\Actions\CreateSubscriptionInvoice;
use App\Actions\GetAppointmentAndRequestsForCommission;
use App\Actions\SerialNumberFormatter;
use App\Appointment;
use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Enums\AppointmentStatus;
use App\Enums\SubscriptionInvoicePaidStatus;
use App\SubscriptionInvoice;
use App\Plan;
use App\Income;
use App\Http\Controllers\Controller;
use App\Notifications\PaymentConfirmation;
use App\Notifications\PaymentSubscriptionConfirmation;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }


        $plans = Plan::where('for_medic', 1)->get(); //centro medico


        return view('medic.subscriptions.index', compact('plans'));
    }

    /**
     * Guardar consulta(cita)
     */
    public function edit(Plan $plan, GetAppointmentAndRequestsForCommission $getAppointmentAndRequestsForCommission)
    {

        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }


        $user = auth()->user();

        if (!Plan::where('for_medic', 1)->get()->contains('id', $plan->id)) {
            return Redirect('/');
        }

        // if (!$user->expiredSubscription()->count() && $user->subscription->cost > 0) {
        //     flash('No puedes cambiar de subscripción hasta que finalices el periodo de la actual', 'danger');

        //     return redirect(route('medicChangeAccountType'));
        // };

        if ($user->subscription && $user->subscription->plan_id == $plan->id) {
            return Redirect('/');
        }


        $newPlan = $plan;
        $currentPlan = $user->subscription->plan;
        $amountTotal = $newPlan->cost;
        $description = $newPlan->title;
        $discount = 0;
        $amountSubtotal = 0;
        $purchaseOperationNumber = getUniqueNumber();
        $amount = fillZeroRightNumber($amountTotal);
        $purchaseCurrencyCode = config('services.pasarela.currency_code'); //env('CURRENCY_CODE');
        //$purchaseVerification = getPurchaseVerfication($purchaseOperationNumber, $amount, $purchaseCurrencyCode);

        $medic_name = $user->name;
        $medic_email = $user->email;

        $planBuyChange = 2; // 2 cambio de plan
        $subscription = $user->subscription;
        $previous_billing_date = $subscription->previous_billing_date ?? $subscription->created_at;
        $currentDate = Carbon::now();
        $invoiceItemsAppointments = [];

        if ($currentPlan->commission_by_appointment) {

            $appointmentAndRequestData = $getAppointmentAndRequestsForCommission->handle($user, $currentPlan, $previous_billing_date->startOfDay(), $currentDate->endOfDay());

            $invoiceItemsAppointments = [
                [
                    'name' => 'Citas en línea efectivas',
                    'description' => 'Cobro por citas en línea efectivas',
                    'discount_type' => 'fixed',
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'quantity' => $appointmentAndRequestData->scheduledAppointmentsQuantity,
                    'unit_name' => 'Unidad',
                    'discount' => 0,
                    'discount_val' => 0,
                    'tax' => 0,
                    'total' => $appointmentAndRequestData->scheduledAppointmentsQuantity * $appointmentAndRequestData->cost_by_appointment,
                ],
                [
                    'name' => 'Solicitudes de citas < '.$currentPlan->commission_discount_range_in_minutes.' min',
                    'description' => 'Cobro por solicitudes de citas confirmadas',
                    'discount_type' => 'fixed',
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'quantity' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity,
                    'unit_name' => 'Unidad',
                    'discount' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity ? $currentPlan->commission_discount : 0,
                    'discount_val' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity ? $appointmentAndRequestData->commission_discount_val : 0,
                    'tax' => 0,
                    'total' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity * $appointmentAndRequestData->cost_by_appointment_with_discount,
                ],
                [
                    'name' => 'Solicitudes de citas > '.$currentPlan->commission_discount_range_in_minutes.' min',
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

            $discount = $currentPlan->cost;
            $total = collect($invoiceItemsAppointments)->sum('total');
            $amountSubtotal = $total + $discount;
            $amountTotal += $total - $discount;

        }


        return view('medic.subscriptions.change')->with(compact('newPlan', 'currentPlan', 'purchaseOperationNumber',
            'amount', 'amountTotal', 'purchaseOperationNumber', 'purchaseCurrencyCode', 'medic_name', 'medic_email',
            'description', 'planBuyChange', 'invoiceItemsAppointments', 'discount', 'amountSubtotal'));
    }

    public function change(Plan $plan)
    {
        $this->validate(request(), [
            'purchase_operation_number' => ['required']
        ]);

        $subscription = auth()->user()->subscription()->first();

        if ($subscription->plan_id != $plan->id) {

            $subscription->plan_id = $plan->id;
            $subscription->cost = $plan->cost;
            $subscription->quantity = $plan->quantity;
            $subscription->ends_at = Carbon::now()->startOfMonth()->addMonths($plan->quantity);
            $subscription->purchase_operation_number = request('purchase_operation_number');
            $subscription->save();
        }


        flash('Cambio de subscripción efectuado correctamente', 'success');

        return redirect(route('medicChangeAccountType'));
    }

    public function changeVoucher(Plan $plan)
    {
        $this->validate(request(), [
            'voucher' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png'],
            'income' => ['sometimes'],
            'purchase_operation_number' => ['required']
        ]);

        DB::transaction(function () use ($plan) {
            // save the file in storage
            $path = request()->file('voucher')->store('subscription-invoices', 's3');

            if (!$path) {
                throw new Exception("The file could not be saved.", 500);
            }


            $subscription = auth()->user()->subscription()->first();
            $currentPlan = $subscription->plan;

            if ($subscription->plan_id != $plan->id) {


                $previous_billing_date = $subscription->previous_billing_date ?? $subscription->created_at;

                app(CreateSubscriptionInvoice::class)($subscription, $previous_billing_date->startOfDay(), now()->endOfDay(), data: [
                    'notes' => 'Cambio de subscripción: '.$plan->title,
                    'paid_status' => SubscriptionInvoicePaidStatus::CHECKING,
                    'reference_number' => request('purchase_operation_number'),
                    'comprobante' => $path,
                ], firstInvoiceItem: [
                    'discount' => 0,
                    'discount_val' => $currentPlan->cost,
                    'total' => 0,
                ]);


                $subscription->plan_id = $plan->id;
                $subscription->cost = $plan->cost;
                $subscription->quantity = $plan->quantity;
                $subscription->ends_at = Carbon::now()->startOfMonth()->addMonths($plan->quantity);
                $subscription->purchase_operation_number = request('purchase_operation_number');
                $subscription->save();


            }

            auth()->user()->fe = $plan->include_fe;
            auth()->user()->save();

            // informamos via email su confirmacion de pago de una compra
            if (auth()->user()->email) {
                try {

                    auth()->user()->notify(new PaymentSubscriptionConfirmation($plan,
                        request('purchase_operation_number')));
                } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                {
                    \Log::error($e->getMessage());
                }
            }

        });


        flash('Comprobante Subido Correctamente', 'success');

        return redirect(route('medicChangeAccountType'));
    }

    public function renewVoucher()
    {
        $this->validate(request(), [
            'voucher' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png'],
            'incomes' => ['required', 'array'],
            'purchase_operation_number' => ['required']
        ]);

        DB::transaction(function () {
            // save the file in storage
            $path = request()->file('voucher')->store('subscriptions', 's3');

            if (!$path) {
                throw new Exception("The file could not be saved.", 500);
            }

            $incomes = Income::whereIn('id', request('incomes'))->get();
            $description = $incomes->pluck('description')->implode(',');
            $total = $incomes->sum('amount');

            $subscription = auth()->user()->subscription()->first();
            $plan = Plan::find($subscription->plan_id);

            foreach ($incomes as $income) {
                if ($income->type == 'MS') {

                    $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                    $subscription->purchase_operation_number = request('purchase_operation_number');
                    $subscription->save();
                }
                $income->paid = 1;
                $income->purchase_operation_number = request('purchase_operation_number');
                $income->voucher = $path;
                $income->save();
            }

            // informamos via email su confirmacion de pago
            if (auth()->user()->email) {
                try {

                    auth()->user()->notify(new PaymentConfirmation($incomes, $description,
                        request('purchase_operation_number'), $total));
                } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                {
                    \Log::error($e->getMessage());
                }
            }
        });


        flash('Comprobante Subido Correctamente', 'success');

        return redirect()->back();
    }

    public function changeToFreeSubscription(Plan $plan)
    {
        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }

        $user = auth()->user();

        if (!$plan->cost > 0) { // si es gratis o coste 0

            $income = Income::where('user_id', auth()->id())->where(function ($query) {
                $query->Where('type', 'MS'); // por subscripcion de paquete
            })->where('paid', 0)->update(['paid' => 1, 'purchase_operation_number' => 'free']);


            $purchaseOperationNumber = $plan->cost > 0 ? '---' : 'free';

            if ($user->subscription) {
                $user->subscription->plan_id = $plan->id;
                $user->subscription->cost = $plan->cost;
                $user->subscription->quantity = $plan->quantity;
                $user->subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                $user->subscription->purchase_operation_number = $purchaseOperationNumber;
                $user->subscription->save();
            } else {

                $user->subscription()->create([
                    'plan_id' => $plan->id,
                    'cost' => $plan->cost,
                    'quantity' => $plan->quantity,
                    'ends_at' => Carbon::now()->addMonths($plan->quantity),
                    'purchase_operation_number' => $purchaseOperationNumber
                ]);
            }

            $user->fe = $plan->include_fe;
            $user->save();

            // informamos via email su confirmacion de pago de una compra
            if (auth()->user()->email) {
                try {

                    auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
                } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                {
                    \Log::error($e->getMessage());
                }
            }

            flash('Cambio de subscripción realizado correctamente', 'success');
        }

        return back(); //redirect('/');
    }

    /**
     * Guardar consulta(cita)
     */
    public function create(Plan $plan)
    {
        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }

        $user = auth()->user();

        //if ($user->subscription && $user->subscription->plan_id == $plan->id) return Redirect('/');
        if ($user->subscription) {
            return Redirect('/');
        }

        if (!Plan::where('for_medic', 1)->get()->contains('id', $plan->id)) {
            return Redirect('/');
        }

        // if(request('office')){
        //     $office = auth()->user()->offices()->where('offices.type', 1)->where('offices.id', request('office'))->first();

        //     if (!$office) return Redirect('/');
        // }

        $newPlan = $plan;

        $amountTotal = $newPlan->cost;
        $description = $newPlan->title;

        $purchaseOperationNumber = getUniqueNumber();
        $amount = fillZeroRightNumber($amountTotal);
        $purchaseCurrencyCode = config('services.pasarela.currency_code'); //env('CURRENCY_CODE');
        $purchaseVerification = getPurchaseVerfication($purchaseOperationNumber, $amount, $purchaseCurrencyCode);

        $medic_name = $user->name;
        $medic_email = $user->email;

        $planBuyChange = 1; // 1 compra de plan


        // $user->fe = $newPlan->include_fe;
        // $user->save();


        return view('medic.subscriptions.buy')->with(compact('newPlan', 'purchaseOperationNumber', 'amount',
            'amountTotal', 'purchaseOperationNumber', 'purchaseCurrencyCode', 'purchaseVerification', 'medic_name',
            'medic_email', 'description', 'planBuyChange'));
    }
}
