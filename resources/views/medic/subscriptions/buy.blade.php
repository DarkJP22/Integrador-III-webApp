@extends('layouts.medics.app')
@section('header')
    <script type="text/javascript" src="{{ config('services.pasarela.url_vpos2') }}" ></script>
@endsection
@section('content')

    <section class="content-header">
      <h1>Realizar de pago</h1>
    
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
        @if(isset($purchaseOperationNumber))
          <b>Numero de operación:</b> {{ $purchaseOperationNumber }}
          <br>
          <b>Estado:</b>
            
        @endif
          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    @if(isset($newPlan) && $newPlan)
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
                <td>
                  {{ $newPlan->title }} <br>
                  @if(isset($office))
                    <small>{{ $office->name }}</small>
                  @endif 
                </td>
                <td>{{ money($newPlan->cost) }}</td>
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
          <p class="lead">Metodos de pago:</p>
          <img src="/img/credit/visa.png" alt="Visa">
          <img src="/img/credit/mastercard.png" alt="Mastercard">
          <!-- <img src="/img/credit/american-express.png" alt="American Express"> -->

          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px; max-width:350px;">
            <img src="/img/credit/banner_payme_latam.png" alt="Payme" style="width:100%">
          </p>
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
         
          <div class="table-responsive">
            <table class="table">
              <tbody>
              <tr>
                <th style="width:50%">Subtotal:</th>
                <td>{{ money($amountTotal) }}</td>
              </tr>
              <tr>
                <th>Total:</th>
                <td>{{ money($amountTotal) }}</td>
              </tr>
            </tbody></table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
   
      <!-- this row will not appear when printing -->
      <div class="row no-print">
         @include('medic.payments._terms')
      </div>
      <div class="row no-print">
        <div class="col-xs-12">
            
            @if(config('services.pasarela.simulation_vpos'))
              <form method="POST" action="/medic/payments/receipt" class="alignet-form-vpos2 form-horizontal">
            @else
              <form method="POST" action="#" class="alignet-form-vpos2 form-horizontal">
            @endif
          
           
                <input class="form-control" type="hidden" name ="acquirerId" value="{{ config('services.pasarela.acquire_id') }}" />
                <input class="form-control" type="hidden" name ="idCommerce" value="{{ config('services.pasarela.commerce_id') }}" />
                <input class="form-control" type="hidden" name="purchaseOperationNumber" value="{{ $purchaseOperationNumber }}" />
                <input class="form-control" type="hidden" name="purchaseAmount" value="{{ $amount }}" />
                <input class="form-control" type="hidden" name="purchaseCurrencyCode" value="{{ $purchaseCurrencyCode }}" />
                <input class="form-control" type="hidden" name="language" value="SP" />
                <input class="form-control" type="hidden" name="shippingFirstName" value="{{ $medic_name }}" />
                <input class="form-control" type="hidden" name="shippingLastName" value="--" />
                <input class="form-control" type="hidden" name="shippingEmail" value="{{ $medic_email }}" />
                <input class="form-control" type="hidden" name="shippingAddress" value="Direccion" />
                <input class="form-control" type="hidden" name="shippingZIP" value="ZIP" />
                <input class="form-control" type="hidden" name="shippingCity" value="CITY" />
                <input class="form-control" type="hidden" name="shippingState" value="STATE" />
                <input class="form-control" type="hidden" name="shippingCountry" value="CR" />
                <input class="form-control" type="hidden" name="userCommerce" value="modalprueba1" />
                <input class="form-control" type="hidden" name="userCodePayme" value="8--580--4390" />
                <input class="form-control" type="hidden" name="descriptionProducts" value="{{ $description }}" />
                <input class="form-control" type="hidden" name="programmingLanguage" value="PHP" />
                <input class="form-control" type="hidden" name="reserved1" value="Valor Reservado ABC" />
                <input class="form-control" type="hidden" name="reserved2" value="{{ $newPlan->id }}" />
                <input class="form-control" type="hidden" name="reserved3" value="{{ $planBuyChange }}" />
                <input class="form-control" type="hidden" name="reserved5" value="1" />
                @if(isset($office))
                  <input class="form-control" type="hidden" name="reserved6" value="{{ $office->id }}" />
                @endif 
                @if(isset($income))
                  <input class="form-control" type="hidden" name="reserved4" value="{{ $income->id }}" />
                @endif
                <input class="form-control" type="hidden" name="purchaseVerification" value="{{ $purchaseVerification }}" />
                @if(config('services.pasarela.simulation_vpos'))
                  <input type="submit" value="Realizar pago (simulación)" class="btn btn-primary pull-right btn-VPOS">
                @else
                  <input type="button" onclick="{{ config('services.pasarela.url_modal') }}" value="Realizar pago" class="btn btn-primary pull-right btn-VPOS">
                @endif
               
              
               
              </form>
               <button class="btn btn-primary btn-sm btn-blank pull-right">Realizar pago</button>
          
        </div>
      </div>
    @endif
    </section>
	
		
@endsection
@push('scripts')
<script>
function printComprobante() {
            window.print();
        }

termscheckboxprepare();
function termscheckboxprepare() {

     if($('#terms').is(':checked')){
        $('.btn-blank').hide();
         $('.btn-VPOS').show();
      }else{
           $('.btn-blank').show();
         $('.btn-VPOS').hide();
      }
}
    
  $('#terms').click(function (e) {
      termscheckboxprepare();
  })

  $('.btn-blank').click(function (e) {
      alert('Aceptar terminos y condiciones para continuar con el proceso de pago')
  });
</script>
@endpush

