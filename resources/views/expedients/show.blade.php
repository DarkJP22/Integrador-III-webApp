@extends('layouts.users.app')
@section('header')
<!-- <link rel="stylesheet" href="/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
<link rel="stylesheet" href="/vendor/bootstrap-timepicker/css/bootstrap-timepicker.min.css"> -->
<link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">

@endsection
@section('content')

	<section class="content">
      
      <div class="row">
       
		 
		<div class="col-md-7">
            <h3>Mi control personal</h3>
            <form action="/">
				<div class="form-group">
					<select name="patient" id="" class="form-control">
						@foreach(auth()->user()->patients as $p)
							<option value="{{ $p->id}}" {{ ($patient->id == $p->id) ? 'selected': ''}} >{{ $p->fullname }}</option>
						@endforeach
					</select>
					
				</div>
			</form>
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	              <li class="{{ isset($tab) ? ($tab =='pressure') ? 'active' : '' : 'active' }}"><a href="#pressure" data-toggle="tab">Control de presión</a></li>
	              <li class="{{ isset($tab) ? ($tab =='sugar') ? 'active' : '' : '' }}"><a href="#sugar" data-toggle="tab">Control de azúcar</a></li>
	              <li class="{{ isset($tab) ? ($tab =='medicines') ? 'active' : '' : '' }}"><a href="#medicines" data-toggle="tab">Mis medicamentos</a></li>
				   <li class="{{ isset($tab) ? ($tab =='alergies') ? 'active' : '' : '' }}"><a href="#alergies" data-toggle="tab">Soy alergico a:</a></li>
				   <li class="{{ isset($tab) ? ($tab =='historialCompras') ? 'active' : '' : '' }}"><a href="#historialCompras" data-toggle="tab">Historial Compras Medicamentos</a></li>
 				  
	              
	            </ul>
	            <div class="tab-content">
	              	<div class="{{ isset($tab) ? ($tab =='pressure') ? 'active' : '' : 'active' }} tab-pane" id="pressure">
						
						<pressure-control :pressures="{{ $pressures }}" :patient="{{ $patient }}"></pressure-control>	

				    </div>
				    <!-- /.tab-pane -->
				    <div class="{{ isset($tab) ? ($tab =='sugar') ? 'active' : '' : '' }} tab-pane" id="sugar">
					   <sugar-control :sugars="{{ $sugars }}" :patient="{{ $patient }}"></sugar-control>	
				    </div>
				    <!-- /.tab-pane -->
				    <div class="{{ isset($tab) ? ($tab =='medicines') ? 'active' : '' : '' }} tab-pane" id="medicines">
						
					    <medicines :medicines="{{ $medicines }}" :patient="{{ $patient }}"></medicines>	
							      
							   
				    </div>
				    <!-- /.tab-pane -->
				    <div class="{{ isset($tab) ? ($tab =='alergies') ? 'active' : '' : '' }} tab-pane" id="alergies">
					   		 
						<allergies :allergies="{{ $allergies }}" :patient="{{ $patient }}"></allergies>	
							  
					</div>
					<div class="{{ isset($tab) ? ($tab =='historialCompras') ? 'active' : '' : '' }} tab-pane" id="historialCompras">

                       
                        <historial-compras-pharmacy :patient="{{ $patient }}"></historial-compras-pharmacy>
                       



                    </div>
				    
				    

				              
				             
	            </div>
	            <!-- /.tab-content -->
	        </div>
	          <!-- /.nav-tabs-custom -->
			 
		</div>
		<div class="col-md-5">
					<h3>Control Médico</h3>
					<div class="nav-tabs-custom">
			            <ul class="nav nav-tabs">
									@foreach($appointments as $index => $lastAppointment)
									   
										<li class="{{ $index == 0 ? 'active' : '' }}">
                                            <a href="#history-{{ $index }}" data-toggle="tab">
                                                Resumen cita  {{ $lastAppointment->id }}
                                            </a>
                                        </li>
			              
										@endforeach
			              
			            </ul>
			            <div class="tab-content">
								@foreach($appointments as $index => $lastAppointment)
											<div class="{{ $index == 0 ? 'active' : '' }} tab-pane" id="history-{{ $index }}">
								
										
											<div>

                                             <summary-appointment 
                                                    history="" 
                                                    :medicines="{{ $lastAppointment->patient->medicines }}"
                                                    :notes="{{ $lastAppointment->diseaseNotes }}"
                                                    :exams="{{ $lastAppointment->physicalExams }}"
                                                    :diagnostics="{{ $lastAppointment->diagnostics }}"
                                                    :treatments="{{ $lastAppointment->treatments }}"
                                                    instructions="{{ $lastAppointment->medical_instructions }}"
                                                    :labexams="{{ $lastAppointment->labexams }}">
											</summary-appointment> 
																
											</div>
										

						    			</div>
									@endforeach
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

<script src="/vendor/moment/min/moment.min.js"></script>
<script src="/vendor/moment/min/locales.min.js"></script>
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 

<script>
  $(function () {
   
    $('select[name="patient"]').on('change',function (e) {
        e.preventDefault();
  
        window.location.href = "/patients/"+ $(this).val() + "/expedient" 
       
     });


    $('.datepicker').datetimepicker({
            format:'YYYY-MM-DD',
            locale: 'es',
            //useCurrent: true,
            //defaultDate: new Date(),
         });
    
    $('.timepicker').datetimepicker({
        format: 'HH:mm',
        stepping: 30,
                          
                          
     });

   
    
    
  });
</script>
@endpush

