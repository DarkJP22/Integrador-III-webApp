@extends('layouts.laboratories.app')
@section('header')
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
                        <li class="{{ isset($tab) ? ($tab == 'profile' ? 'active' : '') : 'active' }}"><a href="#profile" data-toggle="tab" class="tab-profile">Perfil</a></li>
                        <li class="{{ isset($tab) ? ($tab == 'clinics' ? 'active' : '') : '' }}"><a href="#clinics" data-toggle="tab">Información del laboratorio</a></li>
                        <li class="{{ isset($tab) ? ($tab =='payments') ? 'active' : '' : '' }}"><a href="#payments" data-toggle="tab">Tus Facturas (Subscripción)</a></li>
                        <li class="{{ isset($tab) ? ($tab == 'fe' ? 'active' : '') : '' }}"><a href="#fe" data-toggle="tab">Factura eléctronica</a></li>
                        <li class="{{ isset($tab) ? ($tab == 'discounts' ? 'active' : '') : '' }}"><a href="#discounts" data-toggle="tab">Descuentos Emp.</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="{{ isset($tab) ? ($tab == 'profile' ? 'active' : '') : 'active' }} tab-pane" id="profile">

                            @include('lab._profileForm')

                        </div>
                        <!-- /.tab-pane -->
                        <div class="{{ isset($tab) ? ($tab == 'clinics' ? 'active' : '') : '' }} tab-pane" id="clinics">

                            <new-office></new-office>
                        </div>
                        <div class="{{ isset($tab) ? ($tab =='payments') ? 'active' : '' : '' }} tab-pane" id="payments">

                            <subscription-invoices :invoice-paid-statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::optionsAsConst()) }}"
                                                   :statuses="{{ json_encode(\App\Enums\SubscriptionInvoicePaidStatus::options()) }}"></subscription-invoices>

                        </div>
                        <div class="{{ isset($tab) ? ($tab == 'fe' ? 'active' : '') : '' }} tab-pane" id="fe">

                            @include('clinic._facturaElectronica')

                        </div>
                        <div class="{{ isset($tab) ? ($tab == 'discounts' ? 'active' : '') : '' }} tab-pane" id="discounts">

                            <discounts></discounts>

                        </div>


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
    <script>
        $(function () {


            window.emitter.emit('editOffice', window.App.currentOffice);
            window.emitter.emit('loadedOffices', window.App.user.offices);


            var provincias = $('#provincia'),
                cantones = $('#canton'),
                distritos = $('#distrito'),
                barrios = $('#barrio');


            cantones.empty();
            distritos.empty();
            barrios.empty();


            provincias.change(function () {
                var $this = $(this);
                cantones.empty();
                distritos.empty();
                barrios.empty();
                cantones.append('<option value="">-- Canton --</option>');
                $.each(window.provincias, function (index, provincia) {

                    if (provincia.id == $this.val()) {
                        $.each(provincia.cantones, function (index, canton) {

                            cantones.append('<option value="' + canton.id + '">' + canton.title + '</option>');
                        });
                    }
                });

            });
            cantones.change(function () {
                var $this = $(this);
                distritos.empty();
                barrios.empty();
                distritos.append('<option value=""> -- Distrito --</option>');
                $.each(window.provincias, function (index, provincia) {

                    if (provincia.id == provincias.val())
                        $.each(provincia.cantones, function (index, canton) {

                            if (canton.id == $this.val()) {
                                $.each(canton.distritos, function (index, distrito) {

                                    distritos.append('<option value="' + distrito.id + '">' + distrito.title + '</option>');
                                });

                            }
                        });
                });

            });
            distritos.change(function () {
                var $this = $(this);
                barrios.empty();
                barrios.append('<option value=""> -- Barrio --</option>');
                $.each(window.provincias, function (index, provincia) {

                    if (provincia.id == provincias.val())
                        $.each(provincia.cantones, function (index, canton) {

                            if (canton.id == $this.val()) {
                                $.each(canton.distritos, function (index, distrito) {

                                    if (distrito.id == $this.val()) {
                                        $.each(distrito.barrios, function (index, barrio) {

                                            barrios.append('<option value="' + barrio.id + '">' + barrio.title + '</option>');
                                        });
                                    }
                                });

                            }
                        });
                });

            });

            @if ($configFactura)
            setTimeout(function () {

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
