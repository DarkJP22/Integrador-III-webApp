@extends('layouts.admins.app')
@section('css')

@endsection
@section('content')
	
    <section class="content-header">
      <h1>Editar Etiqueta</h1>
    
    </section>
	<section class="content">
      
      <div class="row">
        
		 
		<div class="col-md-8">
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	              <li class="active"><a href="#basic" data-toggle="tab">Información de la etiqueta</a></li>
	             
	              
	            </ul>
	            <div class="tab-content">
	              	<div class="active tab-pane" id="basic">
						<form method="POST" action="{{ url('/mediatags/'.$tag->id) }}" class="form-horizontal">
					         {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
					         @include('admin/mediatags/_form',['buttonText' => 'Actualizar Plan'])
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

