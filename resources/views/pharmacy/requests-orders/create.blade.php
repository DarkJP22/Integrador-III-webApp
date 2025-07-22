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

    .col-md-4,
    .col-md-6,
    .col-md-12 {
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
    <h1>Crear Nueva Orden</h1>
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

    <form action="{{ route('pharmacy.orders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Información de la Orden</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Consecutivo</label>
                        <input type="text" class="form-control @error('consecutive') is-invalid @enderror"
                            name="consecutive" value="{{ old('consecutive', 'ORD-' . date('Ymd') . '-' . rand(1000, 9999)) }}" required>
                        @error('consecutive')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="pharmacy_id" value="{{ auth()->user()->pharmacy->id ?? 1 }}">

                    <div class="form-group col-md-4">
                        <label>Usuario/Cliente</label>
                        <select class="form-control @error('user_id') is-invalid @enderror" name="user_id" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach(\App\User::limit(20)->get() as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Fecha</label>
                        <input type="datetime-local" class="form-control @error('date') is-invalid @enderror"
                            name="date" value="{{ old('date', date('Y-m-d\TH:i')) }}" required>
                        @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Estado</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
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
                        <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required onchange="toggleVoucherSection()">
                            @foreach($paymentMethodOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('payment_method') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Sección del voucher SINPE -->
                <div id="voucher_section" class="row" style="display: none;">
                    <div class="form-group col-md-12">
                        <label>Comprobante de Pago SINPE</label>
                        <input type="file" class="form-control @error('voucher') is-invalid @enderror"
                            name="voucher" accept="image/*" onchange="previewVoucher(this)">
                        <small class="form-text text-muted">Subir imagen del comprobante de pago (JPG, PNG, etc.)</small>
                        @error('voucher')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <!-- Preview de la imagen -->
                        <div id="voucher_preview" style="margin-top: 10px; display: none;">
                            <img id="preview_image" src="" alt="Vista previa" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>¿Requiere envío?</label>
                        <select name="requires_shipping" class="form-control @error('requires_shipping') is-invalid @enderror" required onchange="toggleShippingSection()">
                            @foreach($shippingRequiredOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('requires_shipping') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('requires_shipping')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de envío -->
        <div id="shipping_section" class="box box-info" style="margin-top: 20px; display: none;">
            <div class="box-header with-border">
                <h3 class="box-title">Información de Envío</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Dirección de Envío</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                            rows="3" placeholder="Dirección completa para el envío">{{ old('address') }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Latitud (opcional)</label>
                        <input type="number" class="form-control @error('lat') is-invalid @enderror"
                            name="lat" step="any" value="{{ old('lat') }}">
                        @error('lat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label>Longitud (opcional)</label>
                        <input type="number" class="form-control @error('lot') is-invalid @enderror"
                            name="lot" step="any" value="{{ old('lot') }}">
                        @error('lot')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Costo de Envío</label>
                        <input type="number" class="form-control @error('shipping_total') is-invalid @enderror"
                            name="shipping_total" step="0.01" value="{{ old('shipping_total', '0') }}"
                            onchange="calculateGrandTotal()">
                        @error('shipping_total')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos/Medicamentos -->
        <div class="box box-success" style="margin-top: 20px;">
            <div class="box-header with-border">
                <h3 class="box-title">Productos de la Orden</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-success btn-sm" onclick="addProduct()">
                        <i class="fa fa-plus"></i> Agregar Producto
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div id="products_section">
                    <div class="product-item" data-index="0" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 4px;">
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label>Medicamento/Producto</label>
                                <select class="form-control" name="details[0][drug_id]">
                                    <option value="">Seleccionar medicamento</option>
                                    @foreach(\App\Drug::limit(50)->get() as $drug)
                                    <option value="{{ $drug->id }}">{{ $drug->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Cantidad Solicitada</label>
                                <input type="number" class="form-control" name="details[0][requested_amount]"
                                    min="1" value="1" onchange="calculateProductTotal(0)">
                            </div>
                            <div class="form-group col-md-2">
                                <label>Cantidad Disponible</label>
                                <input type="number" class="form-control" name="details[0][quantity_available]"
                                    min="0" value="1" onchange="calculateProductTotal(0)">
                            </div>
                            <div class="form-group col-md-2">
                                <label>Precio Unitario</label>
                                <input type="number" class="form-control" name="details[0][unit_price]"
                                    step="0.01" min="0" value="1000" onchange="calculateProductTotal(0)">
                            </div>
                            <div class="form-group col-md-1">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-block" onclick="removeProduct(0)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Subtotal: ₡<span id="product_total_0">1000.00</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Totales -->
        <div class="box box-warning" style="margin-top: 20px;">
            <div class="box-header with-border">
                <h3 class="box-title">Resumen de Totales</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>SubTotal de Productos</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" class="form-control" id="products_total"
                                step="0.01" value="{{ old('products_total', '1000') }}" readonly
                                style="background-color: #f4f4f4;">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Total de Orden + IVA (13%)</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" class="form-control @error('order_total') is-invalid @enderror"
                                name="order_total" id="order_total" step="0.01" value="{{ old('order_total', '1130') }}"
                                required readonly style="background-color: #f4f4f4;">
                        </div>
                        @error('order_total')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Costo de Envío</label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" class="form-control @error('shipping_cost') is-invalid @enderror"
                                name="shipping_cost" id="shipping_cost" step="0.01" value="{{ old('shipping_cost', '0') }}"
                                onchange="calculateGrandTotal()">
                        </div>
                        @error('shipping_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label><strong>Total Final con Envío</strong></label>
                        <div class="input-group">
                            <span class="input-group-addon">₡</span>
                            <input type="number" class="form-control @error('shipping_total') is-invalid @enderror"
                                name="shipping_total" id="shipping_total" step="0.01" value="{{ old('shipping_total', '1130') }}"
                                required readonly style="background-color: #d4edda; font-weight: bold;">
                        </div>
                        @error('shipping_total')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Resumen visual 
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h4><i class="fa fa-calculator"></i> Desglose de Totales:</h4>
                            <ul style="margin-bottom: 0;">
                                <li>SubTotal Productos: <strong>₡<span id="summary_subtotal">1,000.00</span></strong></li>
                                <li>IVA (13%): <strong>₡<span id="summary_iva">130.00</span></strong></li>
                                <li>Costo de Envío: <strong>₡<span id="summary_shipping">0.00</span></strong></li>
                                <li style="border-top: 1px solid #ddd; padding-top: 5px; margin-top: 5px;">
                                    <strong>TOTAL FINAL: ₡<span id="summary_total">1,130.00</span></strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="box" style="margin-top: 20px;">
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa fa-save"></i> Crear Orden
                </button>
                <a href="{{ route('pharmacy.orders.index') }}" class="btn btn-default btn-lg">
                    <i class="fa fa-arrow-left"></i> Volver a Órdenes
                </a>
            </div>
        </div>
    </form>
</section>

<script>
    let productIndex = 1;
    const IVA_RATE = 0.13; // 13% IVA

    function toggleVoucherSection() {
        const paymentMethod = document.querySelector('select[name="payment_method"]').value;
        const voucherSection = document.getElementById('voucher_section');

        if (paymentMethod == '1') {
            voucherSection.style.display = 'block';
        } else {
            voucherSection.style.display = 'none';
            document.querySelector('input[name="voucher"]').value = '';
            document.getElementById('voucher_preview').style.display = 'none';
        }
    }

    function previewVoucher(input) {
        const preview = document.getElementById('voucher_preview');
        const previewImage = document.getElementById('preview_image');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }

    function toggleShippingSection() {
        const requiresShipping = document.querySelector('select[name="requires_shipping"]').value;
        const shippingSection = document.getElementById('shipping_section');

        if (requiresShipping == '1') {
            shippingSection.style.display = 'block';
        } else {
            shippingSection.style.display = 'none';
            document.querySelector('textarea[name="address"]').value = '';
            document.querySelector('input[name="lat"]').value = '';
            document.querySelector('input[name="lot"]').value = '';
            document.querySelector('input[name="shipping_cost"]').value = '0';
            calculateGrandTotal();
        }
    }

    function addProduct() {
        const productsSection = document.getElementById('products_section');
        const newProduct = `
        <div class="product-item" data-index="${productIndex}" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 4px;">
            <div class="row">
                <div class="form-group col-md-5">
                    <label>Medicamento/Producto</label>
                    <select class="form-control" name="details[${productIndex}][drug_id]">
                        <option value="">Seleccionar medicamento</option>
                        @foreach(\App\Drug::limit(50)->get() as $drug)
                            <option value="{{ $drug->id }}">{{ $drug->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Cantidad Solicitada</label>
                    <input type="number" class="form-control" name="details[${productIndex}][requested_amount]" 
                        min="1" value="1" onchange="calculateProductTotal(${productIndex})">
                </div>
                <div class="form-group col-md-2">
                    <label>Cantidad Disponible</label>
                    <input type="number" class="form-control" name="details[${productIndex}][quantity_available]" 
                        min="0" value="1" onchange="calculateProductTotal(${productIndex})">
                </div>
                <div class="form-group col-md-2">
                    <label>Precio Unitario</label>
                    <input type="number" class="form-control" name="details[${productIndex}][unit_price]" 
                        step="0.01" min="0" value="1000" onchange="calculateProductTotal(${productIndex})">
                </div>
                <div class="form-group col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-block" onclick="removeProduct(${productIndex})">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <strong>Subtotal: ₡<span id="product_total_${productIndex}">1000.00</span></strong>
                </div>
            </div>
        </div>`;

        productsSection.insertAdjacentHTML('beforeend', newProduct);
        productIndex++;
        calculateOrderTotal();
    }

    function removeProduct(index) {
        const productItem = document.querySelector(`[data-index="${index}"]`);
        if (productItem) {
            productItem.remove();
            calculateOrderTotal();
        }
    }

    function calculateProductTotal(index) {
        const quantityAvailable = parseFloat(document.querySelector(`input[name="details[${index}][quantity_available]"]`).value) || 0;
        const unitPrice = parseFloat(document.querySelector(`input[name="details[${index}][unit_price]"]`).value) || 0;
        const total = quantityAvailable * unitPrice;

        document.getElementById(`product_total_${index}`).textContent = total.toLocaleString('es-CR', {
            minimumFractionDigits: 2
        });
        calculateOrderTotal();
    }

    function calculateOrderTotal() {
        let subtotal = 0;
        document.querySelectorAll('.product-item').forEach((item, index) => {
            const quantityAvailable = parseFloat(item.querySelector(`input[name*="[quantity_available]"]`).value) || 0;
            const unitPrice = parseFloat(item.querySelector(`input[name*="[unit_price]"]`).value) || 0;
            subtotal += quantityAvailable * unitPrice;
        });

        // Calcular total con IVA
        const totalConIVA = subtotal * (1 + IVA_RATE);
        const iva = subtotal * IVA_RATE;

        // Actualizar campos de subtotal y total con IVA
        document.getElementById('products_total').value = subtotal.toFixed(2);
        document.getElementById('order_total').value = totalConIVA.toFixed(2);

        // Actualizar resumen visual
        document.getElementById('summary_subtotal').textContent = subtotal.toLocaleString('es-CR', {
            minimumFractionDigits: 2
        });
        document.getElementById('summary_iva').textContent = iva.toLocaleString('es-CR', {
            minimumFractionDigits: 2
        });

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        const orderTotal = parseFloat(document.getElementById('order_total').value) || 0;
        const shippingCost = parseFloat(document.querySelector('input[name="shipping_cost"]').value) || 0;
        const grandTotal = orderTotal + shippingCost;

        // Actualizar campo total final
        document.getElementById('shipping_total').value = grandTotal.toFixed(2);

        // Actualizar resumen visual
        document.getElementById('summary_shipping').textContent = shippingCost.toLocaleString('es-CR', {
            minimumFractionDigits: 2
        });
        document.getElementById('summary_total').textContent = grandTotal.toLocaleString('es-CR', {
            minimumFractionDigits: 2
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        toggleVoucherSection();
        toggleShippingSection();
        calculateOrderTotal();
    });
</script>
@endsection