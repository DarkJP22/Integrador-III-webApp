@extends('layouts.pharmacies.app')

@section('content')
<section class="content-header">
    <h1>Órdenes</h1>
</section>

<section class="content">
    <!-- Mensajes de estado -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <div class="col-xs-12">
            <div class="box">

                <div class="box-header">
                    <!-- Formulario de Búsqueda Avanzada -->
                    <form action="{{ route('pharmacy.orders.index') }}" method="GET" autocomplete="off" id="search-form">
                        <div class="row">
                            <!-- Búsqueda por usuario -->
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="q">Buscar Usuario:</label>
                                    <div class="input-group">
                                        <input type="text" name="q" id="q" class="form-control"
                                            placeholder="Ej: usuarios que empiecen con 'j'"
                                            value="{{ request('q') }}">
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i>
                                            </button>
                                            <a href="{{ route('pharmacy.orders.index') }}" class="btn btn-default">
                                                <i class="fa fa-refresh"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtro por Estado -->
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="status">Estado:</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Todos los estados</option>
                                        @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Filtros adicionales -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>&nbsp;</label> <!-- Espaciador para alinear con otros campos -->
                                    <div style="height: auto; padding: 8px 0; background: none; border: none;">
                                        <span style="margin-right: 15px;">
                                            <strong>Pago:</strong>
                                            @foreach($paymentMethodOptions as $value => $label)
                                                <label style="font-weight: normal; margin-left: 8px; margin-right: 10px;">
                                                    <input type="checkbox" name="payment_method[]" value="{{ $value }}"
                                                        {{ in_array($value, request('payment_method', [])) ? 'checked' : '' }}
                                                        style="margin-right: 4px;">
                                                    {{ $label }}
                                                </label>
                                            @endforeach
                                        </span>
                                        <span>
                                            <strong>Envío:</strong>
                                            @foreach($shippingRequiredOptions as $value => $label)
                                                <label style="font-weight: normal; margin-left: 8px; margin-right: 10px;">
                                                    <input type="checkbox" name="requires_shipping[]" value="{{ $value }}"
                                                        {{ in_array($value, request('requires_shipping', [])) ? 'checked' : '' }}
                                                        style="margin-right: 4px;">
                                                    {{ $label }}
                                                </label>
                                            @endforeach
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón para crear una prueba de nueva orden 
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('pharmacy.orders.create') }}" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Crear Orden de Prueba
                                </a>
                            </div>
                        </div>
                        -->
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
                                    @switch($order->status->value ?? $order->status)
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
                                    @case('despachado')
                                    <span class="label" style="background-color: purple; color: white;">Despachado</span>
                                    @break
                                    @default
                                    <span class="label label-default">{{ $order->status_text }}</span>
                                    @endswitch
                                </td>
                                <!--<td>₡{{ number_format($order->order_total, 2) }}</td>-->
                                <td class="text-center">
                                    @if($order->requiresShipping())
                                    <i class="fa fa-check text-success" style="font-size: 16px;" title="Requiere envío"></i>
                                    @else
                                    <i class="fa fa-times text-danger" style="font-size: 16px;" title="No requiere envío"></i>
                                    @endif
                                </td>
                                <td>
                                    @php
                                    $paymentClass = $order->isElectronicPayment() ? 'label-success' : 'label-info';
                                    @endphp
                                    <span class="label {{ $paymentClass }}">
                                        {{ $order->payment_method_text }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pharmacy.orders.edit', $order) }}" class="btn btn-xs btn-info">
                                        <i class="fa fa-eye"></i> Ver solicitud
                                    </a>
                                    <!--<a href="{{ route('pharmacy.orders.edit', $order) }}" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>-->

                                    @if($order->status === 'cancelado' || (isset($order->status->value) && $order->status->value === 'cancelado'))
                                    <form action="{{ route('pharmacy.orders.destroy', $order) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar esta orden?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
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

@push('scripts')
<script>
    // Auto-submit del formulario cuando cambian los filtros
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('status').addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });

        // Auto-submit cuando se cambian los checkboxes
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('search-form').submit();
            });
        });
    });

    // Escuchar eventos de nuevas órdenes
    function listenPharmacyOrderUpdate() {
        if (window.Echo && window.App && window.App.user && window.App.user.id) {
            console.log('Registrando listener PharmacyOrderUpdate');
            window.Echo.private(`App.User.${window.App.user.id}`)
                .listen('PharmacyOrderUpdate', (e) => {
                    alert('Recargando la página... Nueva orden: ' + (e.order_id || ''));
                    console.log('Evento PharmacyOrderUpdate:', e);
                    window.location.reload();
                });
        } else {
            // Intenta de nuevo en 200ms
            setTimeout(listenPharmacyOrderUpdate, 200);
        }
    }
listenPharmacyOrderUpdate();
</script>
@endpush

@push('styles')
<style>
    .box-header {
        background-color: #f9f9f9;
        border-bottom: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 0;
    }

    .search-form-container {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 10px;
    }

    .form-group label {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .checkbox-inline {
        margin-right: 15px;
    }

    .checkbox-inline label {
        font-weight: normal;
        padding-left: 20px;
    }

    .label-default {
        background-color: #6c757d !important;
    }

    /* Mejorar la tabla */
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    /* Estilos para los badges de estado */
    .label-warning {
        background-color: #f39c12;
    }

    .label-info {
        background-color: #3498db;
    }

    .label-success {
        background-color: #27ae60;
    }

    .label-primary {
        background-color: #9b59b6;
    }

    .label-danger {
        background-color: #e74c3c;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .col-sm-3 {
            margin-bottom: 15px;
        }

        .checkbox-inline {
            display: block;
            margin-bottom: 5px;
        }
    }
</style>
@endpush