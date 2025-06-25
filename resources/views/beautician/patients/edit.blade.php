@extends('layouts.beauticians.app')
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
       <div class="col-md-12">
        <div class="panel">
          <div class="panel-body">
          @include('beautician.agenda._buttons')
          
          </div>
         
        </div>
         
        </div>
       </div>
      <div class="row">
        <div class="col-md-4">
			
					<avatar-form :user="{{ $patient }}" url="/api/patients/"></avatar-form>
						<div class="box box-default">
							<div class="box-body box-profile ">
									<emergency-contacts :patient="{{ $patient }}"></emergency-contacts>
							</div>
						</div> 
						
         
        </div>
		 
		<div class="col-md-8">
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
				  <li class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }}"><a href="#basic" data-toggle="tab">Información Básica</a></li>
				  <li class="{{ isset($tab) ? ($tab =='history') ? 'active' : '' : '' }}"><a href="#historyAppointments" data-toggle="tab">Historial de consulta</a></li>
	              
	             
	              
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
					<div class="{{ isset($tab) ? ( ($tab =='historyAppointments') ? 'active' : '') : '' }} tab-pane" id="historyAppointments">
						<div class="row">
							<div class="col-sm-6 col-xs-12">
								<h2>Historial de consultas</h2> 
								<div class="margin">
									<a class="btn btn-primary" href="/beautician/agenda/create?p={{ $patient->id }}">Crear consulta</a>
								</div>
								
								<div class="margin">
	
									
									@forelse($appointments as $appointment)
										
											<div class="info-box cita-item" style="text-align: left;margin-bottom: 5px;">
												<span class="info-box-icon bg-primary"><i class="fa fa-calendar"></i></span>
							
												<div class="info-box-content">
												
												@can('update', $appointment)
											
													<a class="info-box-text" href="/beautician/agenda/appointments/{{ $appointment->id }}">{{ $appointment->title }} con <small>Dr(a). {{ $appointment->user?->name ?? $appointment->medic_name }}</small></a>
												@else 
													<a class="info-box-text" href="#">{{ $appointment->title }} con <small>Dr(a). {{ $appointment->user?->name ?? $appointment->medic_name }}</small></a>
												@endcan 
												<span class="info-box-number">{{ \Carbon\Carbon::parse($appointment->date)->toDateString() }}<small></small></span>
												
												</div>
															
												<!-- /.info-box-content -->
												</div>
							
												
											
												
														
															
									@empty
										<p>Aun no hay citas iniciadas.</p>
									@endforelse
								</div>
							</div>

						</div>
						

				    </div>
				  
				    <!-- /.tab-pane -->
				    

				              
				             
	            </div>
	            <!-- /.tab-content -->
	        </div>
	          <!-- /.nav-tabs-custom -->
		
			
		</div>

	  </div>
	</section>



	<form method="post" id="form-delete" data-confirm="Estas Seguro?">
		<input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
	</form>
@endsection
@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>  
<script src="/vendor/moment/min/moment.min.js"></script>
<script src="/vendor/moment/locale/es.js"></script>
<script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
<script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="/vendor/fullcalendar/dist/locale/es.js"></script> 

@vite('resources/js/patients.js')

@endpush

