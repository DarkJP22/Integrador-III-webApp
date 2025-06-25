@extends('layouts.admins.app')

@section('content')
    <section class="content-header">
      <h1>Crear Plan</h1>
    
    </section>
	<section class="content">
      
      <div class="row">
       
		<div class="col-md-8">
			<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#basic" data-toggle="tab">Información del Plan</a></li>
              
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="basic">
	                <form method="POST" action="{{ url('/admin/plans') }}" class="form-horizontal">
      				          
                            {{ csrf_field() }}
          				    @include('admin/plans/_form')
                           
                           
                       
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
  <script src="/vendor/ckeditor/ckeditor.js"></script>
  <script>
	$(function () {
		// Replace the <textarea id="editor1"> with a CKEditor
		// instance, using default configuration.
		CKEDITOR.replace('description')
		//bootstrap WYSIHTML5 - text editor
		$('.textarea').wysihtml5()
	})
	</script>
@endpush
