@extends('layouts.clinics.app')

@section('content')

<section class="content-header">
      <h1>Nueva Afiliación</h1>
    
    </section>
    <section class="content">
          
       
           
          @include('affiliations._form')
   
 

  </section>
		
@endsection
@push('scripts')

@endpush
