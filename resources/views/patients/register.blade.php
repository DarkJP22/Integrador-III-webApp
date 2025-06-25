@extends('layouts.login')
@section('header')
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
  <link rel="stylesheet" href="/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css">
 
@endsection
@section('content')

  <div class="register-box register-box-patient">
  <div class="register-logo">
    <a href="/"><b>{{ config('app.name', 'Laravel') }}</b></a>
  </div>
   
  <div class="register-box-body">
    <div class="callout callout-info"><h4>Ya casi terminas!</h4> <p>Agrega los siguientes datos de paciente para poder reservar citas con los médicos.</p></div>
    <form method="POST" action="{{ url('/patients') }}" class="form-horizontal register-patient">
         {{ csrf_field() }}
         <div class="form-group">
           
           <div class="col-sm-12">
           <select class="form-control select2" style="width: 100%;" name="tipo_identificacion">
              <option value="">-- Tipo de identificación --</option>
              <option value="01" {{ old('tipo_identificacion') == '01' ? 'selected' : '' }}>Cédula Física</option>
              <option value="02" {{ old('tipo_identificacion') == '02' ? 'selected' : '' }}>Cédula Jurídica</option>
              <option value="03" {{ old('tipo_identificacion') == '03' ? 'selected' : '' }}>DIMEX</option>
              <option value="04" {{ old('tipo_identificacion') == '04' ? 'selected' : '' }}>NITE</option>
            </select>
            @if ($errors->has('tipo_identificacion'))
              <span class="help-block">
                  <strong>{{ $errors->first('tipo_identificacion') }}</strong>
              </span>
              @endif
           </div>
         </div>
          <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="ide" placeholder="Cédula" value="{{ auth()->user()->ide }}">
               @if ($errors->has('ide'))
                  <span class="help-block">
                      <strong>{{ $errors->first('ide') }}</strong>
                  </span>
              @endif
              
              </div>
          </div>
         <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="first_name" placeholder="Nombre" value="{{ auth()->user()->name }}" required>
               @if ($errors->has('first_name'))
                  <span class="help-block">
                      <strong>{{ $errors->first('first_name') }}</strong>
                  </span>
              @endif
              </div>
          </div>
          <!-- <div class="form-group">

            <div class="col-sm-12">
              <input type="text" class="form-control" name="last_name" placeholder="Apellidos" value="{{ old('last_name') }}" required>
               @if ($errors->has('last_name'))
                  <span class="help-block">
                      <strong>{{ $errors->first('last_name') }}</strong>
                  </span>
              @endif
            </div>
          </div> -->
          <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="birth_date" placeholder="Fecha de Nacimiento" value="{{ old('birth_date') }}" data-inputmask="'alias': 'yyyy-mm-dd'" data-mask required>
               @if ($errors->has('birth_date'))
                  <span class="help-block">
                      <strong>{{ $errors->first('birth_date') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <div class="form-group">
           
            <div class="col-sm-12">
              <select class="form-control select2" style="width: 100%;" name="gender" placeholder="-- Selecciona Genero --" required>
               
                <option value="m">Masculino</option>
                <option value="f">Femenino</option>
              </select>
              
               @if ($errors->has('gender'))
                  <span class="help-block">
                      <strong>{{ $errors->first('gender') }}</strong>
                  </span>
              @endif
            </div>
          </div>
           <div class="form-group">
            <div class="col-sm-4">
              <select class="form-control" style="width: 100%;" name="phone_country_code" required>

                <option value="+506" {{ auth()->user()->phone_country_code == '+506' ? 'selected' : (old('phone_country_code') == '+506' ? 'selected' : '') }}>+506</option>
                
             
              </select>
            
              @if ($errors->has('phone_country_code'))
                  <span class="help-block">
                      <strong>{{ $errors->first('phone_country_code') }}</strong>
                  </span>
              @endif
            </div>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="phone_number" placeholder="Teléfono (celular)" value="{{ auth()->user()->phone_number }}" required>
               @if ($errors->has('phone_number'))
                  <span class="help-block">
                      <strong>{{ $errors->first('phone_number') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          
          <div class="form-group">
           
            <div class="col-sm-12">
              <input type="email" class="form-control" name="email" placeholder="Email" value="{{ auth()->user()->email }}">
               @if ($errors->has('email'))
                  <span class="help-block">
                      <strong>{{ $errors->first('email') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="address" placeholder="Dirección" value="{{ old('address') }}" >
               @if ($errors->has('address'))
                  <span class="help-block">
                      <strong>{{ $errors->first('address') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <div class="form-group">
         
            <div class="col-sm-12">
              <select class="form-control select2" style="width: 100%;" name="province" placeholder="-- Selecciona provincia --" required>
               
                <option value="5">Guanacaste</option>
                <option value="1">San Jose</option>
                <option value="4">Heredia</option>
                <option value="7">Limon</option>
                <option value="3">Cartago</option>
                <option value="6">Puntarenas</option>
                <option value="2">Alajuela</option>
              </select>
              
               @if ($errors->has('province'))
                  <span class="help-block">
                      <strong>{{ $errors->first('province') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="city" placeholder="Ciudad" value="{{ old('city') }}" >
               @if ($errors->has('city'))
                  <span class="help-block">
                      <strong>{{ $errors->first('city') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <!-- <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="conditions" placeholder="Padecimientos" data-role="tagsinput" value="{{ old('coditions') }}" >
               @if ($errors->has('conditions'))
                  <span class="help-block">
                      <strong>{{ $errors->first('conditions') }}</strong>
                  </span>
              @endif
            </div>
          </div> -->
         
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-10">
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </div>
    </form>


  </div>
  <!-- /.form-box -->
</div>

		
@endsection
@push('scripts')
<script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script src="/vendor/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script>

    $(function () {

        $("form.register-patient").keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });
        
        $("[data-mask]").inputmask();
        
        // $("select[name='province']").select2({
        // placeholder: "Selecciona Provincia",
        // allowClear: true
        // });
        // $("select[name='gender']").select2({
        // placeholder: "Selecciona Genero",
        // allowClear: true
        // });

    });

</script>

@endpush
