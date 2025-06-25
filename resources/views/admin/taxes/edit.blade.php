@extends('layouts.admins.app')
@section('css')

@endsection
@section('content')
	
    <section class="content-header">
      <h1>Editar Impuesto</h1>
    
    </section>
	<section class="content">
      
      <div class="row">
        
		 
		<div class="col-md-8">
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	              <li class="active"><a href="#basic" data-toggle="tab">Información del impuesto</a></li>
	             
	              
	            </ul>
	            <div class="tab-content">
	              	<div class="active tab-pane" id="basic">
						{{-- <form method="POST" action="{{ url('/taxes/'.$tax->id) }}" class="form-horizontal"> --}}
					         {{-- {{ csrf_field() }}<input name="_method" type="hidden" value="PUT"> --}}
					         @include('admin/taxes/_form',['buttonText' => 'Actualizar Impuesto'])
					    {{-- </form> --}}

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

