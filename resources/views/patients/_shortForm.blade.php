<div class="form-group">
    <label for="tipo_identificacion" class="col-sm-2 control-label">Identificación</label>

    <div class="col-sm-10">
        <select class="form-control select2" style="width: 100%;" name="tipo_identificacion" required>
        <option value=""></option>
        <option value="01" {{  (old('tipo_identificacion') == '01' ? 'selected' : 'selected') }}>Cédula Física</option>
        <option value="02" {{  (old('tipo_identificacion') == '02' ? 'selected' : '') }}>Cédula Jurídica</option>
        <option value="03" {{  (old('tipo_identificacion') == '03' ? 'selected' : '') }}>DIMEX</option>
        <option value="04" {{  (old('tipo_identificacion') == '04' ? 'selected' : '') }}>NITE</option>
      </select>
      @if ($errors->has('tipo_identificacion'))
        <span class="help-block">
            <strong>{{ $errors->first('tipo_identificacion') }}</strong>
        </span>
        @endif
      
      </div>
      
  </div>
<div class="form-group">
    <label for="identificacion" class="col-sm-2 control-label">Cédula</label>

    <div class="col-sm-10">
        
        <input type="text" class="form-control" name="identificacion" placeholder="Cédula" value="{{ old('identificacion') }}" required>
        @if ($errors->has('identificacion'))
        <span class="help-block">
            <strong>{{ $errors->first('identificacion') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="office_name" class="col-sm-2 control-label">Nombre Completo</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" name="name" placeholder="Nombre" value="{{ old('name') }}" required>
        @if ($errors->has('name'))
        <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>



<div class="form-group">
    <label for="phone" class="col-sm-2 control-label">Teléfono</label>

    {{-- <div class="col-sm-3">
        <select class="form-control" style="width: 100%;" name="phone_country_code" required>

            <option value="+506" {{ (old('phone_country_code') == '+506' ? 'selected' : '') }}>+506</option>

        </select>

        @if ($errors->has('phone_country_code'))
        <span class="help-block">
            <strong>{{ $errors->first('phone_country_code') }}</strong>
        </span>
        @endif
    </div> --}}
    <div class="col-sm-7">
        <input type="text" class="form-control" name="phone" placeholder="Celular" value="{{ old('phone') }}" required>
        @if ($errors->has('phone'))
        <span class="help-block">
            <strong>{{ $errors->first('phone') }}</strong>
        </span>
        @endif
    </div>
</div>

