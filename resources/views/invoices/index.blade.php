@extends('layouts.medics.app')
@section('header')
    <link rel="stylesheet" href="/vendor/tooltipster.bundle.min.css">
@endsection
@section('content')
    <section class="content-header">
        <h1>Facturación</h1>

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
                        @if (auth()->user()->hasRole('medico') &&
                                auth()->user()->subscriptionPlanHasFe())
                            <a href="/invoices/create" class="btn btn-primary">Crear Factura</a>
                        @endif

                        <form action="/medic/invoices" method="GET" autocomplete="off">
                            <div class="form-group">

                                <div class="col-sm-2">
                                    <div class="input-group input-group-sm">


                                        <input type="text" name="q" class="form-control" placeholder="Cliente..." value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>


                                    </div>

                                </div>
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
                                    @if (auth()->user()->fe)
                                        <th>Estado Hacienda</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr class="{{ $invoice->TotalWithNota != $invoice->TotalComprobante ? 'table-warning' : '' }} {{ $invoice->status ? '' : 'invoice-pending' }}">
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Más
                                                        <span class="fa fa-caret-down"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="/invoices/{{ $invoice->id }}">Ver detalle</a></li>

                                                        @if ($invoice->status && $invoice->fe)
                                                            <li><a href="/invoices/{{ $invoice->id }}/notacredito">Generar Nota Crédito</a></li>
                                                            <li><a href="/invoices/{{ $invoice->id }}/notadebito">Generar Nota Débito</a></li>
                                                            <li><a href="/invoices/{{ $invoice->id }}/download/xml">XML</a></li>
                                                            <li><a href="#" class="btn-copy-clave" data-clipboard-text="{{ $invoice->clave_fe }}">
                                                                    <i class="fa fa-copy"></i> Clave de factura
                                                                </a></li>
                                                        @endif


                                                    </ul>
                                                </div>

                                            </div>
                                        </td>
                                        <th scope="row">
                                            <a href="/invoices/{{ $invoice->id }}">
                                                @if ($invoice->fe)
                                                    {{ $invoice->NumeroConsecutivo }} {{ !$invoice->status ? ' - Pendiente de Facturar' : '' }}
                                                @else
                                                    {{ $invoice->consecutivo }} {{ !$invoice->status ? ' - Pendiente de Facturar' : '' }}
                                                @endif
                                            </a>
                                            <br>
                                            @if ($invoice->TipoDocumento == '01' || $invoice->TipoDocumento == '04')
                                                @foreach ($invoice->notascreditodebito as $nota)
                                                    <small>Nota: {{ $nota->invoice->NumeroConsecutivo }}</small>
                                                @endforeach
                                            @else
                                                @foreach ($invoice->referencias as $nota)
                                                    <small>Doc: {{ $nota->NumeroDocumento }}</small>
                                                @endforeach
                                            @endif
                                        </th>
                                        <td>{{ $invoice->created_at }}</td>
                                        <td><a href="/invoices/{{ $invoice->id }}">{{ Optional($invoice->user)->name }}</a> <br> {{ Optional($invoice->clinic)->name }}</td>
                                        <td><a href="/invoices/{{ $invoice->id }}">{{ $invoice->cliente }}</a></td>
                                        <td>{{ $invoice->TipoDocumentoName }} @if ($invoice->TotalWithNota != $invoice->TotalComprobante)
                                                <span class="text-red tooltip-info" title="La factura tiene una nota de crédito o débito"><i class="fa fa-sticky-note"></i></span>
                                            @endif
                                        </td>
                                        <td>{{ $invoice->CondicionVentaName }}</td>

                                        <td>{{ money($invoice->TotalComprobante, '') }} <small>{{ $invoice->CodigoMoneda }}</small></td>
                                        @if ($invoice->fe)
                                            <td>
                                                @if ($invoice->fe)
                                                    @if (!$invoice->sent_to_hacienda && (int)now()->diffInMinutes($invoice->created_at) < 1)
                                                        <span class="label label-warning">Enviando...</span>
                                                    @else
                                                        @if ($invoice->sent_to_hacienda)
                                                            @if ($invoice->status_fe)
                                                                <a href="#" data-toggle="modal" data-target="#modalRespHacienda" title="Click para comprobar estado de factura" data-invoice="{{ $invoice->id }}"><span class="label label-{{ trans('utils.status_hacienda_color.' . $invoice->status_fe) }}">{{ Illuminate\Support\Str::title($invoice->status_fe) }}</span> </a>
                                                            @else
                                                                <a href="#" data-toggle="modal" data-target="#modalRespHacienda" title="Click para comprobar estado de factura" data-invoice="{{ $invoice->id }}"><span class="label label-warning">Comprobar</span> </a>
                                                            @endif
                                                        @else
                                                            @if ($invoice->status)
                                                                <send-to-hacienda :invoice-id="{{ $invoice->id }}"></send-to-hacienda>
                                                            @else
                                                                <span class="label label-warning">Pendiente</span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @else
                                                    --
                                                @endif
                                            </td>
                                        @endif

                                    </tr>
                                @endforeach
                                @if ($invoices)
                                    <td colspan="6" class="pagination-container">{!! $invoices->appends(['q' => $search['q'], 'start' => $search['start'], 'end' => $search['end'], 'office' => $search['office']])->render() !!}</td>
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


    <modal-status-hacienda></modal-status-hacienda>



@endsection
@push('scripts')
    <script src="/vendor/clipboard/clipboard.min.js"></script>
    <script src="/vendor/tooltipster.bundle.min.js"></script>
    <script>
        $(function() {
            $('.tooltip-info').tooltipster({
                theme: ['tooltipster-noir', 'tooltipster-gps']
            });

            let clipboard = new ClipboardJS('.btn-copy-clave');

            clipboard.on('success', (e) => {
                flash('Clave Copiada correctamenta!!')

                e.clearSelection();
            });

            $('#modalRespHacienda').on('show.bs.modal', function(e) {

                var button = $(e.relatedTarget)
                var invoiceId = button.attr('data-invoice');

                window.emitter.emit('showStatusHaciendaModal', invoiceId);
            })

            
            $('#office').on('change', function(e) {


                $(this).parents('form').submit();

            });
        });
    </script>
@endpush
