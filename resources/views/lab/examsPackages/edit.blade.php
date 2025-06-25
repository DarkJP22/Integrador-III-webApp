@extends('layouts.laboratories.app')
@section('css')

@endsection
@section('content')
	
    <section class="content-header">
      <h1>Editar Paquete</h1>
    
    </section>
	<section class="content">
      
      <div class="row">
        
		 
		<div class="col-md-8">
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	              <li class="active"><a href="#basic" data-toggle="tab">Información del paquete</a></li>

					<form action="{{ url('/lab/exams-packages/'.$examPackage->id) }}" method="post" class="pull-right" name="deletePackageForm">
						@csrf
						@method('DELETE')
						<button type="button" class="btn btn-danger" onclick="confirm('¿Esta seguro que quiere eliminar?') ? deletePackageForm.submit() : false ">Eliminar</button>
					</form>
	            </ul>

	            <div class="tab-content">
	              	<div class="active tab-pane" id="basic">
						<form method="POST" action="{{ url('/lab/exams-packages/'.$examPackage->id) }}" class="form-horizontal" enctype="multipart/form-data">
					         {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
					         @include('lab/examsPackages/_form',['buttonText' => 'Actualizar Paquete'])
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

