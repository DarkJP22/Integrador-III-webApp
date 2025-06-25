@extends('layouts.clinics.app')

@section('content')
    <section class="content-header">
      <h1>Proforma {{ $proforma->consecutivo }}</h1>
       
       
    
    </section>

    <section class="content">

           
        @include('proformas._form') 
         
       

    </section>

@endsection

