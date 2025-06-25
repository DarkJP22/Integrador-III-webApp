@extends('layouts.laboratories.app')
@section('header')
    <link rel="stylesheet" href="/vendor/tooltipster.bundle.min.css">
@endsection
@section('content')
    <section class="content-header">
        <h1>Facturación</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        @if (auth()->user()->hasRole('laboratorio'))
                            <a href="/receptor/mensajes/create" class="btn btn-primary">Confirmar Comprobante</a>
                        @endif


                        <div class="box-tools">
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>

                                    <th scope="col">Fecha</th>
                                    <th scope="col">Conse Receptor.</th>
                                    <th scope="col">Conse Factura Emisor</th>
                                    <th scope="col">Emisor</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Mensaje</th>
                                    <th>Estado Hacienda</th>
                                    <th></th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($receptors as $receptor)
                                    <tr>


                                        <td>{{ $receptor->created_at }}</td>
                                        <th scope="row">{{ $receptor->NumeroConsecutivoReceptor }}</th>

                                        <td class="py-2 px-2">
                                            {{ $receptor->NumeroConsecutivo }} <br>
                                            <small>Clave:{{ $receptor->Clave }} </small>
                                        </td>

                                        <td class="py-2 px-2">{{ $receptor->NumeroCedulaEmisor }} - {{ $receptor->nombre_emisor }}</td>

                                        <td>{{ money($receptor->TotalFactura, '') }} {{ $receptor->CodigoMoneda }}</td>
                                        <td>{{ $receptor->Mensaje }}</td>
                                        <td>

                                            @if ($receptor->sent_to_hacienda)
                                                @if ($receptor->status_fe)
                                                    <a href="#" data-toggle="modal" data-target="#modalRespHacienda" title="Click para comprobar estado de factura" data-invoice="{{ $receptor->id }}"><span class="label label-{{ trans('utils.status_hacienda_color.' . $receptor->status_fe) }}">{{ Illuminate\Support\Str::title($receptor->status_fe) }}</span> </a>
                                                @else
                                                    <a href="#" data-toggle="modal" data-target="#modalRespHacienda" title="Click para comprobar estado de factura" data-invoice="{{ $receptor->id }}"><span class="label label-warning">Comprobar</span> </a>
                                                @endif
                                            @else
                                                @if ($receptor->status)
                                                    <send-mensaje-to-hacienda :receptor-id="{{ $receptor->id }}"></send-mensaje-to-hacienda>
                                                @else
                                                    <span class="label label-warning">Pendiente</span>
                                                @endif
                                            @endif


                                        </td>
                                        <td>
                                            {{-- @can('delete', $receptor)
                                  <!-- <button type="submit" class="btn btn-danger" form="form-delete" formaction="{!! url('/receptor/mensajes/'.$receptor->id) !!}" title="Eliminar Mensaje"><i class="fa fa-remove"></i></button> -->
                              @endcan --}}
                                        </td>


                                    </tr>
                                @endforeach
                                <tr>
                                    <th colspan="5" class="text-right">Total CRC: {{ money($totales->totalFacturaCRC, '') }}</th>
                                    <th colspan="5" class="text-right">Total USD: {{ money($totales->totalFacturaUSD, '') }}</th>
                                </tr>
                                @if ($receptors)
                                    <td colspan="7" class="pagination-container">{!! $receptors->appends(['q' => $search['q'], 'start' => $search['start'], 'end' => $search['end']])->render() !!}</td>
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


    <modal-status-mensaje-hacienda></modal-status-mensaje-hacienda>


    <form method="post" id="form-delete" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>
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
