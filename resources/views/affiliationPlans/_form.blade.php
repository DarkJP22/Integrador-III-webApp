
<div class="form-group">
    <label for="office_name" class="col-sm-2 control-label">Nombre</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" name="name" placeholder="Nombre" value="{{ isset($plan) ? $plan->name : old('name') }}" required>
        @if ($errors->has('name'))
        <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="cuota" class="col-sm-2 control-label">Cuota</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" name="cuota" placeholder="Cuota" value="{{ isset($plan) ? $plan->cuota : old('cuota') }}">
        @if ($errors->has('cuota'))
        <span class="help-block">
            <strong>{{ $errors->first('cuota') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="discount" class="col-sm-4 control-label">% Desc servicio Médico</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" name="discount" placeholder="Descuento" value="{{ isset($plan) ? $plan->discount : old('discount') }}">
        @if ($errors->has('discount'))
        <span class="help-block">
            <strong>{{ $errors->first('discount') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="discount_lab" class="col-sm-4 control-label">% Desc laboratorio</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" name="discount_lab" placeholder="Descuento" value="{{ isset($plan) ? $plan->discount_lab : old('discount_lab') }}">
        @if ($errors->has('discount_lab'))
        <span class="help-block">
            <strong>{{ $errors->first('discount_lab') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="period" class="col-sm-2 control-label">Periodo (M)</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" name="period" placeholder="Periodo" value="{{ isset($plan) ? $plan->period : old('period') }}">
        @if ($errors->has('period'))
        <span class="help-block">
            <strong>{{ $errors->first('period') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="persons" class="col-sm-2 control-label">Personas</label>

    <div class="col-sm-10">
        <input type="number" class="form-control" name="persons" placeholder="Personas" value="{{ isset($plan) ? $plan->persons : old('persons') }}" min="1">
        @if ($errors->has('persons'))
        <span class="help-block">
            <strong>{{ $errors->first('persons') }}</strong>
        </span>
        @endif
    </div>
</div>

