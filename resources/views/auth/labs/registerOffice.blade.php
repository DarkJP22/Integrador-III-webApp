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
    <div class="callout callout-info"><h4>Ya casi terminas!</h4> <p>Agrega los siguientes datos del laboratorio para finalizar el registro.</p></div>
    <form method="POST" action="{{ url('/lab/register/office') }}" class="form-horizontal register-patient"  enctype="multipart/form-data">
         {{ csrf_field() }}

           <!-- <div class="form-group">
         
            <div class="col-sm-12">
              <select class="form-control select2" style="width: 100%;" name="fe" id="fe" required>
                <option value="" style="color: #c3c3c3">¿Utiliza factura electrónica?</option>
                <option value="0" {{ old('fe') == "0" ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('fe') == "1" ? 'selected' : '' }}>Si</option>
    
              </select>
              
              
               @if ($errors->has('fe'))
                  <span class="help-block">
                      <strong>{{ $errors->first('fe') }}</strong>
                  </span>
              @endif
            </div>
          </div> -->
         
         <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="name" placeholder="Nombre del laboratorio" value="{{ old('name') }}" required>
               @if ($errors->has('name'))
                  <span class="help-block">
                      <strong>{{ $errors->first('name') }}</strong>
                  </span>
              @endif
              </div>
          </div>
          <div class="form-group">

            <div class="col-sm-12">
              <input type="text" class="form-control" name="address" placeholder="Dirección" value="{{ old('address') }}" required>
               @if ($errors->has('address'))
                  <span class="help-block">
                      <strong>{{ $errors->first('address') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <div class="form-group">
         
            <div class="col-sm-12">
              <select class="form-control select2" style="width: 100%;" name="province" id="province" placeholder="-- Selecciona provincia --" required>
                <option value="" style="color: #c3c3c3">Provincia</option>
                <option value="1">San Jose</option>
                <option value="2">Alajuela</option>
                <option value="3">Cartago</option>
                <option value="4">Heredia</option>
                <option value="5">Guanacaste</option>
                <option value="6">Puntarenas</option>
                <option value="7">Limon</option>
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
              <select class="form-control select2" style="width: 100%;" name="canton" id="canton" placeholder="-- Selecciona canton --" required>
                <option value="">Canton</option>
               
               
              </select>
              
               @if ($errors->has('canton'))
                  <span class="help-block">
                      <strong>{{ $errors->first('canton') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <div class="form-group">
         
            <div class="col-sm-12">
              <select class="form-control select2" style="width: 100%;" name="district" id="district" placeholder="-- Selecciona district --" required>
                <option value="">Distrito</option>
                
               
              </select>
              
               @if ($errors->has('district'))
                  <span class="help-block">
                      <strong>{{ $errors->first('district') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="phone" placeholder="Teléfono" value="{{ old('phone') }}" required>
               @if ($errors->has('phone'))
                  <span class="help-block">
                      <strong>{{ $errors->first('phone') }}</strong>
                  </span>
              @endif
            </div>
          </div>
           <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="ide" placeholder="Cédula Jurídica" value="{{ old('ide') }}">
               @if ($errors->has('ide'))
                  <span class="help-block">
                      <strong>{{ $errors->first('ide') }}</strong>
                  </span>
              @endif
            </div>
          </div>
           <div class="form-group">
           
            <div class="col-sm-12">
              <input type="text" class="form-control" name="ide_name" placeholder="Nombre Jurídico" value="{{ old('ide_name') }}">
               @if ($errors->has('ide_name'))
                  <span class="help-block">
                      <strong>{{ $errors->first('ide_name') }}</strong>
                  </span>
              @endif
            </div>
          </div>
          <div class="form-group">
            

           
            <div class="col-sm-12">
              <input type="file" class="form-control" name="file" placeholder="Logo">
               <span class="">Logo (jpg - png - bmp - jpeg)</span>
            </div>
          </div>
         
          <div class="form-group">
            <div class="col-sm-12">
              <button type="submit" class="btn btn-primary btn-block">Finalizar</button>
              <a class="btn btn-danger btn-flat" style="margin-top: 1rem;" href="{{ url('/logout') }}"
              onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
               {{ __('Salir') }}
           </a>
            </div>
          </div>
    </form>
<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
      @csrf
  </form>

  </div>
  <!-- /.form-box -->
</div>

		
@endsection
@push('scripts')
<!-- <script src="/vendor/input-mask/jquery.inputmask.js"></script>
<script src="/vendor/input-mask/jquery.inputmask.date.extensions.js"></script> -->
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script src="{{ asset('js/registerOffice.js') }}"></script>
<script>
 $(function () {
	
       var provincias = $('#province'),
        cantones = $('#canton'),
		distritos =  $('#district');
		

    cantones.empty();
    distritos.empty();
    
	

    provincias.change(function() {
        var $this =  $(this);
        cantones.empty();
        distritos.empty();
        cantones.append('<option value="">-- Canton --</option>');
        $.each(window.provincias, function(index,provincia) {

            if(provincia.id == $this.val()){
                $.each(provincia.cantones, function(index,canton) {

                    cantones.append('<option value="' + canton.id + '">' + canton.title + '</option>');
                });
              }
        });

    });
     cantones.change(function() {
        var $this =  $(this);
        distritos.empty();
        distritos.append('<option value=""> -- Distrito --</option>');
        $.each(window.provincias, function(index,provincia) {
           
            if(provincia.id == provincias.val())
                $.each(provincia.cantones, function(index,canton) {
                  
                     if(canton.id == $this.val())
                     {
                      $.each(canton.distritos, function(index,distrito) {

                          distritos.append('<option value="' + distrito.id + '">' + distrito.title + '</option>');
                      });
                      
                     }
                });
        });

	});


});
</script>
@endpush
