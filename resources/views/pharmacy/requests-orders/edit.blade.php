@extends('layouts.pharmacies.app')

@section('content')
<style>
    .form-group {
        margin-bottom: 15px;
        clear: both;
    }
    
    .box {
        margin-bottom: 20px;
        clear: both;
    }
    
    .alert {
        margin-bottom: 20px;
        clear: both;
    }
    
    .table-responsive {
        clear: both;
        overflow-x: auto;
    }
    
    .input-group {
        width: 100%;
    }
    
    .row {
        margin-left: -15px;
        margin-right: -15px;
    }
    
    .row::after {
        content: "";
        display: table;
        clear: both;
    }
    
    .col-md-4, .col-md-6, .col-md-12 {
        position: relative;
        min-height: 1px;
        padding-left: 15px;
        padding-right: 15px;
    }
    
    @media (min-width: 992px) {
        .col-md-4 {
            width: 33.33333333%;
            float: left;
        }
        .col-md-6 {
            width: 50%;
            float: left;
        }
        .col-md-12 {
            width: 100%;
            float: left;
        }
    }
</style>

<section class="content-header">
    <h1>Detalle de Orden</h1>
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

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        <h6>Por favor, corrija los siguientes errores:</h6>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <form action="{{ route('pharmacy.orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Información de la Orden</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Consecutivo</label>
                        <input type="text" class="form-control" value="{{ $order->consecutive }}" readonly>
                    </div>

                    <input type="hidden" name="pharmacy_id" value="{{ old('pharmacy_id', $order->pharmacy_id) }}">

                    <div class="form-group col-md-4">
                        <label>Usuario</label>
                        <input type="text" class="form-control" value="{{ $order->user->name ?? 'N/A' }}" disabled>
                        <input type="hidden" name="user_id" value="{{ $order->user_id }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Fecha</label>
                        <input type="datetime-local" class="form-control" value="{{ $order->date ? date('Y-m-d\TH:i', strtotime($order->date)) : '' }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Estado</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ (($order->status->value ?? $order->status) == $value) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Método de Pago</label>
                        <input type="text" class="form-control" value="{{ $order->payment_method_text }}" readonly>
                        <input type="hidden" name="payment_method" value="{{ $order->payment_method->value ?? $order->payment_method }}">
                    </div>
                </div>

                @if($order->isElectronicPayment())
                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Comprobante de Pago SINPE</label>
                        @if($order->voucher)
                        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background-color: #f9f9f9; border-radius: 4px; border: 1px solid #ddd;">
                            <i class="fa fa-file-image-o text-success" style="font-size: 18px;"></i>
                            <div style="flex: 1;">
                                <strong>Comprobante subido por el usuario</strong>
                                <br><small class="text-muted">{{ basename($order->voucher) }}</small>
                            </div>
                            <a href="{{ asset('storage/' . $order->voucher) }}" target="_blank" class="btn btn-success btn-sm">
                                <i class="fa fa-eye"></i> Ver Comprobante
                            </a>
                        </div>
                        @else
                        <div style="padding: 15px; background-color: #fcf8e3; border: 1px solid #faebcc; border-radius: 4px; color: #8a6d3b;">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Sin comprobante</strong>
                            <br><small>El usuario aún no ha subido el comprobante de pago desde la app móvil.</small>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>¿Requiere envío?</label>
                        <input type="text" class="form-control" value="{{ $order->requiresShipping() ? 'Sí' : 'No' }}" readonly>
                        <input type="hidden" name="requires_shipping" value="{{ $order->requires_shipping->value ?? $order->requires_shipping }}">
                    </div>
                </div>
            </div>
        </div>

        @if($order->requiresShipping())
        <div class="box box-info" style="margin-top: 20px;">
            <div class="box-header with-border">
                <h3 class="box-title">Información de Envío</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Dirección</label>
                        <textarea name="address" class="form-control" rows="2" readonly>{{ old('address', $order->address) }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Latitud</label>
                        <input type="text" name="lat" class="form-control" value="{{ old('lat', $order->lat) }}" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Longitud</label>
                        <input type="text" name="lng" class="form-control" value="{{ old('lot', $order->lot) }}" readonly>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <hr style="margin: 30px 0;">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Productos solicitados</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Medicamento</th>
                                <th>Descripción</th>
                                <th>Cant. Solicitada</th>
                                <th>Cant. Disponible</th>
                                <th>Precio Unitario (₡)</th>
                                <th>SubTotal (₡)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->details as $detail)
                            <tr>
                                <td>
                                    <strong>{{ $detail->drug->name ?? 'N/A' }}</strong>
                                    @if($detail->drug->brand)
                                    <br><small class="text-muted">{{ $detail->drug->brand }}</small>
                                    @endif
                                </td>
                                <td>{{ $detail->description ?? 'Sin descripción' }}</td>
                                <td class="text-center">
                                    <span class="label label-info">{{ $detail->requested_amount }}</span>
                                </td>
                                <td>
                                    <input type="number" step="1" min="0" max="{{ $detail->requested_amount }}"
                                        name="details[{{ $detail->id }}][quantity_available]"
                                        class="form-control quantity @error('details.' . $detail->id . '.quantity_available') is-invalid @enderror"
                                        value="{{ old('details.' . $detail->id . '.quantity_available', $detail->quantity_available) }}">
                                    @error('details.' . $detail->id . '.quantity_available')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">₡</span>
                                        <input type="number" step="0.01" min="0"
                                            name="details[{{ $detail->id }}][unit_price]"
                                            class="form-control price @error('details.' . $detail->id . '.unit_price') is-invalid @enderror"
                                            value="{{ old('details.' . $detail->id . '.unit_price', $detail->unit_price) }}">
                                    </div>
                                    @error('details.' . $detail->id . '.unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal" style="background-color: #f4f4f4;"
                                        value="₡{{ number_format(
                                    ($detail->requested_amount > $detail->quantity_available 
                                        ? $detail->quantity_available 
                                           : $detail->requested_amount) * $detail->unit_price, 2
                                ) }}" readonly>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                                    <i class="fa fa-info-circle"></i> Esta orden no tiene productos asociados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="box box-success" style="margin-top: 20px;">
            <div class="box-header with-border">
                <h3 class="box-title">Resumen de Totales</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>SubTotal de Productos</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" name="products_total" id="products_total" class="form-control" style="background-color: #f4f4f4;" value="0.00" readonly>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Total de Orden + IVA (13%)</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" name="order_total" id="order_total" class="form-control" style="background-color: #f4f4f4;" value="0.00" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Costo de Envío</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" step="0.01" min="0" name="shipping_cost" id="shipping_cost"
                                class="form-control @error('shipping_cost') is-invalid @enderror"
                                value="{{ old('shipping_cost', $order->shipping_total - $order->order_total) }}">
                        </div>
                        @error('shipping_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($order->requiresShipping())
                    <div class="form-group col-md-6">
                        <label><strong>Total Final con Envío</strong></label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" name="shipping_total" id="shipping_total"
                                class="form-control" style="background-color: #d4edda; font-weight: bold;" value="0.00" readonly>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 30px;">
            <div class="col-md-12">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('pharmacy.orders.index') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Regresar
                    </a>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('¿Está seguro de actualizar esta orden?')">
                        <i class="fa fa-save"></i> Actualizar Orden
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shippingInput = document.getElementById('shipping_cost');
        const shippingTotalInput = document.getElementById('shipping_total');
        const orderTotalInput = document.getElementById('order_total');
        const subtotalInput = document.getElementById('products_total');
        const IVA_RATE = 0.13; // 13% IVA

        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CR', {
                style: 'currency',
                currency: 'CRC',
                minimumFractionDigits: 2
            }).format(amount);
        }

        function calcularFila(fila) {
            const cantidadInput = fila.querySelector('.quantity');
            const precioInput = fila.querySelector('.price');
            const filaSubtotalInput = fila.querySelector('.subtotal');

            if (!cantidadInput || !precioInput || !filaSubtotalInput) {
                return 0;
            }

            // Obtener la cantidad solicitada desde la celda correspondiente
            const cantidadSolicitadaCell = fila.cells[2];
            const labelElement = cantidadSolicitadaCell.querySelector('.label');
            const cantidadSolicitadaText = labelElement ? labelElement.textContent : cantidadSolicitadaCell.textContent;
            const cantidadSolicitada = parseFloat(cantidadSolicitadaText.trim()) || 0;

            const cantidadDisponible = parseFloat(cantidadInput.value) || 0;
            const precio = parseFloat(precioInput.value) || 0;

            // Validar que la cantidad disponible no exceda la solicitada
            if (cantidadDisponible > cantidadSolicitada) {
                cantidadInput.value = cantidadSolicitada;
                showAlert('La cantidad disponible no puede ser mayor a la solicitada', 'warning');
            }

            // Aplicar la lógica de negocio:
            // Si cantidad solicitada > disponible, usar disponible
            // Si cantidad solicitada <= disponible, usar solicitada
            const cantidadParaCalculo = Math.min(cantidadSolicitada, cantidadDisponible);

            const subtotal = cantidadParaCalculo * precio;
            filaSubtotalInput.value = formatCurrency(subtotal);
            return subtotal;
        }

        function calcularTotales() {
            let subtotalGeneral = 0;

            document.querySelectorAll('tbody tr').forEach(fila => {
                const cantidadInput = fila.querySelector('.quantity');
                const precioInput = fila.querySelector('.price');

                if (cantidadInput && precioInput) {
                    subtotalGeneral += calcularFila(fila);
                }
            });

            const totalConIVA = subtotalGeneral * (1 + IVA_RATE);
            const shipping = parseFloat(shippingInput?.value) || 0;
            const totalFinal = totalConIVA + shipping;

            // Actualizar campos
            if (subtotalInput) subtotalInput.value = subtotalGeneral.toFixed(2);
            if (orderTotalInput) orderTotalInput.value = totalConIVA.toFixed(2);
            if (shippingTotalInput) shippingTotalInput.value = totalFinal.toFixed(2);

            // Actualizar badges de estado
            updateTotalBadges(subtotalGeneral, totalConIVA, totalFinal);
        }

        function updateTotalBadges(subtotal, totalIVA, totalFinal) {
            // Puedes añadir badges o indicadores visuales aquí
            console.log('Totales calculados:', {
                subtotal,
                totalIVA,
                totalFinal
            });
        }

        function showAlert(message, type = 'info') {
            // Función simple para mostrar alertas compatibles
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible`;
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.maxWidth = '400px';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="close" onclick="this.parentElement.remove()">
                    <span aria-hidden="true">&times;</span>
                </button>
            `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 3000);
        }

        function initializeEventListeners() {
            // Event listeners para cantidad y precio
            document.querySelectorAll('.quantity, .price').forEach(input => {
                input.addEventListener('input', function() {
                    // Validar valores negativos
                    if (parseFloat(this.value) < 0) {
                        this.value = 0;
                    }
                    calcularTotales();
                });

                input.addEventListener('blur', function() {
                    // Formatear valor al perder el foco
                    if (this.classList.contains('price') && this.value) {
                        this.value = parseFloat(this.value).toFixed(2);
                    }
                });
            });

            // Event listener para envío
            if (shippingInput) {
                shippingInput.addEventListener('input', function() {
                    if (parseFloat(this.value) < 0) {
                        this.value = 0;
                    }
                    calcularTotales();
                });
            }
        }

        // Inicializar
        initializeEventListeners();
        calcularTotales();

        // Agregar validación antes del submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const hasProducts = document.querySelectorAll('.quantity').length > 0;
            const hasValidQuantities = Array.from(document.querySelectorAll('.quantity')).some(input => parseFloat(input.value) > 0);

            if (hasProducts && !hasValidQuantities) {
                e.preventDefault();
                showAlert('Debe especificar al menos una cantidad disponible mayor a 0', 'error');
                return false;
            }
        });
    });
</script>
@endpush