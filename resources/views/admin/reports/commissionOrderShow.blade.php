@extends('layouts.admins.app')

@section('content')
<section class="content-header">
    <h1>Detalle de la Orden #{{ $order->consecutive}}</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Información de la Orden</h3>
                </div>

                <div class="box-body">
                    <p><strong>Cliente:</strong> {{ $order->user->name }}</p>
                    <p><strong>Farmacia:</strong> {{ $order->pharmacy->name }}</p>
                    <p><strong>Estado:</strong> 
                       <span class="label" style="background-color: purple; color: white;">
                        {{ $order->status }}
                          </span>
                           </p>
                    <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>

                    <h3>Productos</h3>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->quantity_available }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- /.box-body -->

                <div class="box-footer">
                    <a href="{{ url('/admin/reportsCommission') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div> <!-- /.box -->
        </div>
    </div>
</section>
@endsection

@push('scripts')
@endpush
