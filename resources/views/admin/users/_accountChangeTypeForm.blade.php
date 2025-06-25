
    <form method="POST" action="{{ url('/admin/users/'.$user->id .'/changeaccountcentromedico') }}" class="form-horizontal">
        {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
            <div class="form-group">
                <label for="plan" class="col-sm-2 control-label">Consultorio</label>

                <div class="col-sm-10">
                <select class="form-control select2" style="width: 100%;" name="office_id" required>
                    
                    @foreach($user->offices as $office)
                        <option value="{{ $office->id }}" >{{ $office->name }}</option>
                    @endforeach
                    
                </select>
                
                @if ($errors->has('office_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('office_id') }}</strong>
                    </span>
                @endif
                </div>
            </div>

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Nombre</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}">
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
                    <input type="email" class="form-control" name="email" placeholder="Email"  value="{{ old('email') }}">
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
                    <input type="password" class="form-control" name="password" placeholder="Escribe la nueva contraseña">
                </div>
            </div>
            <div class="form-group">
                <label for="phone" class="col-sm-2 control-label">Teléfono</label>
                <div class="col-sm-3">
                    <select class="form-control" style="width: 100%;" name="phone_country_code">

                    <option value="+506" {{ (old('phone_country_code') == '+506' ? 'selected' : '') }}>+506</option>
                    
                    
                    </select>
                
                    @if ($errors->has('phone_country_code'))
                        <span class="help-block">
                            <strong>{{ $errors->first('phone_country_code') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Teléfono de contacto" value="{{ old('phone_number') }}">
                    @if ($errors->has('phone_number'))
                        <span class="help-block">
                            <strong>{{ $errors->first('phone_number') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Crear Cuenta</button>
                
    

                </div>
            </div>
            
    </form>
      