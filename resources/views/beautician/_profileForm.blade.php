
@php
$user_settings = $profileUser->getAllSettings();
@endphp
<div class="form-group">
    <label for="name" class="col-sm-2 control-label">Nombre</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ old('name') ?: $profileUser->name }}" required>
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
        <input type="email" class="form-control" id="email"  name="email" placeholder="Email"  value="{{ old('email') ?: $profileUser->email }}"  required>
        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
    </div>
    </div>
    <div class="form-group">
    <label for="password" class="col-sm-2 control-label">Cambiar contraseña</label>

    <div class="col-sm-10">
        <input type="password" class="form-control" id="password" name="password" placeholder="Escribe la nueva contraseña">
    </div>
    </div>
    <div class="form-group">
    <label for="phone" class="col-sm-2 control-label">Teléfono</label>
    <div class="col-sm-3">
        <select class="form-control" style="width: 100%;" name="phone_country_code" required>

        <option value="+506" {{  $profileUser->phone_country_code == '+506' ? 'selected' : (old('phone_country_code') == '+506' ? 'selected' : '') }}>+506</option>
        
        
        </select>
    
        @if ($errors->has('phone_country_code'))
            <span class="help-block">
                <strong>{{ $errors->first('phone_country_code') }}</strong>
            </span>
        @endif
    </div>
    <div class="col-sm-7">
        <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Teléfono de contacto" value="{{ old('phone_number') ?: $profileUser->phone_number }}" required>
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
        <input type="text" class="form-control" id="medic_code" name="medic_code" placeholder="Código de Médico" value="{{ old('medic_code') ?: $profileUser->medic_code }}" required>
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
        <select class="form-control select2" style="width: 100%;" name="speciality[]" placeholder="-- Selecciona Especialidad --" multiple >
        
        @foreach($specialities as $speciality)
            <option value="{{$speciality->id}}" @foreach($profileUser->specialities as $s) @if($speciality->id == $s->id)selected="selected"@endif @endforeach>{{$speciality->name}}</option>
        @endforeach
        
        </select>
    </div>
    </div>
    <div class="form-group">
    <label for="minTime" class="col-sm-2 control-label">Horario de atención</label>
    <div class="col-sm-4">
        <div class="input-group">
        <input type="text" class="form-control timepicker"  name="minTime" id="minTime" value="{{ old('minTime') ?: $user_settings['minTime'] }}">

        <div class="input-group-addon">
            <i class="fa fa-clock-o"></i>
        </div>

        </div>
    </div>

    <div class="col-sm-4">
        <div class="input-group">
        <input type="text" class="form-control timepicker"  name="maxTime" id="maxTime" value="{{ old('maxTime') ?: $user_settings['maxTime'] }}">

        <div class="input-group-addon">
            <i class="fa fa-clock-o"></i>
        </div>
        </div>
        
    </div>
    
    </div> 
    <div class="form-group">
        <label for="free_days" class="col-sm-2 control-label">Dias Libres</label>
    <div class="col-sm-4">
    
            <div class="checkbox">
            <label>
                <input type="checkbox" name="freeDays[]" value="1" @foreach(json_decode($user_settings['freeDays']) as $d) @if("1" == $d) checked="checked" @endif @endforeach >
                Lunes
            </label>
            </div>

            <div class="checkbox">
            <label>
                <input type="checkbox" name="freeDays[]" value="2" @foreach(json_decode($user_settings['freeDays']) as $d) @if("2" == $d) checked="checked" @endif @endforeach >
                Martes
            </label>
            </div>

            <div class="checkbox">
            <label>
                <input type="checkbox" name="freeDays[]" value="3" @foreach(json_decode($user_settings['freeDays']) as $d) @if("3" == $d) checked="checked" @endif @endforeach >
                Miércoles
            </label>
            </div>
            <div class="checkbox">
            <label>
                <input type="checkbox" name="freeDays[]" value="4" @foreach(json_decode($user_settings['freeDays']) as $d) @if("4" == $d) checked="checked" @endif @endforeach >
                Jueves
            </label>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="checkbox">
            <label>
                <input type="checkbox" name="freeDays[]" value="5" @foreach(json_decode($user_settings['freeDays']) as $d) @if("5" == $d) checked="checked" @endif @endforeach >
                Viernes
            </label>
            </div>
            <div class="checkbox">
            <label>
                <input type="checkbox" name="freeDays[]" value="6" @foreach(json_decode($user_settings['freeDays']) as $d) @if("6" == $d) checked="checked" @endif @endforeach >
                Sabado
            </label>
            </div>
            <div class="checkbox">
            <label>
                <input type="checkbox" name="freeDays[]" value="0" @foreach(json_decode($user_settings['freeDays']) as $d) @if("0" == $d) checked="checked" @endif @endforeach >
                Domingo
            </label>
            </div>
    </div>
</div>
<!-- <div class="form-group">
<label for="fe" class="col-sm-2 control-label">Factura eléctronica</label>

<div class="col-sm-10">
    <select class="form-control select2" style="width: 100%;" name="fe" required>
    <option value="0" {{ isset($profileUser) ? $profileUser->fe == '0' ? 'selected' : '' : '' }}>No</option>
    <option value="1" {{ isset($profileUser) ? $profileUser->fe == '1' ? 'selected' : '' : '' }}>Si</option>
    </select>

    @if ($errors->has('fe'))
        <span class="help-block">
            <strong>{{ $errors->first('fe') }}</strong>
        </span>
    @endif
</div>
</div> -->
    
    <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
    </div>
