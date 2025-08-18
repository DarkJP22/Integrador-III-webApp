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

    {{-- Información contextual según el estado --}}
    @if($order->status->value === 'cotizacion')
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Cotización:</strong> Complete los precios y cantidades disponibles, luego use "Responder Cotización".
        </div>
    @elseif($order->status->value === 'esperando_confirmacion')
        <div class="alert alert-warning">
            <i class="fa fa-clock-o"></i> <strong>Esperando confirmación del usuario.</strong> El usuario debe confirmar o cancelar esta cotización desde la app móvil.
        </div>
    @elseif($order->status->value === 'confirmado')
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> <strong>¡Orden confirmada!</strong> Verifique el pago y proceda a preparar la orden.
        </div>
    @elseif($order->status->value === 'preparando')
        <div class="alert alert-info">
            <i class="fa fa-cogs"></i> <strong>Orden en preparación.</strong> Cuando esté lista, márquela como despachada.
        </div>
    @elseif($order->status->value === 'despachado')
        <div class="alert alert-success">
            <i class="fa fa-truck"></i> <strong>Orden despachada exitosamente.</strong>
        </div>
    @elseif($order->status->value === 'cancelado')
        <div class="alert alert-danger">
            <i class="fa fa-times-circle"></i> <strong>Orden cancelada.</strong>
        </div>
    @endif

    <form id="order-main-form" action="{{ route('pharmacy.orders.update', $order) }}" method="POST">
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
                        <label>Estado editable a manualmente</label>
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
                            <a href="{{ route('pharmacy.orders.voucher', $order) }}" target="_blank" class="btn btn-info btn-sm">
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
                @elseif($order->status->value === 'confirmado' && !$order->isElectronicPayment())
                <div class="row">
                    <div class="form-group col-md-12">
                        <div style="padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724;">
                            <i class="fa fa-money text-success"></i>
                            <strong>Pago en Efectivo</strong>
                            <br><small>El usuario pagará en efectivo al recibir la orden.</small>
                        </div>
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
                                <th>IVA (%)</th>
                                <th>SubTotal + IVA (₡)</th>
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
                                    <input type="hidden" name="details[{{ $detail->id }}][drug_id]" value="{{ $detail->drug_id }}">
                                </td>
                                <td>{{ $detail->description ?? 'Sin descripción' }}</td>
                                <td class="text-center">
                                    <span class="label label-info">{{ $detail->requested_amount }}</span>
                                </td>
                                <td>
                                    <input type="number" step="1" min="0" max="{{ $detail->requested_amount }}"
                                        name="details[{{ $detail->id }}][quantity_available]"
                                        class="form-control quantity @error('details.' . $detail->id . '.quantity_available') is-invalid @enderror"
                                        value="{{ old('details.' . $detail->id . '.quantity_available', $detail->quantity_available) }}"
                                        {{ in_array($order->status->value, ['preparando', 'despachado', 'cancelado']) ? 'readonly' : '' }}>
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
                                            value="{{ old('details.' . $detail->id . '.unit_price', $detail->unit_price) }}"
                                            {{ in_array($order->status->value, ['preparando', 'despachado', 'cancelado']) ? 'readonly' : '' }}>
                                    </div>
                                    @error('details.' . $detail->id . '.unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="details[{{ $detail->id }}][iva_percentage]"
                                            class="form-control iva-percentage @error('details.' . $detail->id . '.iva_percentage') is-invalid @enderror"
                                            value="{{ old('details.' . $detail->id . '.iva_percentage', $detail->iva_percentage ?? 13) }}"
                                            placeholder="13"
                                            {{ in_array($order->status->value, ['preparando', 'despachado', 'cancelado']) ? 'readonly' : '' }}>
                                        <span class="input-group-addon">%</span>
                                    </div>
                                    @error('details.' . $detail->id . '.iva_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal" style="background-color: #f4f4f4;"
                                        value="{{ number_format($detail->products_total, 2) }}" readonly>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted" style="padding: 30px;">
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
                    <div class="form-group col-md-4">
                        <label>SubTotal de Productos (Sin IVA)</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" name="products_subtotal" id="products_subtotal" class="form-control" style="background-color: #f4f4f4;" value="{{ number_format($order->products_subtotal, 2, '.', '') }}" readonly>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Total IVA</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" name="total_iva" id="total_iva" class="form-control" style="background-color: #f4f4f4;" value="{{ number_format($order->iva_total, 2, '.', '') }}" readonly>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Total de Productos (Con IVA)</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" name="products_total_display" id="products_total_display" class="form-control" style="background-color: #f4f4f4;" value="{{ number_format($order->products_total, 2, '.', '') }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    @if ($order->requiresShipping() && ($order->status->value === 'cotizacion' || $order->status->value === 'confirmado'))
                    <div class="form-group col-md-6">
                        <label>Costo de Envío 
                            @if($order->status->value === 'cotizacion')
                                <small class="text-muted">(Establecer para responder cotización)</small>
                            @else
                                <small class="text-muted">(Editable para confirmar y preparar)</small>
                            @endif
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" step="0.01" min="0" name="shipping_cost" id="shipping_cost"
                                class="form-control @error('shipping_cost') is-invalid @enderror"
                                value="{{ number_format($order->shipping_cost, 2, '.', '') }}">
                        </div>
                        @error('shipping_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @elseif ($order->requiresShipping())
                    <div class="form-group col-md-6">
                        <label>Costo de Envío</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" class="form-control" 
                                value="{{ number_format($order->shipping_cost, 2, '.', '') }}" 
                                style="background-color: #f4f4f4;" readonly>
                        </div>
                        <small class="text-muted">Costo establecido al confirmar la orden</small>
                    </div>
                    @endif

                    <div class="form-group col-md-6">
                        <label><strong>{{ $order->requiresShipping() ? 'Total Final con Envío' : 'Total Final' }}</strong></label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" name="order_total" id="order_total"
                                class="form-control" style="background-color: #d4edda; font-weight: bold;" value="{{ number_format($order->order_total, 2, '.', '') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 30px;">
            <div class="col-md-12">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('pharmacy.orders.index') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Regresar
                    </a>
                    
                    <div>
                        @if($order->status->value === 'cotizacion')
                            <button type="button" class="btn btn-success btn-lg" onclick="respondQuote()">
                                <i class="fa fa-paper-plane"></i> Responder Cotización
                            </button>
                        @elseif($order->status->value === 'confirmado')
                            <button type="button" class="btn btn-success btn-lg" onclick="confirmAndPrepare()">
                                @if($order->isElectronicPayment() && $order->voucher)
                                    <i class="fa fa-check-circle"></i> Confirmar y Preparar
                                @elseif($order->isElectronicPayment() && !$order->voucher)
                                    <i class="fa fa-money"></i> Confirmar Pago Efectivo
                                @else
                                    <i class="fa fa-check-circle"></i> Confirmar y Preparar
                                @endif
                            </button>
                        @elseif($order->status->value === 'preparando')
                            <button type="button" class="btn btn-warning btn-lg" onclick="markAsDispatched()">
                                <i class="fa fa-truck"></i> Marcar como Despachada
                            </button>
                        @elseif(in_array($order->status->value, ['esperando_confirmacion', 'despachado', 'cancelado']))
                            <button type="submit" class="btn btn-default" onclick="return confirm('¿Está seguro de actualizar esta orden?')">
                                <i class="fa fa-save"></i> Guardar Cambios
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary" onclick="return confirm('¿Está seguro de actualizar esta orden?')">
                                <i class="fa fa-save"></i> Actualizar Orden
                            </button>
                        @endif
                    </div>
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
        const orderTotalInput = document.getElementById('order_total');
        const productsDisplayInput = document.getElementById('products_total_display');
        const subtotalInput = document.getElementById('products_subtotal');
        const totalIvaInput = document.getElementById('total_iva');

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
            const ivaInput = fila.querySelector('.iva-percentage');
            const filaSubtotalInput = fila.querySelector('.subtotal');

            if (!cantidadInput || !precioInput || !ivaInput || !filaSubtotalInput) {
                return { subtotal: 0, iva: 0, total: 0 };
            }

            // Obtener la cantidad solicitada desde la celda correspondiente
            const cantidadSolicitadaCell = fila.cells[2];
            const labelElement = cantidadSolicitadaCell.querySelector('.label');
            const cantidadSolicitadaText = labelElement ? labelElement.textContent : cantidadSolicitadaCell.textContent;
            const cantidadSolicitada = parseFloat(cantidadSolicitadaText.trim()) || 0;

            const cantidadDisponible = parseFloat(cantidadInput.value) || 0;
            const precio = parseFloat(precioInput.value) || 0;
            const ivaPercentage = parseFloat(ivaInput.value) || 0;

            // Validar que la cantidad disponible no exceda la solicitada
            if (cantidadDisponible > cantidadSolicitada) {
                cantidadInput.value = cantidadSolicitada;
                showAlert('La cantidad disponible no puede ser mayor a la solicitada', 'warning');
            }

            // Usar la menor cantidad entre solicitada y disponible
            const cantidadParaCalculo = Math.min(cantidadSolicitada, cantidadDisponible);

            // Calcular subtotal sin IVA
            const subtotalSinIva = cantidadParaCalculo * precio;
            
            // Calcular IVA individual para este producto
            const ivaAmount = subtotalSinIva * (ivaPercentage / 100);
            
            // Total con IVA para este producto
            const totalConIva = subtotalSinIva + ivaAmount;

            // Mostrar el total con IVA en la celda
            filaSubtotalInput.value = formatCurrency(totalConIva);
            
            return { 
                subtotal: subtotalSinIva, 
                iva: ivaAmount, 
                total: totalConIva 
            };
        }

        function calcularTotales() {
            // Si la orden está en preparando o posterior, usar valores de BD
            const orderStatus = '{{ $order->status->value }}';
            if (['preparando', 'despachado', 'cancelado'].includes(orderStatus)) {
                // Usar valores de la base de datos
                const dbSubtotal = {{ $order->products_subtotal ?? 0 }};
                const dbIvaTotal = {{ $order->iva_total ?? 0 }};
                const dbProductsTotal = {{ $order->products_total ?? 0 }};
                const dbShippingCost = {{ $order->shipping_cost ?? 0 }};
                const dbOrderTotal = {{ $order->order_total ?? 0 }};

                // Actualizar campos con valores de BD
                if (subtotalInput) subtotalInput.value = dbSubtotal.toFixed(2);
                if (totalIvaInput) totalIvaInput.value = dbIvaTotal.toFixed(2);
                if (productsDisplayInput) productsDisplayInput.value = dbProductsTotal.toFixed(2);
                if (orderTotalInput) orderTotalInput.value = dbOrderTotal.toFixed(2);
                return;
            }

            // Lógica de cálculo normal para estados editables
            let subtotalGeneral = 0;
            let totalIvaGeneral = 0;
            let totalConIvaGeneral = 0;

            // Calcular desde los detalles de la tabla
            document.querySelectorAll('tbody tr').forEach(fila => {
                const cantidadInput = fila.querySelector('.quantity');
                const precioInput = fila.querySelector('.price');
                const ivaInput = fila.querySelector('.iva-percentage');
                const filaSubtotalInput = fila.querySelector('.subtotal');

                if (cantidadInput && precioInput && ivaInput && filaSubtotalInput) {
                    // Obtener la cantidad solicitada desde la celda correspondiente
                    const cantidadSolicitadaCell = fila.cells[2];
                    const labelElement = cantidadSolicitadaCell.querySelector('.label');
                    const cantidadSolicitadaText = labelElement ? labelElement.textContent : cantidadSolicitadaCell.textContent;
                    const cantidadSolicitada = parseFloat(cantidadSolicitadaText.trim()) || 0;

                    const cantidadDisponible = parseFloat(cantidadInput.value) || 0;
                    const precio = parseFloat(precioInput.value) || 0;
                    const ivaPercentage = parseFloat(ivaInput.value) || 0;

                    // Validar que la cantidad disponible no exceda la solicitada
                    if (cantidadDisponible > cantidadSolicitada) {
                        cantidadInput.value = cantidadSolicitada;
                        showAlert('La cantidad disponible no puede ser mayor a la solicitada', 'warning');
                    }

                    // Usar la menor cantidad entre solicitada y disponible
                    const cantidadParaCalculo = Math.min(cantidadSolicitada, cantidadDisponible);

                    // Calcular subtotal sin IVA
                    const subtotalSinIva = cantidadParaCalculo * precio;
                    
                    // Calcular IVA individual para este producto
                    const ivaAmount = subtotalSinIva * (ivaPercentage / 100);
                    
                    // Total con IVA para este producto
                    const totalConIva = subtotalSinIva + ivaAmount;

                    // Mostrar el total con IVA en la celda
                    filaSubtotalInput.value = formatCurrency(totalConIva);
                    
                    // Sumar a los totales generales
                    subtotalGeneral += subtotalSinIva;
                    totalIvaGeneral += ivaAmount;
                    totalConIvaGeneral += totalConIva;
                }
            });

            // Obtener costo de envío
            const shipping = parseFloat(shippingInput?.value) || 0;
            const totalFinal = totalConIvaGeneral + shipping;

            // Actualizar todos los campos
            if (subtotalInput) subtotalInput.value = subtotalGeneral.toFixed(2);
            if (totalIvaInput) totalIvaInput.value = totalIvaGeneral.toFixed(2);
            if (productsDisplayInput) productsDisplayInput.value = totalConIvaGeneral.toFixed(2);
            if (orderTotalInput) orderTotalInput.value = totalFinal.toFixed(2);
        }

        function updateTotalBadges(subtotal, totalIVA, totalConIVA, totalFinal) {
            // Función mantenida para compatibilidad
        }

        function showAlert(message, type = 'info') {
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
            // Solo agregar listeners si la orden es editable
            const orderStatus = '{{ $order->status->value }}';
            const isEditable = !['preparando', 'despachado', 'cancelado'].includes(orderStatus);

            if (isEditable) {
                // Event listeners para cantidad, precio e IVA
                document.querySelectorAll('.quantity, .price, .iva-percentage').forEach(input => {
                    input.addEventListener('input', function() {
                        // Validar valores negativos
                        if (parseFloat(this.value) < 0) {
                            this.value = 0;
                        }
                        
                        // Validar IVA máximo 100%
                        if (this.classList.contains('iva-percentage') && parseFloat(this.value) > 100) {
                            this.value = 100;
                            showAlert('El IVA no puede ser mayor al 100%', 'warning');
                        }
                        
                        calcularTotales();
                    });

                    input.addEventListener('blur', function() {
                        // Formatear valor al perder el foco
                        if (this.classList.contains('price') && this.value) {
                            this.value = parseFloat(this.value).toFixed(2);
                        }
                        if (this.classList.contains('iva-percentage') && this.value) {
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
        }

        // Inicializar event listeners
        initializeEventListeners();
        
        // Calcular totales al cargar la página
        calcularTotales();
        // Validación antes del submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const hasProducts = document.querySelectorAll('.quantity').length > 0;
            const hasValidQuantities = Array.from(document.querySelectorAll('.quantity')).some(input => parseFloat(input.value) > 0);

            if (hasProducts && !hasValidQuantities) {
                e.preventDefault();
                showAlert('Debe especificar al menos una cantidad disponible mayor a 0', 'error');
                return false;
            }

            // Validar precios e IVA para productos con cantidad
            const quantities = document.querySelectorAll('.quantity');
            const prices = document.querySelectorAll('.price');
            const ivas = document.querySelectorAll('.iva-percentage');
            
            for (let i = 0; i < quantities.length; i++) {
                const quantity = parseFloat(quantities[i].value) || 0;
                const price = parseFloat(prices[i].value) || 0;
                const iva = parseFloat(ivas[i].value);
                
                if (quantity > 0) {
                    if (price <= 0) {
                        e.preventDefault();
                        showAlert('Todos los productos con cantidad disponible deben tener un precio mayor a 0', 'error');
                        prices[i].focus();
                        return false;
                    }
                    if (isNaN(iva) || iva < 0) {
                        e.preventDefault();
                        showAlert('Todos los productos deben tener un porcentaje de IVA válido (0% o mayor)', 'error');
                        ivas[i].focus();
                        return false;
                    }
                }
            }
        });

        // Función para responder cotización
        window.respondQuote = function() {
            if (!confirm('¿Está seguro de enviar esta cotización al usuario? Esta acción cambiará el estado a "Esperando Confirmación".')) {
                return;
            }

            // Validar que hay cantidades, precios e IVA válidos
            const quantities = document.querySelectorAll('.quantity');
            const prices = document.querySelectorAll('.price');
            const ivas = document.querySelectorAll('.iva-percentage');
            let hasValidData = false;

            for (let i = 0; i < quantities.length; i++) {
                const quantity = parseFloat(quantities[i].value) || 0;
                const price = parseFloat(prices[i].value) || 0;
                const iva = parseFloat(ivas[i].value);

                if (quantity > 0 && price > 0 && !isNaN(iva) && iva >= 0) {
                    hasValidData = true;
                    break;
                }
            }

            if (!hasValidData) {
                showAlert('Debe especificar al menos una cantidad disponible, precio mayor a 0 y porcentaje de IVA válido', 'error');
                return;
            }

            // Cambiar la acción del formulario para responder cotización
            const form = document.getElementById('order-main-form');
            form.action = '{{ route("pharmacy.orders.respond-quote", $order) }}';

            // Eliminar cualquier input _method existente
            const oldMethodInput = form.querySelector('input[name="_method"]');
            if (oldMethodInput) {
                oldMethodInput.remove();
            }
            // Crear y agregar el input _method con valor PUT
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            form.submit();
        };

        // Función para confirmar y preparar orden
        window.confirmAndPrepare = function() {
            // Validar que el shipping_cost esté establecido si la orden requiere envío
            const shippingInput = document.getElementById('shipping_cost');
            const requiresShipping = {{ $order->requiresShipping() ? 'true' : 'false' }};
            
            if (requiresShipping && shippingInput && (!shippingInput.value || parseFloat(shippingInput.value) < 0)) {
                showAlert('Debe establecer un costo de envío válido antes de confirmar', 'error');
                return;
            }

            const isElectronic = {{ $order->isElectronicPayment() ? 'true' : 'false' }};
            const hasVoucher = {{ $order->voucher ? 'true' : 'false' }};
            
            let confirmMessage = '¿Confirmar el pago y cambiar el estado a Preparando?';
            
            if (isElectronic && hasVoucher) {
                confirmMessage = '⚠️ IMPORTANTE: Antes de continuar, asegúrese de haber revisado el comprobante de pago.\n\n¿Confirmar el pago y cambiar el estado a Preparando?';
            } else if (isElectronic && !hasVoucher) {
                confirmMessage = '¿Confirmar como pago efectivo y cambiar el estado a Preparando?';
            }

            if (!confirm(confirmMessage)) {
                return;
            }

            // Cambiar la acción del formulario para confirmar pago
            const form = document.getElementById('order-main-form');
            form.action = '{{ route("pharmacy.orders.confirm-payment", $order) }}';

            // Eliminar cualquier input _method existente
            const oldMethodInput = form.querySelector('input[name="_method"]');
            if (oldMethodInput) {
                oldMethodInput.remove();
            }
            // Crear y agregar el input _method con valor PUT
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            form.submit();
        };

        // Función para marcar como despachada
        window.markAsDispatched = function() {
            if (!confirm('¿Está seguro de marcar esta orden como despachada?')) {
                return;
            }

            // Cambiar la acción del formulario para marcar como despachada
            const form = document.getElementById('order-main-form');
            form.action = '{{ route("pharmacy.orders.mark-dispatched", $order) }}';

            // Eliminar cualquier input _method existente
            const oldMethodInput = form.querySelector('input[name="_method"]');
            if (oldMethodInput) {
                oldMethodInput.remove();
            }
            // Crear y agregar el input _method con valor PUT
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            form.submit();
        };
    });
</script>
@endpush