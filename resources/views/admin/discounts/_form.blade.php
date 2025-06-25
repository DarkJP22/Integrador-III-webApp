

 <div class="form-group">
    <label for="name" class="col-sm-2 control-label">Nombre</label>
    <div class="col-sm-10">
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" name="name" placeholder="" value="{{ isset($discount) ? $discount->name : old('name') }}">
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
       
        <input type="text" class="form-control {{ $errors->has('tarifa') ? ' is-invalid' : '' }}" id="tarifa" name="tarifa" placeholder="Ej: 13" value="{{ isset($discount) ? $discount->tarifa : old('tarifa') }}">
            
       
        @if ($errors->has('tarifa'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('tarifa') }}</strong>
            </div>
        @endif
    </div>
  </div>

  <div class="form-group">
    <label for="available" class="col-sm-2 control-label">Disponibilidad</label>
    <div class="col-sm-3">
       <label>
          <input type="radio" class="minimal" name="to" value="clinica" {{ isset($discount) && $discount->to == 'clinica' ? 'checked' : ''}}>
          Para Médicos y Clínicas
        </label>
        </div>
    
    <div class="col-sm-3">
        <label>
          <input type="radio" class="minimal" name="to" value="farmacia" {{ isset($discount) && $discount->to == 'farmacia' ? 'checked' : ''}}>
          Para Farmacias
        </label>
    </div>
  </div>

  
  
   @if(isset($discount))
        @can('update', $discount)
            <button type="submit" class="btn btn-primary">Guardar</button>
        @endcan
    @else 
        <button type="submit" class="btn btn-primary">Guardar</button>
    @endif
    <a href="/admin/discounts" class="btn btn-default"> Regresar</a>
</div>
