@extends('layouts.operators.app')
@section('header')
 <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
 <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css"> 
 <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
 <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
@endsection
@section('content')
   <section class="content-header">
      <h1>Paciente {{ $patient->fullname }}</h1>
    
    </section>

	<section class="content">
      
      <div class="row">
        <div class="col-md-4">
			
          <avatar-form :user="{{ $patient }}" url="/api/patients/" :read="true"></avatar-form>
         
        </div>
		 
		<div class="col-md-8">
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	              <li class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }}"><a href="#basic" data-toggle="tab">Información Básica</a></li>
	              <li class="{{ isset($tab) ? ($tab =='appointments') ? 'active' : '' : '' }}"><a href="#appointments" data-toggle="tab">Consultas</a></li>
	             
	              
	            </ul>
	            <div class="tab-content">
	              	<div class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }} tab-pane" id="basic">
						<form method="POST" action="{{ url('/general/patients/'.$patient->id) }}" class="form-horizontal">
					         {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
					         @include('patients._form')

							@can('update', $patient)
							  <div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
									<button type="submit" class="btn btn-primary">Actualizar</button>
									</div>
								</div>
							@endcan
					    </form>

				    </div>
				    <!-- /.tab-pane -->
	

				    <div class="{{ isset($tab) ? ($tab =='appointments') ? 'active' : '' : '' }} tab-pane" id="appointments">
							

					      @include('appointments._historical')
					    
				    </div>
				    <!-- /.tab-pane -->
				    

				              
				             
	            </div>
	            <!-- /.tab-content -->
	        </div>
	          <!-- /.nav-tabs-custom -->
			
			
		</div>

	  </div>
	</section>


@endsection
@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>  
<script src="/vendor/moment/min/moment.min.js"></script>
<script src="/vendor/moment/locale/es.js"></script>
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 
<script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>
<script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
<script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="/vendor/fullcalendar/dist/locale/es.js"></script> 
@vite('resources/js/patients.js')
<script>
  $(function () {
  
	$("[data-mask]").inputmask();
	
	$('#datetimepickerLabResult').datetimepicker({
			format:'YYYY-MM-DD',
            locale: 'es',
            
         });

   
  });
</script>
@endpush

