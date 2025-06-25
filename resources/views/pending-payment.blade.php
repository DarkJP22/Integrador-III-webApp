@extends('layouts.login-payment')

@section('content')
    <section class="content container tw-bg-white">

        <div class="notification-app alert-danger tw-py-4">

            Tienes facturas pendientes de pago, cancela para poder tener acceso a la plataforma.

        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <subscription-invoices :invoice-paid-statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::optionsAsConst()) }}"
                                       :statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::options()) }}"></subscription-invoices>

            </div>

        </div>
    </section>
@endsection
