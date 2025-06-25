@extends('layouts.users.app')

@section('content')

<section class="content-header">
    <h1>Comprobante de pago</h1>

</section>

   
    <section class="invoice">
    
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

      @if(isset($code) && $code)
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
                <td>{{ $description }}. Código: {{ $code }}</td>
                <td>{{ money($total) }}</td>
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
  
</script>
@endpush

