@extends('layouts.pharmacies.app')

@section('content')

<section class="content-header">
    <h1>Comprobante de pago</h1>

</section>

   
    <section class="invoice">
        @if( ($authorizationResult == 00 || $authorizationResult == "Success") && isset($purchaseOperationNumber) )
          @if(isset($reserved3) && ($reserved3 == 1 || $reserved3 == 2))
          <div class="row">
            <div class="col-xs-12">
            
                <div class="callout callout-warning">
                  <h4>Información importante!</h4>

                  <p>Para finalizar la configuración del sistema de punto de venta comunicate con soporte <a href="#" class="btn btn-secondary" data-toggle="modal" data-target="#contact-modal" data-user="" data-subject="Configuración Punto de venta. Numero Operación:{{ isset($purchaseOperationNumber) ? $purchaseOperationNumber : 'Error' }}" data-message="Configuración Punto de venta"><i class="fa fa-phone"></i> <span>Contácto / Soporte</span></a><p>
                    
                
                </div>
                
            
            </div>
            <!-- /.col -->
          </div>
          @endif
       @else
       <div class="row">
          <div class="col-xs-12">
          
              <div class="callout callout-danger">
                <h4>Error!</h4>

                <p>No se realizo la transacción correctamente.<p>
                  
              
              </div>
              
          
          </div>
          <!-- /.col -->
        </div>
       @endif
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <i class="fa fa-globe"></i> {{ config('app.name', 'Laravel') }}
            <small class="pull-right">{{ \Carbon\Carbon::now()->toDateTimeString() }}	</small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-6">
         
        @if(isset($authorizationResult) && isset($purchaseOperationNumber))
          <b>Numero de operación:</b> {{ $purchaseOperationNumber }}
          <br>
          <b>Estado:</b>
            @if($authorizationResult == 00 || $authorizationResult == "Success")
                <span class="text-green" ><b>Autorizada</b></span>
            @endif
            @if($authorizationResult == 01)
                <span class="text-red"><b>Denegada en el Banco Emisor</b></span>
            @endif
            @if($authorizationResult == 05 || $authorizationResult == "Failure")
                <span class="text-red"><b>Rechazada</b></span>
            @endif<br>
        @endif
          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    @if(isset($plan) && $plan->count())
      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>Cant.</th>
              <th>Description</th>
              <th>Subtotal</th>
            </tr>
            </thead>
            <tbody>
           
            <tr>
                <td>1</td>
                <td>{{ $plan->title }}</td>
                <td>{{ money($plan->cost) }}</td>
            </tr>
           
            
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">
          
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
         
          <div class="table-responsive">
            <table class="table">
              <tbody>
              <tr>
                <th style="width:50%">Subtotal:</th>
                <td>{{ money($total) }}</td>
              </tr>
              <tr>
                <th>Total:</th>
                <td>{{ money($total) }}</td>
              </tr>
            </tbody></table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    @endif
      <!-- this row will not appear when printing -->
      <div class="row no-print">
        <div class="col-xs-12">
          <a href="#" target="_blank" class="btn btn-secondary" onclick="printComprobante();"><i class="fa fa-print"></i> Imprimir</a>
           <a href="/" class="btn btn-primary pull-right"><i class="fa fa-edit"></i> Regresar
          </a>
          
        </div>
      </div>
    </section>
	
@endsection
@push('scripts')
<script>
function printComprobante() {
            window.print();
        }
  
    $('#contact-modal').on('show.bs.modal', function (e) {

      var button = $(e.relatedTarget)
      var subject = button.attr('data-subject');
      var message = button.attr('data-message');

      window.emitter.emit('subjectEvent', subject);
      window.emitter.emit('messageEvent', message);
      }); 
</script>
@endpush

