@extends('layouts.clinics.app')
@section('header')
 
@endsection
@section('content')
 <section class="content">
	  <div class="box">
      <div class="box-header no-print">
          <a href="#" target="_blank" class="btn btn-secondary btn-sm" onclick="printSummary();"><i class="fa fa-print"></i></a>
          <a href="/clinic/agenda" class="btn btn-primary btn-sm">Regresar</a>
      </div>
		 <div class="box-body table-responsive no-padding" id="no-more-tables">
       @include('agenda._agendaPrint')
              
       </div>
              <!-- /.box-body -->
           
		
  </div>
 
</section>
 @endsection
 @push('scripts')
 <script>
 	 function printSummary() {
            window.print();
        }
        window.onload = printSummary;
 </script>
 @endpush