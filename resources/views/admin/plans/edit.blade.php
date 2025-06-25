@extends('layouts.admins.app')
@section('css')

@endsection
@section('content')
	
    <section class="content-header">
      <h1>Editar Plan</h1>
    
    </section>
	<section class="content">
      
      <div class="row">
        
		 
		<div class="col-md-8">
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	              <li class="active"><a href="#basic" data-toggle="tab">Información del plan</a></li>
	             
	              
	            </ul>
	            <div class="tab-content">
	              	<div class="active tab-pane" id="basic">
						<form method="POST" action="{{ url('/admin/plans/'.$plan->id) }}" class="form-horizontal">
					         {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
					         @include('admin/plans/_form',['buttonText' => 'Actualizar Plan'])
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

