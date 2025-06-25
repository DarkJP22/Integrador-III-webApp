@extends('layouts.clinics.app')
@section('header')
@endsection
@section('content')

<section class="content-header">
      <h1>Editar Recomendación</h1>
    
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
	                 <form method="POST" action="{{ url('/clinic/esthetic/recomendations/'.$recomendation->id) }}" class="form-horizontal">
      				          
                        {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
          				    @include('esthetic.recomendations._form')
                           
                       
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

@endpush
