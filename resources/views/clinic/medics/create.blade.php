@extends('layouts.clinics.app')
@section('header')
<link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
@endsection
@section('content')

<section class="content-header">
      <h1>Nuevo Médico</h1>
    
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
	                 <form method="POST" action="{{ url('/clinic/medics') }}" class="form-horizontal">
      				          
                            {{ csrf_field() }}
                      @include('medics._form')
                            <div class="form-group">
                              <label for="ide" class="col-sm-2 control-label">Rol</label>
                          
                              <div class="col-sm-10">
                                  <div class="checkbox">
                                    <label>
                                      <input type="checkbox" name="esteticista" value="1">
                                      Esteticista
                                    </label>
                                  </div>
                                </div>
                            </div>
                            
                            
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </div>
                       
                       
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
  
    //$("[data-mask]").inputmask();
  });
</script>
@endpush
