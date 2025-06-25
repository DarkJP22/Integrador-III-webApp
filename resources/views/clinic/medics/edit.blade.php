@extends('layouts.clinics.app')
@section('header')
<link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
@endsection
@section('content')

<section class="content-header">
      <h1>Editar Médico</h1>
    
    </section>
	<section class="content">
      
      <div class="row">
        
		 
		<div class="col-md-8">
			<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#basic" data-toggle="tab">Información Básica</a></li>
              
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="basic">
	                 <form method="POST" action="{{ url('/clinic/medics/'.$profileUser->id) }}" class="form-horizontal">
      				          
                        {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
          				    @include('medic._profileForm')
                           
                       
      			    	</form>
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
<script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>
<script>
    
  $(function () {

    //Initialize Select2 Elements
    $(".select2").select2({
      placeholder: "Especialidad del médico",
      allowClear: true
    });
  
    $("[data-mask]").inputmask();
  });
</script>
@endpush
