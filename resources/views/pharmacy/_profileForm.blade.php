<form method="POST" action="{{ url('/pharmacy/profiles/'.$profileUser->id) }}" class="form-horizontal">
    {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
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
            <input type="email" class="form-control" id="email"  name="email" placeholder="Email"  value="{{ old('email') ?: $profileUser->email }}" readonly required>
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
        <label for="api_token" class="col-sm-2 control-label">Token Acceso GPS Farmacias</label>

        <div class="col-sm-10">
            <textarea class="form-control" name="api_token" id="api_token" cols="30" rows="2">{{ old('api_token') ?: $profileUser->api_token }}</textarea>
          
            @if ($errors->has('api_token'))
                <span class="help-block">
                    <strong>{{ $errors->first('api_token') }}</strong>
                </span>
            @endif
        </div>
    </div>
    



    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</form>