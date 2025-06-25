<div class="form-group">
  <label for="plan_id" class="col-sm-2 control-label">Plan</label>
  <div class="col-sm-10">   
    <select class="form-control" style="width: 100%;" name="plan_id" required>
      @foreach($plans as $plan)
        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->title }} - @if($plan->cost) {{ $plan->currency?->symbol }}{{ $plan->cost }} / {{ $plan->quantity }} mes(es) @else Gratis @endif</option>
      @endforeach
    </select>
    @if ($errors->has('plan_id'))
        <span class="help-block">
            <strong>{{ $errors->first('plan_id') }}</strong>
        </span>
    @endif

  </div>
</div>
<div class="form-group">
    <label for="ide" class="col-sm-2 control-label">Cédula</label>

    <div class="col-sm-10">
      <input type="text" class="form-control" name="ide" placeholder="Cédula" value="{{ isset($medic) ? $medic->ide : old('ide') }}" {{ isset($medic) ? 'readonly' : ''}} required>
       @if ($errors->has('ide'))
          <span class="help-block">
              <strong>{{ $errors->first('ide') }}</strong>
          </span>
      @endif
      </div>
  </div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label">Nombre</label>

    <div class="col-sm-10">
      <input type="text" class="form-control" name="name" placeholder="Nombre y apellidos" value="{{ isset($medic) ? $medic->name : old('name') }}" required {{ isset($medic) ? 'readonly' : ''}}>
       @if ($errors->has('name'))
          <span class="help-block">
              <strong>{{ $errors->first('name') }}</strong>
          </span>
      @endif
      </div>
  </div>
 
   <div class="form-group">
    <label for="phone" class="col-sm-2 control-label">Teléfono</label>

    <div class="col-sm-3">
      <select class="form-control" style="width: 100%;" name="phone_country_code" required {{ isset($medic) ? 'readonly' : ''}}>

        <option value="+506" {{ isset($medic) ? $medic->phone_country_code == '+506' ? 'selected' : '' : (old('phone_country_code') == '+506' ? 'selected' : '') }}>+506</option>
      
      </select>
    
       @if ($errors->has('phone_country_code'))
          <span class="help-block">
              <strong>{{ $errors->first('phone_country_code') }}</strong>
          </span>
      @endif
    </div>
    <div class="col-sm-7">
      <input type="text" class="form-control" name="phone_number" placeholder="Teléfono" value="{{ isset($medic) ? $medic->phone_number : old('phone_number') }}" {{ isset($medic) ? 'readonly' : ''}} required>
       @if ($errors->has('phone_number'))
          <span class="help-block">
              <strong>{{ $errors->first('phone_number') }}</strong>
          </span>
      @endif
    </div>
  </div>

<div class="form-group">
    <label for="medic_code" class="col-sm-2 control-label">Código de Médico</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" id="medic_code" name="medic_code" placeholder="Código de Médico" value="{{ isset($medic) ? $medic->medic_code : old('medic_code') }}" required>
        @if ($errors->has('medic_code'))
            <span class="help-block">
                <strong>{{ $errors->first('medic_code') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="speciality_id" class="col-sm-2 control-label">Especialidad</label>

    <div class="col-sm-10">
        <select class="form-control select2" style="width: 100%;" name="speciality[]" placeholder="-- Selecciona Especialidad --" multiple>
        
        @foreach($specialities as $speciality)
            <option value="{{$speciality->id}}" @if(isset($medic))@foreach($medic->specialities as $s) @if($speciality->id == $s->id)selected="selected"@endif @endforeach @endif>{{$speciality->name}}</option>
        @endforeach
        
        </select>
    </div>
</div>
  
  <div class="form-group">
    <label for="email" class="col-sm-2 control-label">Email</label>

    <div class="col-sm-10">
      <input type="email" class="form-control" name="email" placeholder="Email" value="{{ isset($medic) ? $medic->email : old('email') }}" >
       @if ($errors->has('email'))
          <span class="help-block">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
      @endif
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-sm-2 control-label">Contraseña</label>

    <div class="col-sm-10">
      <input type="password" class="form-control" name="password" placeholder="Contraseña"  >
       @if ($errors->has('password'))
          <span class="help-block">
              <strong>{{ $errors->first('password') }}</strong>
          </span>
      @endif
    </div>
  </div>
  
 