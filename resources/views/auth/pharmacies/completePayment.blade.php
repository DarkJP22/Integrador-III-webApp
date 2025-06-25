@extends('layouts.login')

@section('header')
  <link rel="stylesheet" href="/vendor/select2/select2.min.css">
@endsection
@section('content')

<div class="register-box">
  <!-- <div class="register-logo">
     <a href="/"><img src="/img/logo.png" alt="{{ config('app.name', 'Laravel') }}"></a>
  </div> -->

  <div class="register-box-body">
    <p class="login-box-msg">Completar Pago de subscripción</p>

    <form  role="form" method="POST" action="{{ url('/pharmacy/complete-payment') }}">
        {{ csrf_field() }}
        <div class="form-group has-feedback">
          <select class="form-control" style="width: 100%;" name="plan_id" required>
            @foreach($plans as $plan)
              <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->title }} - ${{ $plan->cost }} / {{ $plan->quantity }} mes(es)</option>
            @endforeach
          </select>
          @if ($errors->has('plan_id'))
              <span class="help-block">
                  <strong>{{ $errors->first('plan_id') }}</strong>
              </span>
          @endif
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
      <div class="form-group has-feedback">
        <input id="name" type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" readonly autofocus placeholder="Nombre del administrador de la farmacia">
        
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="email" type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" readonly placeholder="Email">

       
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
       <div class="form-group row">
        <div class="col-sm-4">
            <select class="form-control" style="width: 100%;" name="phone_country_code" readonly>

              <option value="+506" {{  auth()->user()->phone_country_code == '+506' ? 'selected' : '' }}>+506</option>
            
            </select>
          
           
        </div>
        <div class="col-sm-8 has-feedback">
            <input id="phone" type="phone" class="form-control" name="phone_number" value="{{ auth()->user()->phone_number }}" readonly placeholder="Teléfono">
           
            <!-- <span class="glyphicon glyphicon-phone form-control-feedback"></span> -->
        </div>
       
      </div>
     
      
      <div class="row">
        
        <!-- /.col -->
        <div class="col-xs-12 col-sm-8">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Continuar con el pago</button>
          
        </div>
        <!-- /.col -->
      </div>
    </form>


   
  </div>
  <!-- /.form-box -->
</div>

@endsection
@push('scripts')
<script src="/vendor/select2/select2.full.min.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();

  });
</script>
@endpush