@extends('layouts.clinics.app')
@section('header')
    <style>
        @media print {
            .box-header {
                display: none !important;
            }
        }
    </style>
@endsection
@section('content')
    <section class="content-header">
        <h1>Facturas del dia por médico</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <form action="/clinic/balance" method="GET" autocomplete="off">
                            <div class="form-group">

                                <div class="col-sm-3">
                                    <select name="CodigoActividad" id="CodigoActividad" class="form-control" required>
                                        <option value="">-- Filtro por Actividad Económica --</option>
                                        @foreach ($activities as $activity)
                                            <option value="{{ $activity->codigo }}" {{ isset($search) && $search['CodigoActividad'] == $activity->codigo ? 'selected' : '' }}>{{ $activity->actividad }}</option>
                                        @endforeach

                                    </select>

                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group input-group-sm flatpickr">

                                        <input data-input type="text" name="date" class="date form-control" placeholder="Fecha" value="{{ $search['date'] }}">

                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <a href="#" class="btn btn-secondary btn-sm" onclick="printSummary();"><i class="fa fa-print"></i></a>
                                </div>


                            </div>

                        </form>



                        <div class="box-tools">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        @if ($search['CodigoActividad'])
                            <h4>Codigo Actividad Económica {{ $search['CodigoActividad'] }}</h4>
                        @endif
                        <table class="table no-margin">
                            <thead>
                                <tr>
                                    <th>Médico</th>
                                    <th>Facturas Finalizadas</th>
                                    <th>Total Facturado CRC</th>
                                    <th>Total Facturado USD</th>
                                    <th>Facturas Pendientes</th>
                                    <th>Total No Facturado CRC</th>
                                    <th>Total No Facturado USD</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statisticsInvoices['medics'] as $medic)
                                    <tr>
                                        <td>{{ $medic->name }}</td>
                                        <td>{{ $medic->finishedInvoices }}</td>
                                        <td>{{ money($medic->invoicesTotalMedicCRC, '') }}</td>
                                        <td>{{ money($medic->invoicesTotalMedicUSD, '') }}</td>
                                        <td>{{ $medic->pendingInvoices }}</td>
                                        <td>{{ money($medic->invoicesTotalPendingMedicCRC, '') }}</td>
                                        <td>{{ money($medic->invoicesTotalPendingMedicUSD, '') }}</td>

                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>Totales</b></td>
                                    <td>{{ $statisticsInvoices['totalAppointments'] }}</td>
                                    <td><b>{{ money($statisticsInvoices['totalInvoicesCRC'], '') }} CRC</b></td>
                                    <td><b>{{ money($statisticsInvoices['totalInvoicesUSD'], '') }} USD</b></td>
                                    <td>{{ $statisticsInvoices['totalPending'] }}</td>
                                    <td><b>{{ money($statisticsInvoices['totalInvoicesPendingCRC'], '') }} CRC</b></td>
                                    <td><b>{{ money($statisticsInvoices['totalInvoicesPendingUSD'], '') }} USD</b></td>


                                </tr>
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
@push('scripts')
    <script>
        function printSummary() {
            window.print();
        }
    </script>
@endpush
