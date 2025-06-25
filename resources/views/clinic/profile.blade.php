@extends('layouts.clinics.app')
@section('header')
<link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
<link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
<!-- <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css"> -->
<link rel="stylesheet" href="/vendor/hopscotch/css/hopscotch.min.css">  
@endsection
@section('content')


    <section class="content">
      
      <div class="row">
        <div class="col-md-4">

            <avatar-form :user="{{ $profileUser }}"></avatar-form>
         
        </div>

        <div class="col-md-8">
         
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="tabs-profile">
              <li class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }}"><a href="#profile" data-toggle="tab" class="tab-profile">Perfil</a></li>
              <li class="{{ isset($tab) ? ($tab =='clinics') ? 'active' : '' : '' }}"><a href="#clinics" data-toggle="tab">Información de la clínica</a></li>
              <li class="{{ isset($tab) ? ($tab =='assistant') ? 'active' : '' : '' }}"><a href="#assistant" data-toggle="tab">Perfil Secretaria</a></li>
                <li class="{{ isset($tab) ? ($tab =='fe') ? 'active' : '' : '' }}"><a href="#fe" data-toggle="tab">Factura eléctronica</a></li>
              <li class="{{ isset($tab) ? ($tab =='discounts') ? 'active' : '' : '' }}"><a href="#discounts" data-toggle="tab">Descuentos Emp.</a></li>
                <li class="{{ isset($tab) ? ($tab == 'payments' ? 'active' : '') : '' }}"><a href="#payments" data-toggle="tab">Tus Facturas (Subscripción)</a></li>
            </ul>
            <div class="tab-content">
              <div class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }} tab-pane" id="profile">

                                     @include('clinic._profileForm')

              </div>

              <div class="{{ isset($tab) ? ($tab =='clinics') ? 'active' : '' : '' }} tab-pane" id="clinics">
                  
                  <new-office></new-office>
              </div>
              <div class="{{ isset($tab) ? ($tab =='assistant') ? 'active' : '' : '' }} tab-pane" id="assistant">
                
                   <assistants></assistants>
                  
              </div>
              <div class="{{ isset($tab) ? ($tab =='fe') ? 'active' : '' : '' }} tab-pane" id="fe">

                  @include('clinic._facturaElectronica')
                  
               </div>
               <div class="{{ isset($tab) ? ($tab =='discounts') ? 'active' : '' : '' }} tab-pane" id="discounts">

                  <discounts></discounts>
                  
               </div>
                <div class="{{ isset($tab) ? ($tab == 'payments' ? 'active' : '') : '' }} tab-pane" id="payments">

                    <subscription-invoices :invoice-paid-statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::optionsAsConst()) }}" :statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::options()) }}"></subscription-invoices>
                </div>
              
             

            </div>

          </div>

        </div>

      </div>


    </section>


@endsection
@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script src="/vendor/moment/min/moment.min.js"></script>
<script src="/vendor/moment/locale/es.js"></script>
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 
<script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>

<script>
  $(function () {

    

     window.emitter.emit('editOffice', window.App.currentOffice);
     window.emitter.emit('loadedOffices', window.App.user.offices);

     $("[data-mask]").inputmask();

    $('.datepicker').datetimepicker({
            format:'YYYY-MM-DD',
            locale: 'es',
            //useCurrent: true,
            //defaultDate: new Date(),
         });
    
    $('.timepicker').datetimepicker({
        format: 'HH:mm',
        stepping: 30,
                          
                          
     });
   

    $(".select2").select2();



	
    var provincias = $('#provincia'),
        cantones = $('#canton'),
        distritos =  $('#distrito'),
        barrios =  $('#barrio');
		

    cantones.empty();
    distritos.empty();
    barrios.empty();
	

    provincias.change(function() {
        var $this =  $(this);
        cantones.empty();
        distritos.empty();
        barrios.empty();
        cantones.append('<option value="">-- Canton --</option>');
        $.each(window.provincias, function(index,provincia) {

            if(provincia.id == $this.val()){
                $.each(provincia.cantones, function(index,canton) {

                    cantones.append('<option value="' + canton.id + '">' + canton.title + '</option>');
                });
              }
        });

    });
     cantones.change(function() {
        var $this =  $(this);
        distritos.empty();
        barrios.empty();
        distritos.append('<option value=""> -- Distrito --</option>');
        $.each(window.provincias, function(index,provincia) {
           
            if(provincia.id == provincias.val())
                $.each(provincia.cantones, function(index,canton) {
                  
                     if(canton.id == $this.val())
                     {
                      $.each(canton.distritos, function(index,distrito) {

                          distritos.append('<option value="' + distrito.id + '">' + distrito.title + '</option>');
                      });
                      
                     }
                });
        });

    });
    distritos.change(function() {
        var $this =  $(this);
        barrios.empty();
        barrios.append('<option value=""> -- Barrio --</option>');
        $.each(window.provincias, function(index,provincia) {
           
            if(provincia.id == provincias.val())
                $.each(provincia.cantones, function(index,canton) {
                  
                     if(canton.id == $this.val())
                     {
                      $.each(canton.distritos, function(index,distrito) {

                          if(distrito.id == $this.val())
                            {
                                $.each(distrito.barrios, function(index,barrio) {

                                    barrios.append('<option value="' + barrio.id + '">' + barrio.title + '</option>');
                                });
                            }
                      });
                      
                     }
                });
        });

	});

	@if($configFactura)
	  	setTimeout(function(){

                $('#provincia option[value="{{ $configFactura->provincia }}"]').attr("selected", true);
                $('#provincia').change();
                $('#canton option[value="{{ $configFactura->canton }}"]').attr("selected", true);
			    $('#canton').change();
				$('#distrito option[value="{{ $configFactura->distrito }}"]').attr("selected", true);
                $('#distrito').change();
                $('#barrio option[value="{{ $configFactura->barrio }}"]').attr("selected", true);
            }, 100);
	@endif

});
</script>
@endpush
