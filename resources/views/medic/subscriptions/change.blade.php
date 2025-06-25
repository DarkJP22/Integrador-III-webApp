@extends('layouts.medics.app')
@section('header')
    <script type="text/javascript" src="{{ config('services.pasarela.url_vpos2') }}"></script>
@endsection
@section('content')

    <section class="content-header">
        <h1>Confirmar pago</h1>

    </section>
    <section class="invoice">
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <i class="fa fa-globe"></i> {{ config('app.name', 'Laravel') }}
                    <small class="pull-right">{{ \Carbon\Carbon::now()->toDateTimeString() }}    </small>
                </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-6">
                @if(isset($purchaseOperationNumber))
                    <b>Numero de operación:</b> {{ $purchaseOperationNumber }}
                    <br>
                    <b>Estado:</b>

                @endif

            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        @if(isset($newPlan) && $newPlan)
            <!-- Table row -->
            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Cant.</th>
                            <th>Description</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td>1</td>
                            <td>
                                {{ $newPlan->title }} <br>
                                @if(isset($office))
                                    <small>{{ $office->name }}</small>
                                @endif
                            </td>
                            <td>{{ money($newPlan->cost) }} / Mes</td>
                        </tr>
                        @foreach($invoiceItemsAppointments as $item)
                            <tr>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ money($item['total']) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-6">
                    {{-- <p class="lead">Metodos de pago:</p> --}}
                    {{-- <img src="/img/credit/visa.png" alt="Visa">
                    <img src="/img/credit/mastercard.png" alt="Mastercard">
                    <!-- <img src="/img/credit/american-express.png" alt="American Express"> -->

                    <p class="text-muted well well-sm no-shadow" style="margin-top: 10px; max-width:350px;">
                      <img src="/img/credit/banner_payme_latam.png" alt="Payme" style="width:100%">
                    </p> --}}
                </div>
                <!-- /.col -->
                <div class="col-xs-6">

                    @if(count($invoiceItemsAppointments))
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th style="width:50%">Subtotal:</th>
                                    <td>{{ money($amountSubtotal) }}</td>
                                </tr>
                                @if($discount)
                                    <tr>
                                        <th style="width:50%">Descuento:</th>
                                        <td>-{{ money($discount) }}</td>
                                    </tr>
                                @endif
                                <tr class="tw-bg-gray-100">
                                    <th class="tw-text-4xl">Total:</th>
                                    <td class="tw-text-4xl">{{ money($amountTotal) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- this row will not appear when printing -->
            <div class="row no-print">
                @include('medic.subscriptions._notesVoucher')
                @if($currentPlan->commission_by_appointment && $amountTotal > 0)
                    <h3>Subir Comprobante de pago</h3>
                    <form action="/medic/subscriptions/{{ $newPlan->id }}/change-voucher" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="voucher">
                        @if ($errors->has('voucher'))
                            <span class="help-block">
                        <strong>{{ $errors->first('voucher') }}</strong>
                    </span>
                        @endif
                        <input class="form-control" type="hidden" name="purchase_operation_number"
                               value="{{ $purchaseOperationNumber }}"/>


                        <div class="tw-mt-4">
                            <button class="btn btn-primary">Subir Comprobante</button>
                        </div>

                    </form>
                @else
                    <form action="/medic/subscriptions/{{ $newPlan->id }}/change" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <input class="form-control" type="hidden" name="purchase_operation_number"
                               value="{{ $purchaseOperationNumber }}"/>


                        <div class="tw-mt-4">
                            <button class="btn btn-primary">Cambiar</button>
                        </div>

                    </form>

                @endif

            </div>

        @endif
    </section>

@endsection
@push('scripts')
    <script>
        function printComprobante() {
            window.print();
        }

        termscheckboxprepare();

        function termscheckboxprepare() {

            if ($('#terms').is(':checked')) {
                $('.btn-blank').hide();
                $('.btn-VPOS').show();
            } else {
                $('.btn-blank').show();
                $('.btn-VPOS').hide();
            }
        }

        $('#terms').click(function (e) {
            termscheckboxprepare();
        })

        $('.btn-blank').click(function (e) {
            alert('Aceptar terminos y condiciones para continuar con el proceso de pago')
        });
    </script>
@endpush

