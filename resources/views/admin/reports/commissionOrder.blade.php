@extends('layouts.admins.app')

@section('content')
<section class="content-header">
    <h1>Reporte de Órdenes (Farmacias)</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Lista de Órdenes</h3>
                    <div class="box-tools filters">
                        <form action="{{ url('/admin/reportsCommission/filter') }}" method="GET" autocomplete="off">
                            <div class="row">
                                <!-- Filtrar por fecha inicio -->
                                <div class="col-xs-12 col-sm-3">
                                    <div class="input-group">
                                        <input type="date" name="start" class="form-control" value="{{ $search['start'] ?? '' }}">
                                    </div>
                                </div>
                                <!-- Filtrar por fecha fin -->
                                <div class="col-xs-12 col-sm-3">
                                    <div class="input-group">
                                        <input type="date" name="end" class="form-control" value="{{ $search['end'] ?? '' }}">
                                    </div>
                                </div>
                                <!-- Filtrar por usuario -->
                                <div class="col-xs-12 col-sm-3">
                                    <div class="input-group">
                                        <select name="user" class="form-control">
                                            <option value="">Todas las Farmacias</option>
                                            @foreach(App\User::whereHas('roles', function($q) {
                                            $q->where('name', 'farmacia');})->get() as $user)
                                            <option value="{{ $user->id }}" {{ ($search['user'] ?? '') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Botón de buscar -->
                                <div class="col-xs-12 col-sm-3">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Filtrar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="box-body table-responsive no-padding" id="no-more-tables">
                    @if($report->count())
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th># Orden</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Farmacia</th>
                                <th>Total Orden</th>
                                <th>Comisión (5%)</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report as $order)
                            <tr>
                                <td>{{ $order->consecutive }}</td>
                                <td>{{ $order->date }}</td>
                                <td>{{ $order->user_name }}</td>
                                <td>{{ $order->pharmacy_name }}</td>
                                <td>${{ number_format($order->TotalOrdenes, 2) }}</td>
                                <td>${{ number_format($order->TotalComision, 2) }}</td>
                                <td>
                                <td>
                                    <a href="{{ url('/admin/reportsCommission/' . $order->consecutive) }}" class="btn btn-info btn-sm">Ver</a>

                                </td>
                                </td>
                            </tr>
                            @endforeach
                            <!-- Totales -->
                            <tr>
                                <th colspan="4">Totales</th>
                                <th>${{ number_format($totales['TotalOrdenes'], 2) }}</th>
                                <th>${{ number_format($totales['TotalComision'], 2) }}</th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                    @else
                    <p class="text-center">No hay órdenes registradas.</p>
                    @endif
                </div> <!-- /.box-body -->
            </div> <!-- /.box -->
        </div>
    </div>
</section>
@endsection

@push('scripts')

@endpush