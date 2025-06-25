@extends('layouts.admins.app')
@section('header')
<link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
@endsection
@section('content')
	
	<section class="content-header">
      <h1>Editar Usuario</h1>
    
    </section>
	<section class="content">
      
      <div class="row">
        
		 
		<div class="col-md-6">
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	              <li class="active"><a href="#basic" data-toggle="tab">Información Básica</a></li>
	             
	              
	            </ul>
	            <div class="tab-content">
	              	<div class="active tab-pane" id="basic">
						<form method="POST" action="{{ url('/admin/users/'.$user->id) }}" class="form-horizontal">
					         {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
					         @include('admin/users/_form',['buttonText' => 'Actualizar Usuario'])
					    </form>


				    </div>
				    <!-- /.tab-pane -->

				             
	            </div>
	            <!-- /.tab-content -->
	        </div>
	          <!-- /.nav-tabs-custom -->

			  @include('admin.users._facturaElectronica')
			
			
		</div>
		<div class="col-md-6">
		
	              		 @if($user->hasRole('medico'))
						  <div class="box box-solid box-medics">
							<div class="box-header with-border">
								<h4 class="box-title">Subscripcion Actual @if(!$subscription)(No tiene Subscripción)@else <small>(Vencimiento: {{ $user->subscription->ends_at->toDateString() }})</small> @endif</h4>
								
							</div>
							<div class="box-body">
								@include('admin.users._subscriptionForm')
							</div>
							<!-- /.box-body -->
						</div>
							@if($user->hasRole('medico') && !$user->belongsToCentroMedico())
							<div class="box box-solid box-medics">
								<div class="box-header with-border">
									<h4 class="box-title">Crear cuenta de centro médico</h4>
									
								</div>
								<div class="box-body">
									@include('admin.users._accountChangeTypeForm')
								</div>
								<!-- /.box-body -->
							</div>
							@else
								<div class="box box-solid box-medics">
									<div class="box-header with-border">
										<h4 class="box-title">El médico esta afiliado a un centro médico</h4>
										
									</div>
									<div class="box-body">
									<table class="table table-bordered">
										<tbody>
											<tr>
												<th style="width: 10px">#</th>
												<th>Nombre</th>
											
												<th style="width: 200px">Tipo</th>
											</tr>
											@foreach($user->offices as $office)
											<tr>
												<td>{{ $office->id }}</td>
												<td>{{ $office->name }}</td>
												
												<td>{{ $office->typeName }}</td>
											</tr>
											@endforeach
										
										
											</tbody>
										</table>
										
									</div>
									<!-- /.box-body -->
								</div>

							@endif
				<div class="box box-solid box-medics">
					<div class="box-header with-border">
						<h4 class="box-title">Títulos Médicos</h4>

					</div>
					<div class="box-body">
						<div class="tw-grid tw-grid-cols-3 tw-gap-4">
							@foreach($titlesPhotos as $photo)
								<a href="{{ $photo['url'] }}" target="_blank">

									<img src="{{ $photo['url'] }}" alt="" class="tw-w-full" />
								</a>

							@endforeach
						</div>
					</div>
					<!-- /.box-body -->
				</div>
						@endif
						@if($user->hasRole('clinica') || $user->hasRole('laboratorio'))
							<div class="box box-solid box-medics">
								<div class="box-header with-border">
									<h4 class="box-title">Clínica</h4>
									
								</div>
								<div class="box-body">
									
								<new-office></new-office>
									
								</div>
								<!-- /.box-body -->
							</div>
							
							@endif

						@if(($user->hasRole('medico') || $user->hasRole('clinica') || $user->hasRole('laboratorio')) && $configFactura)
						  <div class="box box-solid box-medics">
							<div class="box-header with-border">
								<h4 class="box-title">Prueba Factura Eléctronica</h4>
								
							</div>
							<div class="box-body">
								
								@if(existsCertFile($configFactura))

								<test-conexion-hacienda user-id="{{ $user->id }}"></test-conexion-hacienda>
								@else 
									<h3>Parece que no tiene un certificado de pruebas instalado. Agrega uno para poder realizar pruebas de conexion con Hacienda</h3>
								
								@endif
								
							</div>
							<!-- /.box-body -->
						</div>
						@endif


			
				   
	       
			
			
		</div>

	  </div>
	</section>

<form method="post" id="form-delete-subscription" data-confirm="Estas Seguro?">
  <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
</form>
<form method="post" id="form-delete-configfactura" data-confirm="Estas Seguro?">
  <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
</form>

@endsection
@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script>
 $(function () {
	$(".select2").select2({
      placeholder: "Especialidad del médico",
      allowClear: true
    });

     @if($user->hasRole('clinica') || $user->hasRole('laboratorio'))
        let currentOffice = {!! $user->offices->first() !!}
        let offices = {!! json_encode($user->offices) !!}
       
        window.emitter.emit('editOffice', currentOffice);
        window.emitter.emit('loadedOffices',  offices);

      @endif
	
      var provincias = $('#provincia'),
        cantones = $('#canton'),
		distritos =  $('#distrito'),
		barrios = $('#barrio');
		

    cantones.empty();
	distritos.empty();
	barrios.empty();
    
	

    provincias.change(function() {
        var $this =  $(this);
        cantones.empty();
		distritos.empty();
		barrios.empty();
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
		barrios.empty();
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
	 distritos.change(function() {
        var $this =  $(this);
        barrios.empty();
        barrios.append('<option value=""> -- Barrio --</option>');
        $.each(window.provincias, function(index,provincia) {
           
            if(provincia.id == provincias.val())
                $.each(provincia.cantones, function(index,canton) {
                  
                     if(canton.id == $this.val())
                     {
                      $.each(canton.distritos, function(index,distrito) {

                          if(distrito.id == $this.val())
                            {
                                $.each(distrito.barrios, function(index,barrio) {

                                    barrios.append('<option value="' + barrio.id + '">' + barrio.title + '</option>');
                                });
                            }
                      });
                      
                     }
                });
        });

	});

	@if($configFactura)
	  	setTimeout(function(){

                $('#provincia option[value="{{ $configFactura->provincia }}"]').attr("selected", true);
                $('#provincia').change();
                $('#canton option[value="{{ $configFactura->canton }}"]').attr("selected", true);
				$('#canton').change();
				$('#distrito option[value="{{ $configFactura->distrito }}"]').attr("selected", true);
                $('#distrito').change();
                $('#barrio option[value="{{ $configFactura->barrio }}"]').attr("selected", true);
            }, 100);
	@endif
});
</script>

@endpush

