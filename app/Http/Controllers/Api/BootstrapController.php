<?php

namespace App\Http\Controllers\Api;

use App\Plan;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Office;

class BootstrapController extends Controller
{

    public function __invoke(Request $request)
    {

        return response()->json([

            'config' => config('gpsmedica'),
            'plans' => Plan::all(),
            'subscription_invoice_paid_statuses' => \App\Enums\SubscriptionInvoicePaidStatus::options(),
            'subscription_invoice_paid_statuses_as_const' => \App\Enums\SubscriptionInvoicePaidStatus::optionsAsConst(),
            'types_of_health_professional' => \App\Enums\TypeOfHealthProfessional::options(),
            'types_of_health_professional_as_const' => \App\Enums\TypeOfHealthProfessional::optionsAsConst(),
            ...Setting::getAllSettings()


        ]);
    }
}
