@extends('layouts.medics.app')
@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">

    <!-- <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css"> -->
    <link rel="stylesheet" href="/vendor/hopscotch/css/hopscotch.min.css">
@endsection
@section('content')
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
        <div class="row">
            <div class="col-md-4">

                <avatar-form :user="{{ $profileUser }}"></avatar-form>

            </div>
            <!-- /.col -->
            <div class="col-md-8">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="tabs-profile">
                        <li class="{{ isset($tab) ? ($tab == 'profile' ? 'active' : '') : 'active' }}"><a href="#profile" data-toggle="tab" class="tab-profile">Perfil</a></li>
                        <!-- <li class="{{ isset($tab) ? ($tab == 'clinics' ? 'active' : '') : '' }}"><a href="#clinics" data-toggle="tab" class="tab-consultorios">Consultorios</a></li> -->

                        @if (auth()->user()->subscriptionPlanHasAssistant())
                            <li class="{{ isset($tab) ? ($tab == 'assistant' ? 'active' : '') : '' }}"><a href="#assistant" data-toggle="tab">Asistentes</a></li>
                        @endif
                        <li class="{{ isset($tab) ? ($tab == 'reviews' ? 'active' : '') : '' }}"><a href="#reviews" data-toggle="tab">Comentarios</a></li>
                        <li class="{{ isset($tab) ? ($tab == 'payments' ? 'active' : '') : '' }}"><a href="#payments" data-toggle="tab">Hitorial de pagos</a></li>
                        @if (auth()->user()->subscriptionPlanHasFe())
                            <!-- <li class="{{ isset($tab) ? ($tab == 'fe' ? 'active' : '') : '' }}"><a href="#fe" data-toggle="tab">Factura Electrónica</a></li> -->
                        @endif
                        <li class="{{ isset($tab) ? ($tab == 'discounts' ? 'active' : '') : '' }}"><a href="#discounts" data-toggle="tab">Descuentos Emp.</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="{{ isset($tab) ? ($tab == 'profile' ? 'active' : '') : 'active' }} tab-pane" id="profile">
                            <form method="POST" action="{{ url('/medic/profiles/' . $profileUser->id) }}" class="form-horizontal">
                                {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
                                @include('medic._profileForm')
                            </form>
                            @include('layouts._cancelAccountForm')

                        </div>

                        <!-- <div class="{{ isset($tab) ? ($tab == 'clinics' ? 'active' : '') : '' }} tab-pane" id="clinics">
                      <div class="callout callout-info">
                        <h4>Informacion importante!</h4>

                        <p>Agrega los consultorios donde brindarás consulta privada. Si el nombre de la <b>"Clínica privada"</b> no aparece, puedes solicitar integrarla al sistema. Nos pondremos en contacto con el administrador para crear la clínica lo antes posible.</p>
                      </div>
                      
                       <offices></offices>

                  </div> -->
                        @if (auth()->user()->subscriptionPlanHasAssistant())
                            <div class="{{ isset($tab) ? ($tab == 'assistant' ? 'active' : '') : '' }} tab-pane" id="assistant">

                                <assistants></assistants>

                            </div>
                        @endif
                        <!-- /.tab-pane -->
                        <div class="{{ isset($tab) ? ($tab == 'reviews' ? 'active' : '') : '' }} tab-pane" id="reviews">

                            @include('medic._reviews')

                        </div>
                        <!-- /.tab-pane -->

                        <div class="{{ isset($tab) ? ($tab == 'payments' ? 'active' : '') : '' }} tab-pane" id="payments">

                            @include('medic._historicalPayments')

                        </div>
                        @if (auth()->user()->subscriptionPlanHasFe())
                            <div class="{{ isset($tab) ? ($tab == 'fe' ? 'active' : '') : '' }} tab-pane" id="fe">

                                @include('medic._facturaElectronica')

                            </div>
                        @endif
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
    <script src="/vendor/select2/js/select2.full.min.js"></script>
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/moment/locale/es.js"></script>
    <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="/vendor/hopscotch/js/hopscotch.min.js"></script>
    <script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>

    <script>
        $(function() {

            $("[data-mask]").inputmask();

            $('.comments-box').slimScroll({
                height: '250px'
            });


            $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'es',
                //useCurrent: true,
                //defaultDate: new Date(),
            });

            $('.timepicker').datetimepicker({
                format: 'HH:mm:ss',
                stepping: 30,


            });


            $(".select2").select2();



        });
    </script>
    <script>
        $(function() {

            @if (!auth()->user()->active && !$userOffices)
                var tour = {
                    id: "inactive-user-consultorios",

                    i18n: {
                        nextBtn: "Siguiente",
                        prevBtn: "Atras",
                        doneBtn: "Listo"
                    },

                    steps: [{
                            title: "Cuenta esta inactiva",
                            content: "Esta cuenta esta inactiva mientras el administrador verifica tus datos. Puedes seguir editando tus opciones mientras se activa. <a class='popup-youtube' href='http://www.youtube.com/watch?v=DrYMxb-7WQI'>EMPIECE AQUI!</a>",
                            target: "#tabs-profile .tab-profile",
                            placement: "top",


                        },
                        {
                            title: "Consultorios",
                            content: "Recuerda agregar tus consultorios o clinica para poder ser agregado en el catalogo de busquedas!",
                            target: "#tabs-profile .tab-consultorios",
                            placement: "top",



                        }

                    ],
                    onEnd: function() {

                        // localStorage.setItem("tour_viewed", 1)

                    }

                };


                hopscotch.startTour(tour);
            @elseif (!auth()->user()->active)

                var tour = {
                    id: "inactive-user",

                    i18n: {
                        nextBtn: "Siguiente",
                        prevBtn: "Atras",
                        doneBtn: "Listo"
                    },

                    steps: [{
                            title: "Cuenta esta inactiva",
                            content: "Esta cuenta esta inactiva mientras el administrador verifica tus datos. Puedes seguir editando tus opciones mientras se activa. <a class='popup-youtube' href='http://www.youtube.com/watch?v=DrYMxb-7WQI'>EMPIECE AQUI!</a>",
                            target: "#tabs-profile .tab-profile",
                            placement: "top",


                        }


                    ],
                    onEnd: function() {

                        // localStorage.setItem("tour_viewed", 1)

                    }

                };


                hopscotch.startTour(tour);
            @elseif (!$userOffices)
                var tour = {
                    id: "consultorios-user",

                    i18n: {
                        nextBtn: "Siguiente",
                        prevBtn: "Atras",
                        doneBtn: "Listo"
                    },

                    steps: [{
                            title: "Consultorios",
                            content: "Recuerda agregar tus consultorios o clinica para poder ser agregado en el catalogo de busquedas!",
                            target: "#tabs-profile .tab-consultorios",
                            placement: "top",


                        }


                    ],
                    onEnd: function() {

                        // localStorage.setItem("tour_viewed", 1)

                    }

                };


                hopscotch.startTour(tour);
            @endif

        });
    </script>
    <script>
        $(function() {

            var provincias = $('#provincia'),
                cantones = $('#canton'),
                distritos = $('#distrito'),
                barrios = $('#barrio');


            cantones.empty();
            distritos.empty();
            barrios.empty();


            provincias.change(function() {
                var $this = $(this);
                cantones.empty();
                distritos.empty();
                barrios.empty();
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
                barrios.empty();
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

            distritos.change(function() {
                var $this = $(this);
                barrios.empty();
                barrios.append('<option value=""> -- Barrio --</option>');
                $.each(window.provincias, function(index, provincia) {

                    if (provincia.id == provincias.val())
                        $.each(provincia.cantones, function(index, canton) {

                            if (canton.id == $this.val()) {
                                $.each(canton.distritos, function(index, distrito) {

                                    if (distrito.id == $this.val()) {
                                        $.each(distrito.barrios, function(index, barrio) {

                                            barrios.append('<option value="' + barrio.id + '">' + barrio.title + '</option>');
                                        });
                                    }
                                });

                            }
                        });
                });

            });

            @if ($configFactura)
                setTimeout(function() {

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
