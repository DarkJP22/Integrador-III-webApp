@extends('layouts.assistants.app')
@section('header')
    <link rel="stylesheet" href="/vendor/tooltipster.bundle.min.css">
@endsection
@section('content')
    <section class="content-header">
        <h1>Cuentas por Cobrar</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <form action="/assistant/cxc" method="GET" autocomplete="off">
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
                                    <select name="office" id="office" class="form-control">
                                        <option value="">-- Filtro por consultorio --</option>
                                        @foreach (auth()->user()->clinicsAssistants as $userClinic)
                                            <option value="{{ $userClinic->id }}" {{ isset($search) && $search['office'] == $userClinic->id ? 'selected' : '' }}>
                                                {{ $userClinic->name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                                <div class="col-sm-2">
                                    <select name="medic" id="medic" class="form-control">
                                        <option value="">-- Filtro por medico --</option>
                                        @foreach ($medics as $medic)
                                            <option value="{{ $medic->id }}" {{ isset($search) && $search['medic'] == $medic->id ? 'selected' : '' }}>
                                                {{ $medic->name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                                <div class="col-sm-2">
                                    <select name="CodigoActividad" id="CodigoActividad" class="form-control" required>
                                        <option value="">-- Filtro por Actividad Económica --</option>
                                        @foreach ($activities as $activity)
                                            <option value="{{ $activity->codigo }}" {{ isset($search) && $search['CodigoActividad'] == $activity->codigo ? 'selected' : '' }}>{{ $activity->actividad }}</option>
                                        @endforeach

                                    </select>

                                </div>
                                <div class="col-sm-2">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">-- Estado --</option>

                                        <option value="pending" {{ isset($search['status']) && $search['status'] == 'pending' ? 'selected' : '' }}>Pendientes</option>
                                        <option value="cancel" {{ isset($search['status']) && $search['status'] == 'cancel' ? 'selected' : '' }}>Canceladas</option>
                                        <option value="overdue" {{ isset($search['status']) && $search['status'] == 'overdue' ? 'selected' : '' }}>Vencidas</option>


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
                                    <th scope="col"></th>
                                    <th scope="col">Conse.</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Médico / Admin</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Tipo Documento</th>
                                    <th scope="col">Condicion Venta</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Pendiente</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    @if ($invoice->PlazoCredito && $invoice->PlazoCredito == '30 días')
                                        <tr class="{{ $invoice->created_at->addDays(30)->lessThan(Illuminate\Support\Carbon::now()) ? 'invoice-pending' : '' }}">
                                        @elseif($invoice->PlazoCredito && $invoice->PlazoCredito == '45 días')
                                        <tr class="{{ $invoice->created_at->addDays(45)->lessThan(Illuminate\Support\Carbon::now()) ? 'invoice-pending' : '' }}">
                                        @else
                                        <tr class="{{ $invoice->PlazoCredito && Illuminate\Support\Carbon::parse($invoice->PlazoCredito)->lessThan(Illuminate\Support\Carbon::now()) ? 'invoice-pending' : '' }}">
                                    @endif

                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Más
                                                    <span class="fa fa-caret-down"></span></button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        @if ($invoice->TotalWithNota != $invoice->TotalComprobante)
                                                            <a href="#">No se puede agregar abonos por la nota de credito</a>
                                                        @else
                                                            <a href="#" data-toggle="modal" data-target="#paymentsModal" data-invoice="{{ $invoice->id }}">Abonos</a>
                                                        @endif
                                                    </li>

                                                </ul>
                                            </div>

                                        </div>
                                    </td>
                                    @if (auth()->user()->fe)
                                        <th scope="row"><a href="/invoices/{{ $invoice->id }}">{{ $invoice->NumeroConsecutivo }}
                                                {{ !$invoice->status ? ' - Pendiente de Facturar' : '' }}</a></th>
                                    @else
                                        <th scope="row"><a href="/invoices/{{ $invoice->id }}">{{ $invoice->consecutivo }}
                                                {{ !$invoice->status ? ' - Pendiente de Facturar' : '' }}</a></th>
                                    @endif
                                    <td>{{ $invoice->created_at }}</td>
                                    <td><a href="/invoices/{{ $invoice->id }}">{{ $invoice->user?->name }}</a></td>
                                    <td><a href="/invoices/{{ $invoice->id }}">{{ $invoice->cliente }}</a></td>
                                    <td>{{ $invoice->TipoDocumentoName }} @if ($invoice->TotalWithNota != $invoice->TotalComprobante)
                                            <span class="text-red tooltip-info" title="La factura tiene una nota de crédito o débito"><i class="fa fa-sticky-note"></i></span>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->CondicionVentaName }}</td>

                                    <td>{{ money($invoice->TotalComprobante, '') }}
                                        <small>{{ $invoice->CodigoMoneda }}</small>
                                    </td>
                                    <td>
                                        {{ money($invoice->cxc_pending_amount, '') }}
                                        <small>{{ $invoice->CodigoMoneda }}</small>
                                    </td>

                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="8"></td>
                                    <td>
                                        <b>Total:</b> {{ money($cxcTotals, '') }}
                                    </td>
                                </tr>
                                @if ($invoices)
                                    <tr>
                                        <td colspan="9" class="pagination-container">{!! $invoices->appends(['q' => $search['q'], 'start' => $search['start'], 'end' => $search['end'], 'status' => $search['status']])->render() !!}</td>
                                    </tr>
                                @endif


                            </tbody>

                        </table>
                        <payments-modal></payments-modal>
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
            $('#paymentsModal').on('show.bs.modal', function(e) {

                var button = $(e.relatedTarget)
                var invoice_id = button.attr('data-invoice');

                window.emitter.emit('showPaymentsModal', invoice_id);
            })

           
            $('#office').on('change', function(e) {


                $(this).parents('form').submit();

            });
            $('#medic').on('change', function(e) {


                $(this).parents('form').submit();

            });
        });
    </script>
@endpush
