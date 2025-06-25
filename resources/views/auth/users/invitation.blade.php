@extends('layouts.login')

@section('css')
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
@endsection
@section('content')

<div class="register-box">
  <!-- <div class="register-logo">
     <a href="/"><img src="/img/logo.png" alt="{{ config('app.name', 'Laravel') }}"></a>
  </div> -->

  <div class="register-box-body">
    <p class="login-box-msg">Registra una nueva cuenta por invitación del paciente {{ Optional($patient)->fullname }}</p>

    <form  role="form" method="POST" action="{{ url('/invitation/'.$patientId.'/register/'.$code) }}">
        {{ csrf_field() }}
      <div class="form-group has-feedback">
        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus placeholder="Nombre">
         @if ($errors->has('name'))
            <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
            </span>
        @endif
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
       <div class="form-group has-feedback">
        
        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required>

        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
        
      </div>
       <div class="form-group has-feedback">
        <input id="password" type="password" class="form-control" name="password" required placeholder="Contraseña">

        @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="Confirmación de contraseña">
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
      </div>
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
        <div class="col-sm-8 has-feedback">
            <input id="phone" type="phone" class="form-control" name="phone_number" value="{{ old('phone_number') }}" required placeholder="Teléfono">
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
        <div class="col-xs-12 col-sm-5">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Registrar</button>
        </div>
        <!-- /.col -->
      </div>
    </form>


    <a href="{{ url('/user/login') }}" class="text-center">Ya tengo una cuenta</a>
  </div>
  <!-- /.form-box -->
</div>

@endsection
@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
     $("[data-mask]").inputmask();

  });
</script>
@endpush