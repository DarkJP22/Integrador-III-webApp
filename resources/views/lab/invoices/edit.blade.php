@extends('layouts.laboratories.app')
@section('content')
    <section class="content-header">
      <h1>Factura {{ $invoice->consecutivo }}</h1>
       
       
    
    </section>

    <section class="content">

           
        @include('invoices._form') 
         
       

    </section>

@endsection

