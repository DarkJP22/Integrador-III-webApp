<div class="form-group">
  <label for="ide" class="col-sm-2 control-label">Cédula</label>

  <div class="col-sm-10">
    <input type="text" class="form-control" name="ide" placeholder="Cédula" value="{{ isset($user) ? $user->ide : old('ide') }}" required>
     @if ($errors->has('ide'))
        <span class="help-block">
            <strong>{{ $errors->first('ide') }}</strong>
        </span>
    @endif
    </div>
</div>
<div class="form-group">
    <label for="office_name" class="col-sm-2 control-label">Nombre</label>

    <div class="col-sm-10">
      <input type="text" class="form-control" name="name" placeholder="Nombre" value="{{ isset($user) ? $user->name : old('name') }}" required >
       @if ($errors->has('name'))
          <span class="help-block">
              <strong>{{ $errors->first('name') }}</strong>
          </span>
      @endif
      </div>
  </div>
  
  <div class="form-group">
    <label for="email" class="col-sm-2 control-label">Email</label>

    <div class="col-sm-10">
      <input type="email" class="form-control" name="email" placeholder="Email" value="{{ isset($user) ? $user->email : old('email') }}" >
       @if ($errors->has('email'))
          <span class="help-block">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
      @endif
    </div>
  </div>

  <div class="form-group">
    <label for="phone" class="col-sm-2 control-label">Teléfono</label>
    <div class="col-sm-3">
        <select class="form-control" style="width: 100%;" name="phone_country_code" required>

        <option value="+506" {{  isset($user) && $user->phone_country_code == '+506' ? 'selected' : (old('phone_country_code') == '+506' ? 'selected' : '') }}>+506</option>
        
        
        </select>
    
        @if ($errors->has('phone_country_code'))
            <span class="help-block">
                <strong>{{ $errors->first('phone_country_code') }}</strong>
            </span>
        @endif
    </div>
    <div class="col-sm-7">
        <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Teléfono de contacto" value="{{ isset($user) ? $user->phone_number : old('phone_number') }}" required>
        @if ($errors->has('phone_number'))
            <span class="help-block">
                <strong>{{ $errors->first('phone_number') }}</strong>
            </span>
        @endif
    </div>
    </div>
  
  
<div class="form-group">
    <label for="password" class="col-sm-2 control-label">Contraseña: </label>
    <div class="col-sm-10">
        <input type="password" class="form-control" name="password" placeholder="Contraseña">
        @if ($errors->has('password'))
          <span class="help-block">
              <strong>{{ $errors->first('password') }}</strong>
          </span>
      @endif
    </div>

</div>

<div class="form-group">
    <label for="role" class="col-sm-2 control-label">Roles</label>

    <div class="col-sm-10">
      <select class="form-control select2" style="width: 100%;" name="role" required>
        @foreach($roles as $role)
        <option value="{{ $role->id }}" {{ isset($user) ? $user->hasRole($role->name) ? 'selected' : '' : '' }}>{{ $role->name }}</option>
        @endforeach
       
      </select>
     
       @if ($errors->has('role'))
          <span class="help-block">
              <strong>{{ $errors->first('role') }}</strong>
          </span>
      @endif
    </div>
  </div>
  <div class="form-group">
    <label for="medic_code" class="col-sm-2 control-label">Código de Médico</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" id="medic_code" name="medic_code" placeholder="Código de Médico" value="{{ isset($user) ? $user->medic_code : old('medic_code') }}">
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
            <option value="{{$speciality->id}}" @if(isset($user))@foreach($user->specialities as $s) @if($speciality->id == $s->id)selected="selected"@endif @endforeach @endif>{{$speciality->name}}</option>
        @endforeach
        
        </select>
    </div>
</div>
 @if(isset($user) && ($user->hasRole('medico') || $user->hasRole('asistente') || $user->hasRole('clinica') || $user->hasRole('laboratorio') ))
 <div class="form-group">
    <label for="fe" class="col-sm-2 control-label">Factura eléctronica</label>

    <div class="col-sm-10">
      <select class="form-control select2" style="width: 100%;" name="fe" required>
        <option value="0" {{ isset($user) ? $user->fe == '0' ? 'selected' : '' : '' }}>No</option>
        <option value="1" {{ isset($user) ? $user->fe == '1' ? 'selected' : '' : '' }}>Si</option>
      </select>
     
       @if ($errors->has('fe'))
          <span class="help-block">
              <strong>{{ $errors->first('fe') }}</strong>
          </span>
      @endif
    </div>
  </div>
  @endif
  
 @if(!isset($read))
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
  </div>
  @endif