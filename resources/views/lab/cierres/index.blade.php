@extends('layouts.laboratories.app')
@section('header')
@endsection
@section('content')
    <section class="content-header">
        <h1>Cierre del dia</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">

                        <form method="POST" id="form-cierre" data-confirm="Estas Seguro?" action="/lab/cierres">
                            {{ csrf_field() }}
                            <div class="form-group">



                                <div class="col-sm-3">
                                    <select name="office" id="office" class="form-control">

                                        @foreach (auth()->user()->offices as $userClinic)
                                            <option value="{{ $userClinic->id }}" {{ isset($search) && $search['office'] == $userClinic->id ? 'selected' : '' }}>{{ $userClinic->name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                                <div class="col-sm-3">
                                    <select name="CodigoActividad" id="CodigoActividad" class="form-control" required>
                                        <option value="">-- Filtro por Actividad Económica --</option>
                                        @foreach ($activities as $activity)
                                            <option value="{{ $activity->codigo }}" {{ isset($search) && $search['CodigoActividad'] == $activity->codigo ? 'selected' : '' }}>{{ $activity->actividad }}</option>
                                        @endforeach

                                    </select>

                                </div>
                                <div class="col-sm-3 flatpickr time">


                                    <input data-input type="text" name="to" class="date form-control" placeholder="Fecha Hasta" value="{{ $search['to'] }}">



                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-primary" form="form-cierre" formaction="/lab/cierres">Generar Nuevo Cierre</button>
                                </div>


                            </div>

                        </form>

                        <div class="box-tools">
                        </div>


                    </div>
                    <div class="box-body">
                    </div>
                </div>

                <div class="box">

                    <div class="box-header">
                        <div class="filters">

                            <form action="/lab/cierres" method="GET" autocomplete="off">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5>Consultar</h5>
                                    </div>


                                    <div class="col-sm-2 flatpickr time">

                                        <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">



                                    </div>
                                    <div class="col-sm-2 flatpickr time">

                                        <input data-input type="text" name="end" class="date form-control" placeholder="Fecha Hasta" value="{{ $search['end'] }}">

                                    </div>
                                    <div class="col-sm-3">
                                        <select name="CodigoActividad" id="CodigoActividad" class="form-control" required>
                                            <option value="">-- Filtro por Actividad Económica --</option>
                                            @foreach ($activities as $activity)
                                                <option value="{{ $activity->codigo }}" {{ isset($search) && $search['CodigoActividad'] == $activity->codigo ? 'selected' : '' }}>{{ $activity->actividad }}</option>
                                            @endforeach

                                        </select>

                                    </div>
                                    <div class="col-sm-2">
                                        <select name="archived" id="archived" class="form-control">
                                            <option value="">-- Estado --</option>

                                            <option value="1" {{ isset($search) && $search['archived'] == 1 ? 'selected' : '' }}>Archivado</option>


                                        </select>

                                    </div>
                                    <div class="col-sm-3">

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-secondary">Buscar</button>
                                            <button type="button" class="btn btn-secondary btn-print">Imprimir</button>
                                            <input type="hidden" name="print" value="0">
                                        </div>

                                    </div>


                                </div>
                            </form>

                        </div>
                    </div>


                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table class="table no-margin">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Desde</th>
                                    <th scope="col">Hasta</th>
                                    <th scope="col">Médico / Admin</th>
                                    <th scope="col">Credito</th>
                                    <th scope="col">Contado</th>
                                    <th scope="col">Efectivo</th>
                                    <th scope="col">Tarjeta</th>
                                    <th scope="col">Cheque</th>
                                    <th scope="col">Transf</th>
                                    <th scope="col">IVA Dev.</th>
                                    {{-- <th scope="col">Clínica</th>
                            <th scope="col">Lab</th> --}}
                                    <th scope="col">Gravado</th>
                                    <th scope="col">Exento</th>
                                    <th scope="col">Exonerado</th>
                                    <th scope="col">Descuentos</th>
                                    <th scope="col">Impuestos</th>
                                    <th scope="col">Ventas</th>
                                    <th scope="col">Generado</th>
                                    <th scope="col"></th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cierres as $cierre)
                                    <tr>
                                        <th scope="row">{{ $cierre->id }}</th>
                                        <td><small>{{ $cierre->from }}</small></td>
                                        <td><small>{{ $cierre->to }}</small></td>
                                        <td>{{ Optional($cierre->user)->name }}</td>
                                        <td>{{ money($cierre->TotalCredito, '') }} <small>CRC</small> <br> {{ money($cierre->TotalCreditoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalContado, '') }} <small>CRC</small> <br> {{ money($cierre->TotalContadoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalEfectivo, '') }} <small>CRC</small> <br> {{ money($cierre->TotalEfectivoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalTarjeta, '') }} <small>CRC</small> <br> {{ money($cierre->TotalTarjetaDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalCheque, '') }} <small>CRC</small> <br> {{ money($cierre->TotalChequeDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalDeposito, '') }} <small>CRC</small> <br> {{ money($cierre->TotalDepositoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalIVADevuelto, '') }} <small>CRC</small> <br> {{ money($cierre->TotalIVADevueltoDolar, '') }} <small>USD</small></td>
                                        {{-- <td>{{ money($cierre->TotalClinica, '') }} <small>CRC</small> <br> {{ money($cierre->TotalClinicaDolar, '') }} <small>USD</small></td>
                            <td>{{ money($cierre->TotalLaboratorio, '') }} <small>CRC</small> <br> {{ money($cierre->TotalLaboratorioDolar, '') }} <small>USD</small></td> --}}
                                        <td>{{ money($cierre->TotalGravado, '') }} <small>CRC</small> <br> {{ money($cierre->TotalGravadoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalExento, '') }} <small>CRC</small> <br> {{ money($cierre->TotalExentoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalExonerado, '') }} <small>CRC</small> <br> {{ money($cierre->TotalExoneradoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalDescuento, '') }} <small>CRC</small> <br> {{ money($cierre->TotalDescuentoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalImpuesto, '') }} <small>CRC</small> <br> {{ money($cierre->TotalImpuestoDolar, '') }} <small>USD</small></td>
                                        <td>{{ money($cierre->TotalVentas, '') }} <small>CRC</small> <br> {{ money($cierre->TotalVentasDolar, '') }} <small>USD</small></td>
                                        <td><small>{{ $cierre->created_at }}</small></td>
                                        <td>
                                            @can('update', $cierre)
                                                <form method="POST" action="{{ url('/lab/cierres/' . $cierre->id) }}" data-confirm="Estas Seguro?">
                                                    @csrf
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="btn btn-danger btn-sm waves-effect">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>

                                    </tr>
                                @endforeach


                            </tbody>
                            <tfoot>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Desde</th>
                                    <th scope="col">Hasta</th>
                                    <th scope="col">Médico / Admin</th>
                                    <th scope="col">Credito</th>
                                    <th scope="col">Contado</th>
                                    <th scope="col">Efectivo</th>
                                    <th scope="col">Tarjeta</th>
                                    <th scope="col">Cheque</th>
                                    <th scope="col">Transf</th>
                                    <th scope="col">IVA Dev.</th>
                                    {{-- <th scope="col">Clínica</th>
                            <th scope="col">Lab</th> --}}
                                    <th scope="col">Gravado</th>
                                    <th scope="col">Exento</th>
                                    <th scope="col">Exonerado</th>
                                    <th scope="col">Descuentos</th>
                                    <th scope="col">Impuestos</th>
                                    <th scope="col">Ventas</th>
                                    <th scope="col">Generado</th>
                                    <th scope="col"></th>

                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>{{ money($totales['TotalCredito'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalCreditoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalContado'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalContadoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalEfectivo'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalEfectivoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalTarjeta'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalTarjetaDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalCheque'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalChequeDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalDeposito'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalDepositoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalIVADevuelto'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalIVADevueltoDolar'], '') }} <small>USD</small></b></td>
                                    {{-- <td><b>{{ money($totales['TotalClinica'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalClinicaDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalLaboratorio'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalLaboratorioDolar'], '') }} <small>USD</small></b></td> --}}
                                    <td><b>{{ money($totales['TotalGravado'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalGravadoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalExento'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalExentoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalExonerado'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalExoneradoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalDescuento'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalDescuentoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalImpuesto'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalImpuestoDolar'], '') }} <small>USD</small></b></td>
                                    <td><b>{{ money($totales['TotalVentas'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalVentasDolar'], '', 0) }} <small>USD</small></b></td>
                                    <td></td>
                                    <td>

                                    </td>

                                </tr>
                                @if (count($cierres))
                                    <tr>
                                        <td colspan="13" class="pagination-container">{!! $cierres->appends(['office' => $search['office'], 'user' => $search['user'], 'start' => $search['start'], 'end' => $search['end'], 'to' => $search['to'], 'archived' => $search['archived']])->render() !!}</td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>

                        <div class="row">
                            <div class="col-md-6">
                                <h3>Reporte Ventas</h3>
                                <ul>
                                    @php($TotalVentas = 0)
                                    @php($TotalVentasUSD = 0)
                                    @foreach ($impuestosVentasCompras['impuestosVentas'] as $impuestoVenta)
                                        @php($TotalVentas += $impuestoVenta->CodigoMoneda == 'CRC' ? $impuestoVenta->TotalVentas - $impuestoVenta->TotalIVADevuelto : 0)
                                        @php($TotalVentasUSD += $impuestoVenta->CodigoMoneda == 'USD' ? $impuestoVenta->TotalVentas - $impuestoVenta->TotalIVADevuelto : 0)
                                        <li><b>{{ trans('utils.codigo_tarifa_name.' . $impuestoVenta->codTarifa) }} {{ $impuestoVenta->CodigoMoneda }}</b>
                                            <ul>
                                                <li>
                                                    Total Impuestos: {{ $impuestoVenta->codTarifa != '00' && $impuestoVenta->codTarifa != '01' ? money($impuestoVenta->TotalImpuesto - $impuestoVenta->TotalIVADevuelto, '', 5) : money($impuestoVenta->TotalImpuesto, '', 5) }} <small>{{ $impuestoVenta->CodigoMoneda }}</small>
                                                </li>
                                                <li>
                                                    {{ $impuestoVenta->codTarifa != '00' ? 'Total Gravado' : 'Total Excento' }} ({{ money($impuestoVenta->TotalGravadoDesc, '', 5) }}): {{ $impuestoVenta->codTarifa != '00' && $impuestoVenta->codTarifa != '01' ? money($impuestoVenta->TotalVentas - $impuestoVenta->TotalIVADevuelto, '', 5) : money($impuestoVenta->TotalVentas, '', 5) }} <small>{{ $impuestoVenta->CodigoMoneda }}</small>


                                                </li>
                                                <li>
                                                    IVA Devuelto: {{ money($impuestoVenta->TotalIVADevuelto, '', 5) }}
                                                </li>


                                            </ul>

                                        </li>
                                    @endforeach
                                    <li>
                                        <b>Total Ventas CRC: {{ money($TotalVentas, '', 5) }}</b><br>
                                        <b>Total Ventas USD: {{ money($TotalVentasUSD, '', 5) }}</b>
                                    </li>

                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h3>Reporte Ventas en caja</h3>

                                <li>
                                    <b>Total Ventas CRC: {{ money($totales['TotalVentas'], '', 2) }}</b>

                                </li>
                                <li>
                                    <b>Total CxC CRC: -({{ money($totales['TotalCxc'], '', 2) }})</b>

                                </li>
                                <li>

                                    <b>Subtotal CRC: {{ money($totales['SubtotalVentas'], '', 2) }}</b>

                                </li>
                                <li>

                                    <b>Total Abonos CRC: {{ money($totales['TotalAbonos'], '', 2) }}</b>

                                </li>
                                <li>

                                    <b>Total En Caja CRC: {{ money($totales['TotalVentasNeta'], '', 2) }}</b>
                                </li>
                                </ul>
                            </div>
                        </div>
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
            //   $('.date').datetimepicker({
            //       format:'YYYY-MM-DD HH:mm:ss',
            //       locale: 'es',

            //    });

            $('.btn-print').on('click', function(e) {

                $('input[name="print"]').val(1);

                $(this).parents('form').submit();
            })
        });
    </script>
@endpush
