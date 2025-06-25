<div class="form-group">
    <label for="title" class="col-sm-2 control-label">Nombre</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" name="title" value="{{ isset($plan) ? $plan->title : old('title') }}"
               required>
        @if ($errors->has('title'))
            <span class="help-block">
              <strong>{{ $errors->first('title') }}</strong>
          </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="cost" class="col-sm-2 control-label">Costo</label>

    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">₡</span>
            <input type="text" class="form-control" name="cost" value="{{ isset($plan) ? $plan->cost : old('cost') }}"
                   required>
        </div>

        @if ($errors->has('cost'))
            <span class="help-block">
              <strong>{{ $errors->first('cost') }}</strong>
          </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="quantity" class="col-sm-2 control-label">Meses</label>

    <div class="col-sm-10">
        <input type="number" class="form-control" name="quantity" min="1"
               value="{{ isset($plan) ? $plan->quantity : old('quantity') }}" required>
        @if ($errors->has('quantity'))
            <span class="help-block">
              <strong>{{ $errors->first('quantity') }}</strong>
          </span>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="description" class="col-sm-2 control-label">Descripción</label>

    <div class="col-sm-10">
        <textarea class="form-control" name="description" id="description" cols="30"
                  rows="10">{{ isset($plan) ? $plan->description : old('description') }}</textarea>

        @if ($errors->has('description'))
            <span class="help-block">
              <strong>{{ $errors->first('description') }}</strong>
          </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="include_fe" class="col-sm-2 control-label">Incluye Factura eléctronica</label>

    <div class="col-sm-10">
        <select class="form-control select2" style="width: 100%;" name="include_fe" required>
            <option value="0" {{ isset($plan) ? $plan->include_fe == '0' ? 'selected' : '' : '' }}>No</option>
            <option value="1" {{ isset($plan) ? $plan->include_fe == '1' ? 'selected' : '' : '' }}>Si</option>
        </select>

        @if ($errors->has('include_fe'))
            <span class="help-block">
                <strong>{{ $errors->first('include_fe') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="include_fe" class="col-sm-2 control-label">Incluye Secretaria</label>

    <div class="col-sm-10">
        <select class="form-control select2" style="width: 100%;" name="include_assistant" required>
            <option value="0" {{ isset($plan) ? $plan->include_assistant == '0' ? 'selected' : '' : '' }}>No</option>
            <option value="1" {{ isset($plan) ? $plan->include_assistant == '1' ? 'selected' : '' : '' }}>Si</option>
        </select>

        @if ($errors->has('include_assistant'))
            <span class="help-block">
                <strong>{{ $errors->first('include_assistant') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="commission_by_appointment" class="col-sm-2 control-label">Comisión por cita</label>

    <div class="col-sm-10">
        <select class="form-control select2" style="width: 100%;" name="commission_by_appointment" required>
            <option value="0" {{ isset($plan) ? $plan->commission_by_appointment == '0' ? 'selected' : '' : '' }}>No
            </option>
            <option value="1" {{ isset($plan) ? $plan->commission_by_appointment == '1' ? 'selected' : '' : '' }}>Si
            </option>
        </select>

        @if ($errors->has('commission_by_appointment'))
            <span class="help-block">
                <strong>{{ $errors->first('commission_by_appointment') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="general_cost_commission_by_appointment" class="col-sm-2 control-label">Monto Comisión Médico General</label>

    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">₡</span>
            <input type="text" class="form-control" name="general_cost_commission_by_appointment"  value="{{ isset($plan) ? $plan->general_cost_commission_by_appointment : old('general_cost_commission_by_appointment') }}" required >
        </div>

        @if ($errors->has('general_cost_commission_by_appointment'))
            <span class="help-block">
              <strong>{{ $errors->first('general_cost_commission_by_appointment') }}</strong>
          </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="specialist_cost_commission_by_appointment" class="col-sm-2 control-label">Monto Comisión Médico Especialista</label>

    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">₡</span>
            <input type="text" class="form-control" name="specialist_cost_commission_by_appointment"  value="{{ isset($plan) ? $plan->specialist_cost_commission_by_appointment : old('specialist_cost_commission_by_appointment') }}" required >
        </div>

        @if ($errors->has('specialist_cost_commission_by_appointment'))
            <span class="help-block">
              <strong>{{ $errors->first('specialist_cost_commission_by_appointment') }}</strong>
          </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="specialist_cost_commission_by_appointment" class="col-sm-2 control-label">Descuento de Comisión </label>

    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">%</span>
            <input type="text" class="form-control" name="commission_discount"  value="{{ isset($plan) ? $plan->commission_discount : old('commission_discount') }}" required >
        </div>

        @if ($errors->has('commission_discount'))
            <span class="help-block">
              <strong>{{ $errors->first('commission_discount') }}</strong>
          </span>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="specialist_cost_commission_by_appointment" class="col-sm-2 control-label">Rango en minutos para Descuento de Comisión</label>

    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-addon">Min:</span>
            <input type="text" class="form-control" name="commission_discount_range_in_minutes"  value="{{ isset($plan) ? $plan->commission_discount_range_in_minutes : old('commission_discount_range_in_minutes') }}" required >
        </div>

        @if ($errors->has('commission_discount_range_in_minutes'))
            <span class="help-block">
              <strong>{{ $errors->first('commission_discount_range_in_minutes') }}</strong>
          </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="available" class="col-sm-2 control-label">Disponibilidad</label>
    <div class="col-sm-3">
        <label>
            <input type="checkbox" class="minimal" name="for_medic"
                   value="1" {{ isset($plan) && $plan->for_medic == 1 ? 'checked' : ''}}>
            Disponible para Médicos
        </label>
    </div>
    <div class="col-sm-3">
        <label>
            <input type="checkbox" class="minimal" name="for_clinic"
                   value="1" {{ isset($plan) && $plan->for_clinic == 1 ? 'checked' : ''}}>
            Disponible para Clínicas
        </label>
    </div>
    <div class="col-sm-3">
        <label>
            <input type="checkbox" class="minimal" name="for_pharmacy"
                   value="1" {{ isset($plan) && $plan->for_pharmacy == 1 ? 'checked' : ''}}>
            Disponible para Farmacias
        </label>
    </div>
    <div class="col-sm-3">
        <label>
            <input type="checkbox" class="minimal" name="for_lab"
                   value="1" {{ isset($plan) && $plan->for_lab == 1 ? 'checked' : ''}}>
            Disponible para Laboratorios
        </label>
    </div>
</div>


<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="/admin/plans" class="btn btn-default">Regresar</a>
    </div>
</div>
