@extends('layouts.pharmacies.app')
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
        <!-- /.col -->
        <div class="col-md-8">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="tabs-profile">
                    <li class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }}"><a href="#profile" data-toggle="tab" class="tab-profile">Perfil</a></li>
                    <li class="{{ isset($tab) ? ($tab =='pharmacies') ? 'active' : '' : '' }}"><a href="#pharmacies" data-toggle="tab">Información de la farmacia</a></li>
                    <!-- <li class="{{ isset($tab) ? ($tab =='assistant') ? 'active' : '' : '' }}"><a href="#assistant" data-toggle="tab">Perfil asistente</a></li> -->
                    <li class="{{ isset($tab) ? ($tab =='credentials') ? 'active' : '' : '' }}"><a href="#credentials" data-toggle="tab">Credenciales POS farmacia</a></li>
                    <li class="{{ isset($tab) ? ($tab == 'payments' ? 'active' : '') : '' }}"><a href="#payments" data-toggle="tab">Tus Facturas (Subscripción)</a></li>
                    <li class="{{ isset($tab) ? ($tab == 'acceptAffiliates' ? 'active' : '') : '' }}"><a href="#acceptAffiliates" data-toggle="tab">Atención a Afiliados</a></li> <!-- Inicio de modificación grupo G1 para aceptar afiliaciones  -->
                </ul>
                <div class="tab-content">
                    <div class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }} tab-pane" id="profile">

                        @include('pharmacy._profileForm')

                    </div>
                    <!-- /.tab-pane -->
                    <div class="{{ isset($tab) ? ($tab =='pharmacies') ? 'active' : '' : '' }} tab-pane" id="pharmacies">

                        <new-pharmacy></new-pharmacy>
                    </div>
                    <!-- <div class="{{ isset($tab) ? ($tab =='assistant') ? 'active' : '' : '' }} tab-pane" id="assistant">
                
                   <assistants></assistants>
                  
              </div> -->
                    <div class="{{ isset($tab) ? ($tab =='credentials') ? 'active' : '' : '' }} tab-pane" id="credentials">

                        <pharmacy-credentials-form :pharmacy="{{ auth()->user()->pharmacies()->with('pharmacredential')->first() }}"></pharmacy-credentials-form>

                    </div>
                    <div class="{{ isset($tab) ? ($tab == 'payments' ? 'active' : '') : '' }} tab-pane" id="payments">

                        <subscription-invoices :invoice-paid-statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::optionsAsConst()) }}"
                            :statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::options()) }}"></subscription-invoices>
                    </div>
                    <div class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }} tab-pane" id="profile">

                        @include('pharmacy._profileForm')

                    </div>
                    <!-- Inicio de modificación grupo G1 para aceptar afiliaciones  -->
                    <div class="{{ isset($tab) ? ($tab == 'acceptAffiliates' ? 'active' : '') : '' }} tab-pane" id="acceptAffiliates">
                        @include('pharmacy._AcceptAffiliationUserPatient', ['profileUser' => auth()->user()])
                    </div>
                    <!-- Fin de modificación grupo G1 para aceptar afiliaciones  -->
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

</section>

@endsection
@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script src="/vendor/moment/min/moment.min.js"></script>
<script src="/vendor/moment/locale/es.js"></script>
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>

<script>
    $(function() {


        window.emitter.emit('editPharmacy', window.App.currentPharmacy);
        window.emitter.emit('loadedPharmacies', window.App.user.pharmacies);

        $("[data-mask]").inputmask();

        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD',
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
            distritos = $('#distrito');


        cantones.empty();
        distritos.empty();


        provincias.change(function() {
            var $this = $(this);
            cantones.empty();
            distritos.empty();
            cantones.append('<option value="">-- Canton --</option>');
            $.each(window.provincias, function(index, provincia) {

                if (provincia.id == $this.val()) {
                    $.each(provincia.cantones, function(index, canton) {

                        cantones.append('<option value="' + canton.id + '">' + canton.title + '</option>');
                    });
                }
            });

        });
        cantones.change(function() {
            var $this = $(this);
            distritos.empty();
            distritos.append('<option value=""> -- Distrito --</option>');
            $.each(window.provincias, function(index, provincia) {

                if (provincia.id == provincias.val())
                    $.each(provincia.cantones, function(index, canton) {

                        if (canton.id == $this.val()) {
                            $.each(canton.distritos, function(index, distrito) {

                                distritos.append('<option value="' + distrito.id + '">' + distrito.title + '</option>');
                            });

                        }
                    });
            });

        });


    });
</script>
@endpush