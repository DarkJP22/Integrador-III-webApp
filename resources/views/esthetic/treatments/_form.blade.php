{{-- <treatment-view :treatment="{{ json_encode($treatment) }}" :product="{{ json_encode($selectedProduct) }}" inline-template>
    <div>
        <div class="form-group">
            <label for="category" class="col-sm-2 control-label">Categoria</label>
            <div class="col-sm-10">
                <select class="form-control" style="width: 100%;" name="category" required>

                    <option value="facial"
                        {{ isset($treatment) && $treatment->category == 'facial' ? 'selected' : (old('category') == 'facial' ? 'selected' : '') }}>
                        Facial</option>
                    <option value="corporal"
                        {{ isset($treatment) && $treatment->category == 'corporal' ? 'selected' : (old('category') == 'corporal' ? 'selected' : '') }}>
                        Corporal</option>
                    <option value="depilacion"
                        {{ isset($treatment) && $treatment->category == 'depilacion' ? 'selected' : (old('category') == 'depilacion' ? 'selected' : '') }}>
                        Depilación</option>

                </select>
                @if ($errors->has('category'))
                    <span class="help-block">
                        <strong>{{ $errors->first('category') }}</strong>
                    </span>
                @endif

            </div>
        </div>
        <div class="form-group">
            <label for="product_id" class="col-sm-2 control-label">Producto</label>

            <div class="col-sm-10">
                 <v-select 
                    @search="onSearch"
                    :options="items"
                    placeholder="Buscar Producto..."
                    label="name"
                    @input="selectItem"
                    :value="selectedProduct"
                    
                    >

                    <template slot="no-options">
                        Escribe para buscar los productos
                    </template>
                
                </v-select>
                <input type="hidden" class="form-control" name="product_id" 
                    value="{{ isset($treatment) ? $treatment->product_id : old('product_id') }}" v-model="selectedProductId">
            </div>
        </div>
       
        
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Nombre</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" placeholder="Nombre"
                    value="{{ isset($treatment->name) ? $treatment->name : old('name') }}" v-model="productName" required>
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="form-group">
            <label for="price" class="col-sm-2 control-label">Precio</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="price" placeholder=""
                    value="{{ isset($treatment->price) ? $treatment->price : old('price', 0) }}" v-model="productPrice">
                @if ($errors->has('price'))
                    <span class="help-block">
                        <strong>{{ $errors->first('price') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="form-group">
            <label for="discount" class="col-sm-2 control-label">% Descuento</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="discount" placeholder="%"
                    value="{{ isset($treatment->discount) ? $treatment->discount : old('discount', 0) }}">
                @if ($errors->has('discount'))
                    <span class="help-block">
                        <strong>{{ $errors->first('discount') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="tax" class="col-sm-2 control-label">% Impuesto</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="tax" placeholder="%"
                    value="{{ isset($treatment->tax) ? $treatment->tax : old('tax', 0) }}" v-model="productTax">
                @if ($errors->has('tax'))
                    <span class="help-block">
                        <strong>{{ $errors->first('tax') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <input type="hidden" class="form-control" name="CodigoMoneda" 
                    value="{{ isset($treatment) ? $treatment->CodigoMoneda : 'CRC' }}" v-model="productMoneda">

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</treatment-view> --}}
<treatment-view :treatment="{{ json_encode($treatment) }}" :product="{{ json_encode($selectedProduct) }}"></treatment-view>
