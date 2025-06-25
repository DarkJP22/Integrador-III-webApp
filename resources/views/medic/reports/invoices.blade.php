@extends('layouts.medics.app')

@section('content')
    <section class="content-header">
        <h1>Reporte de Facturas</h1>

    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        @include('agenda._buttons')

                    </div>

                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">


                        <form action="/reports/invoices" method="GET" autocomplete="off">
                            <div class="form-group">


                                <div class="col-sm-3 flatpickr">


                                    <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">





                                </div>
                                <div class="col-sm-3 flatpickr">


                                    <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">


                                </div>
                                <div class="col-sm-3">
                                    <select name="office" id="office" class="form-control">
                                        <option value="">-- Filtro por consultorio --</option>
                                        @foreach ($offices as $userClinic)
                                            <option value="{{ $userClinic->id }}" {{ isset($search) && $search['office'] == $userClinic->id ? 'selected' : '' }}>{{ $userClinic->name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-primary">Generar</button>
                                    <button type="button" class="btn btn-secondary btn-print">Imprimir</button>
                                    <input type="hidden" name="print" value="0">
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
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->NumeroConsecutivo ? $invoice->NumeroConsecutivo : $invoice->consecutivo }}</td>

                                        <td>
                                            {{ $invoice->created_at }}
                                        </td>

                                        <td data-title="Cliente">
                                            {{ $invoice->cliente }}
                                        </td>

                                        <td data-title="Total">{{ money($invoice->TotalComprobante, '') }} {{ $invoice->CodigoMoneda }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td colspan="3"></td>
                                    <td>
                                        <b>Total CRC: {{ money($totalVentasCRC, '') }} </b><br>
                                        <b>Total USD: {{ money($totalVentasUSD, '') }}</b><br>
                                    </td>
                                </tr>

                                @if ($invoices)
                                    <td colspan="6" class="pagination-container">{!! $invoices->appends(['start' => $search['start'], 'end' => $search['end']])->render() !!}</td>
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
@push('scripts')
    <script>
        $(function() {
            $('#office').on('change', function(e) {


                $(this).parents('form').submit();

            });

            $('.btn-print').on('click', function(e) {

                $('input[name="print"]').val(1);

                $(this).parents('form').submit();
            })
        });
    </script>
@endpush
