@extends('layouts.medics.app')

@section('content')

<section class="content-header">
      <h1>Nuevo Paciente</h1>
    
    </section>
	<section class="content">
       <div class="row">
       <div class="col-md-12">
        <div class="panel">
          <div class="panel-body">
          @include('agenda._buttons')
          
          </div>
         
        </div>
         
        </div>
       </div>
      <div class="row">
        
		 
		<div class="col-md-8">
			<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#basic" data-toggle="tab">Información Básica</a></li>
              
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="basic">
                <short-patient-form endpoint="/medic/patients" action-url="/medic/patients"></short-patient-form>
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

