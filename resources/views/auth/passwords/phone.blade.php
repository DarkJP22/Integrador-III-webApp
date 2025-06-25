@extends('layouts.login')

@section('content')

  <div class="login-box-body">
    <p class="login-box-msg">Cambio de contraseña</p>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
      <form role="form" method="POST" action="{{ url('/user/password/phone') }}">
            {{ csrf_field() }}
      <div class="form-group row">
        <div class="col-sm-4">
            <select class="form-control" style="width: 100%;" name="phone_country_code" required>

              <option value="+506" {{ old('phone_country_code') == '+506' ? 'selected' : '' }}>+506</option>
              
            
            </select>
          
            @if ($errors->has('phone_country_code'))
                <span class="help-block">
                    <strong>{{ $errors->first('phone_country_code') }}</strong>
                </span>
            @endif
        </div>
         <div class="col-sm-8">
        
            <input id="phone_number" type="phone" class="form-control" name="phone_number" value="{{ old('phone_number') }}" required placeholder="Teléfono">

            @if ($errors->has('phone_number'))
                <span class="help-block">
                    <strong>{{ $errors->first('phone_number') }}</strong>
                </span>
            @endif
            <!-- <span class="glyphicon glyphicon-phone form-control-feedback"></span> -->
        </div>
      </div>
    
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
        <!--<div class="col-md-6 col-md-offset-4">-->
          <button type="submit" class="btn btn-primary btn-block btn-flat">Enviar código de cambio de contraseña</button>
        </div>
        <!-- /.col -->
      </div>
    </form>


  </div>
  <!-- /.login-box-body -->

@endsection
