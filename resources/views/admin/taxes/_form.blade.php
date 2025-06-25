{{-- <tax-view :tax="{{ json_encode(isset($tax) ? $tax : false) }}" :codes-taxes="{{ json_encode($codesTaxes) }}" :codes-tarifa-Iva="{{ json_encode($codesTarifaIVA) }}" inline-template>
<div>
<div class="form-group">
            
        <label for="code" class="col-sm-2 control-label">Código</label>
        <div class="col-sm-10">
            <select class="form-control custom-select {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" id="code" >
                
                @foreach($codesTaxes as $codeTax)    
                    <option value="{{ $codeTax['code'] }}" {{ isset($tax) && $tax->code == $codeTax['code'] ? 'selected' : (old('code') == $codeTax['code'] ? 'selected' : '')  }}>{{ $codeTax['code'] }} - {{  $codeTax['name'] }}</option>
                @endforeach
            </select>
                
            @if ($errors->has('code'))
                <div class="error invalid-feedback">
                    <strong>{{ $errors->first('code') }}</strong>
                </div>
            @endif
        </div>
            
</div>

<div class="form-group">
            
        <label for="CodigoTarifa" class="col-sm-2 control-label">Código de Tarifa</label>
        <div class="col-sm-10">
            <select class="form-control custom-select {{ $errors->has('CodigoTarifa') ? ' is-invalid' : '' }}" name="CodigoTarifa" id="CodigoTarifa" v-model="CodigoTarifa" @change="onChangeCodigoTarifa" >
                
                @foreach($codesTarifaIVA as $codeTarifa)    
                    <option value="{{ $codeTarifa['code'] }}" {{ isset($tax) && $tax->CodigoTarifa == $codeTarifa['code'] ? 'selected' : (old('CodigoTarifa') == $codeTarifa['code'] ? 'selected' : '')  }}>{{ $codeTarifa['code'] }} - {{  $codeTarifa['name'] }}</option>
                @endforeach
            </select>
                
            @if ($errors->has('CodigoTarifa'))
                <div class="error invalid-feedback">
                    <strong>{{ $errors->first('CodigoTarifa') }}</strong>
                </div>
            @endif
        </div>
</div>

 <div class="form-group">
    <label for="name" class="col-sm-2 control-label">Nombre</label>
    <div class="col-sm-10">
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" name="name" placeholder="" value="{{ isset($tax) ? $tax->name : old('name') }}">
        @if ($errors->has('name'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>
  </div>
<div class="form-group">
    <label for="tarifa" class="col-sm-2 control-label">% Tarifa</label>
    <div class="col-sm-10">
       
        <input type="text" class="form-control {{ $errors->has('tarifa') ? ' is-invalid' : '' }}" id="tarifa" name="tarifa" placeholder="Ej: 13" value="{{ isset($tax) ? $tax->tarifa : old('tarifa') }}" v-model="tarifa">
            
       
        @if ($errors->has('tarifa'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('tarifa') }}</strong>
            </div>
        @endif
    </div>
  </div>

  
  
   @if(isset($tax))
        @can('update', $tax)
            <button type="submit" class="btn btn-primary">Guardar</button>
        @endcan
    @else 
        <button type="submit" class="btn btn-primary">Guardar</button>
    @endif
    <a href="/taxes" class="btn btn-default"> Regresar</a>
</div>
</tax-view> --}}
<tax-view :tax="{{ json_encode(isset($tax) ? $tax : false) }}" :codes-taxes="{{ json_encode($codesTaxes) }}" :codes-tarifa-Iva="{{ json_encode($codesTarifaIVA) }}"></tax-view>