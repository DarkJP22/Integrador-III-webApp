@extends('layouts.laboratories.app')

@section('content')
    <section class="content-header">
      <h1>Crear examen (Producto)</h1>
    
    </section>
	<section class="content">
      
      <div class="row">
       
		<div class="col-md-8">
			<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#basic" data-toggle="tab">Información del examen</a></li>
              
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="basic">
	              
          				    @include('lab/exams/_form')
                           
                           
                       
      			    
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
<script>
  $(function() {
      let officeId = {!! auth()->user()->offices->first()->id !!}
      window.emitter.emit('createProduct', officeId);
  });
</script>
@endpush
