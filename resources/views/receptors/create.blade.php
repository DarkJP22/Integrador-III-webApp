@extends('layouts.medics.app')

@section('content')
    <section class="content-header">
      <h1>Confirmar Documento</h1>
    
    </section>

    <section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="panel">
                <div class="panel-body">
                @include('agenda._buttons')
                
                </div>
              
              </div>
              
            </div>
          </div>
          @include('receptors._form')
           
             
         
       

    </section>

@endsection


