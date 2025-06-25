@extends('layouts.assistants.app')
@section('header')
 
@endsection
@section('content')
 <section class="content">
    
  @include('agenda._agendaOnePrint')
	
		

 
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