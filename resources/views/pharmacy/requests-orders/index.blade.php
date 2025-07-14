@extends('layouts.pharmacies.app')

@section('content')
<section class="content-header">
    <h1>Órdenes</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">

                <div class="box-header">

                    <form action="{{ route('pharmacy.orders.index') }}" method="GET" autocomplete="off">
                        <div class="col-sm-3">

                            <div class="input-group input-group">

                                <input type="text" name="q" class="form-control pull-right" 
                                    placeholder="Buscar por consecutivo, estado o usuario..." value="{{ request('q') }}">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>

                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <!--<th>ID</th>-->
                                <th>Consecutivo</th>
                                <!--<th>Farmacia</th>-->
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <!--<th>Total</th>-->
                                <th>Envío</th>
                                <th>Metodo de Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <!--<td>{{ $order->id }}</td>-->
                                <td>{{ $order->consecutive }}</td>
                                <!--<td>{{ $order->pharmacy->name ?? 'N/A' }}</td>-->
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>{{ $order->formatted_date }}</td>
                                <td>
                                    @switch($order->status)
                                    @case('cotizacion')
                                    <span class="label label-warning">Cotización</span>
                                    @break
                                    @case('esperando_confirmacion')
                                    <span class="label label-info">Esperando Confirmación</span>
                                    @break
                                    @case('confirmado')
                                    <span class="label label-success">Confirmado</span>
                                    @break
                                    @case('preparando')
                                    <span class="label label-primary">Preparando</span>
                                    @break
                                    @case('cancelado')
                                    <span class="label label-danger">Cancelado</span>
                                    @break
                                    @default
                                    <span class="label" style="background-color: purple; color: white;">Despachado</span>
                                    @endswitch
                                    <!--{{ ucfirst($order->status) }}</td>-->
                                </td>
                                <!--<td>₡{{ number_format($order->order_total, 2) }}</td>-->
                                <td class="text-center">
                                    @if($order->requires_shipping)
                                        <i class="fa fa-check text-success" style="font-size: 16px;" title="Requiere envío"></i>
                                    @else
                                        <i class="fa fa-times text-danger" style="font-size: 16px;" title="No requiere envío"></i>
                                    @endif
                                </td>
                                <td>
                                    @switch($order->payment_method)
                                    @case('0')
                                    <span class="label label-info">Efectivo</span>    
                                    @break
                                    @case('1')
                                    <span class="label label-success">SINPE</span>
                                    <!--{{$order->payment_method ? 'Si' : 'No'}}</td>-->
                                    @break
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('pharmacy.orders.edit', $order) }}" class="btn btn-xs btn-info"><i class="fa fa-eye"><strong> Ver solicitud</strong></i></a>
                                    <!--<a href="{{ route('pharmacy.orders.edit', $order) }}" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>-->
                                    <!--<form action="{{ route('pharmacy.orders.destroy', $order) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar esta orden?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>-->
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No hay órdenes registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="box-footer clearfix text-center">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection