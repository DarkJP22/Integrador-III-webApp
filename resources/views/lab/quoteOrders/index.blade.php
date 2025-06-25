@extends('layouts.laboratories.app')

@section('content')
    <section class="content-header">
        <h1>Cotizaciones de boleta</h1>

    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <form action="/lab/quotes" method="GET" autocomplete="off">
                            <div class="form-group">

                                <div class="col-sm-3">
                                    <div class="input-group input-group">


                                        <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
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
                                    <select name="status" id="status" class="form-control">
                                        <option value="">-- Filtro por estado --</option>
                                        <option value="0" {{ isset($search) && $search['status'] === '0' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="1" {{ isset($search) && $search['status'] === '1' ? 'selected' : '' }}>Procesada</option>

                                    </select>

                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>

                            </div>

                        </form>
                        <div class="box-tools">

                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding" id="no-more-tables">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Paciente</th>
                                    <th>Teléfono</th>
                                    <th>Boleta</th>
                                    <th style="width: 250px">Proforma</th>
                                    <th>Estado</th>

                                </tr>
                            </thead>
                            @foreach ($quotes as $quote)
                                <tr>

                                    <td data-title="ID">{{ $quote->id }}</td>
                                    <td data-title="Nombre">

                                        {{ $quote->name }}
                                        <div>
                                            {{ $quote->ide }}
                                        </div>


                                    </td>
                                    <td data-title="Teléfono">{{ $quote->phone_number }}</td>
                                    <td data-title="Boleta">
                                        <a href="{{ $quote->photo_url }}" target="_blank" download>Descargar Foto</a>
                                    </td>

                                    <td data-title="Fecha solicitada">

                                        @if(!$quote->uploaded_at)
                                        <form action="/lab/quotes/{{ $quote->id}}/upload-quote" method="POST" enctype="multipart/form-data">
                                          @csrf
                                          <input type="file" name="voucher">
                                          @if ($errors->has('voucher'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('voucher') }}</strong>
                                            </span>
                                          @endif
                                          <button type="submit" class="btn btn-secondary btn-sm">Subir Comprobante</button>
                                        </form>
                                        @else 
                                          <a href="{{ $quote->quote_url }}" target="_blank" download>Descargar Comprobante</a>
                                        @endif
                                    </td>

                                    <td data-title="Estado">
                                        <button @class([
                                            'btn',
                                            'btn-xs',
                                            'btn-success' => $quote->uploaded_at,
                                            'btn-danger' => !$quote->uploaded_at,
                                        ]) type="button" >
                                            {{ $quote->uploaded_at ? 'Procesada' : 'Pendiente' }}
                                        </button>
                                    </td>

                                </tr>
                            @endforeach
                            @if ($quotes)
                                <td colspan="6" class="pagination-container">{!! $quotes->appends(['q' => $search['q'], 'start' => $search['start'], 'end' => $search['end'], 'status' => $search['status']])->render() !!}</td>
                            @endif
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>
    <form method="post" id="form-status">
        {{ csrf_field() }}
    </form>
@endsection

