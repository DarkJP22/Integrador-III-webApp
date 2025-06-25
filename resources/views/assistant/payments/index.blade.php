@extends('layouts.assistants.app')
@section('header')
    <link rel="stylesheet" href="/vendor/tooltipster.bundle.min.css">
@endsection
@section('content')
    <section class="content-header">
        <h1>Historial de Abonos</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <form action="/assistant/cxc/payments" method="GET" autocomplete="off">
                            <div class="form-group">

                                <div class="col-sm-2">
                                    <div class="input-group input-group-sm">


                                        <input type="text" name="q" class="form-control" placeholder="Cliente..." value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>


                                    </div>

                                </div>

                                <div class="col-sm-2 flatpickr">


                                    <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">





                                </div>
                                <div class="col-sm-2 flatpickr">


                                    <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">


                                </div>
                                <div class="col-sm-2">
                                    <select name="CodigoActividad" id="CodigoActividad" class="form-control" required>
                                        <option value="">-- Filtro por Actividad Económica --</option>
                                        @foreach ($activities as $activity)
                                            <option value="{{ $activity->codigo }}" {{ isset($search) && $search['CodigoActividad'] == $activity->codigo ? 'selected' : '' }}>{{ $activity->actividad }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                        </form>
                        <div class="box-tools">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Conse</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Total</th>


                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr>

                                        <td>
                                            <a href="/invoices/{{ $payment->invoice?->id }}">{{ $payment->invoice?->NumeroConsecutivo }}</a>
                                        </td>

                                        <td>{{ $payment->date }}</td>
                                        <td>
                                            <a href="/invoices/{{ $payment->invoice?->id }}">{{ $payment->invoice?->cliente }}</a> <br>
                                            <a href="/invoices/{{ $payment->invoice?->id }}"><small>{{ $payment->invoice?->identificacion_cliente }}</a>
                                        </td>

                                        <td>
                                            {{ money($payment->amount, '') }}
                                            <small>{{ $payment->invoice?->CodigoMoneda }}</small>
                                        </td>

                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3"></td>
                                    <td>
                                        <b>Total:</b> {{ money($paymentsTotals, '') }}
                                    </td>
                                </tr>
                                @if ($payments)
                                    <tr>
                                        <td colspan="4" class="pagination-container">{!! $payments->appends(['q' => $search['q'], 'start' => $search['start'], 'end' => $search['end'], 'CodigoActividad' => $search['CodigoActividad']])->render() !!}</td>
                                    </tr>
                                @endif


                            </tbody>

                        </table>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>
@endsection
