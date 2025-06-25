@extends('layouts.clinics.app')
@section('header')
    <link rel="stylesheet" href="/vendor/tooltipster.bundle.min.css">
@endsection
@section('content')
    <section class="content-header">
        <h1>Proformas</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <a href="/proformas/create" class="btn btn-primary">Crear Proforma</a>

                        <form action="/clinic/proformas" method="GET" autocomplete="off">
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
                                        @foreach (auth()->user()->offices as $userClinic)
                                            <option value="{{ $userClinic->id }}" {{ isset($search) && $search['office'] == $userClinic->id ? 'selected' : '' }}>{{ $userClinic->name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                                <div class="col-sm-2">
                                    <select name="medic" id="medic" class="form-control">
                                        <option value="">-- Filtro por medico --</option>
                                        @foreach ($medics as $medic)
                                            <option value="{{ $medic->id }}" {{ isset($search) && $search['medic'] == $medic->id ? 'selected' : '' }}>{{ $medic->name }}</option>
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

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proformas as $proforma)
                                    <tr class="{{ $proforma->TotalWithNota != $proforma->TotalComprobante ? 'table-warning' : '' }} {{ $proforma->status ? '' : 'invoice-pending' }}">
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Más
                                                        <span class="fa fa-caret-down"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="/proformas/{{ $proforma->id }}">Ver detalle</a></li>


                                                    </ul>
                                                </div>

                                            </div>
                                        </td>

                                        <th scope="row">
                                            <a href="/proformas/{{ $proforma->id }}">

                                                {{ $proforma->consecutivo }}

                                            </a>

                                        </th>

                                        <td>{{ $proforma->created_at }}</td>
                                        <td><a href="/proformas/{{ $proforma->id }}">{{ Optional($proforma->user)->name }}</a></td>
                                        <td><a href="/proformas/{{ $proforma->id }}">{{ $proforma->cliente }}</a></td>
                                        <td>{{ $proforma->TipoDocumentoName }} </td>
                                        <td>{{ $proforma->CondicionVentaName }}</td>

                                        <td>{{ money($proforma->TotalComprobante, '') }} <small>{{ $proforma->CodigoMoneda }}</small></td>


                                    </tr>
                                @endforeach
                                @if ($proformas)
                                    <td colspan="6" class="pagination-container">{!! $proformas->appends(['q' => $search['q'], 'start' => $search['start'], 'end' => $search['end'], 'medic' => $search['medic'], 'office' => $search['office']])->render() !!}</td>
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
    <script src="/vendor/clipboard/clipboard.min.js"></script>
    <script src="/vendor/tooltipster.bundle.min.js"></script>
    <script>
        $(function() {
            $('.tooltip-info').tooltipster({
                theme: ['tooltipster-noir', 'tooltipster-gps']
            });




  
            $('#office').on('change', function(e) {


                $(this).parents('form').submit();

            });
            $('#medic').on('change', function(e) {


                $(this).parents('form').submit();

            });
        });
    </script>
@endpush
