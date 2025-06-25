@extends('layouts.admins.app')

@section('content')
     <section class="content-header">
      <h1>Subscripciones</h1>
    
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
            <subscription-invoices :invoice-paid-statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::optionsAsConst()) }}" :statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::options()) }}" endpoint="/admin/subscription/invoices"></subscription-invoices>
            </div>
        </div>

    </section>

@endsection
@push('scripts')

@endpush
