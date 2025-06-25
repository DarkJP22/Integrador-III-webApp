@extends('layouts.beauticians.app')
@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
    <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">

    <link rel="stylesheet" href="/vendor/tooltipster.bundle.min.css">
    <link rel="stylesheet" href="/vendor/hopscotch/css/hopscotch.min.css">
@endsection
@section('content')
    <section class="content-header">
        <h1>Calendario</h1>

    </section>

    <section class="content">
        @php
        $user_settings = auth()->user()->getAllSettings();
         @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        @include('beautician.agenda._buttons')

                    </div>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-3">



                <div class="box box-solid box-horario-appointment">
                    <div class="box-header with-border bg-dark">
                        <h3 class="box-title">Horarios Programados</h3> <!-- <small id="currentWeek">Actual</small> -->

                    </div>
                    <div class="box-body">

                        <div id="miniCalendar" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-freeDays="{{ $user_settings['freeDays'] }}"></div>

                    </div>
                </div>

            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box box-default box-calendar medico-calendar">
                    <div class="box-body no-padding">
                        @include('layouts._loading')
                        <!-- THE CALENDAR -->

                        <div id="calendar" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-freeDays="{{ $user_settings['freeDays'] }}"></div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /. box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </section>

    <!-- Modal -->



    <modal-new-appointment-estetica :patient="{{ isset($p) ? $p : 'false' }}" has-subscription="{{ auth()->user()->hasSubscription() }}" :pending-payment="{{ auth()->user()->monthlyCharge() }}" :pending-payment-total="0" endpoint="/beautician/patients"></modal-new-appointment-estetica>
@endsection
@push('scripts')
    <script src="/vendor/select2/js/select2.full.min.js"></script>
    <script src="/vendor/moment/min/moment.min.js"></script>
    <!-- <script src="/vendor/fullcalendar/jquery-ui.min.js"></script> -->
    <script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
    <script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="/vendor/fullcalendar/dist/locale/es.js"></script>
    <script src="/vendor/jquery.ui.touch-punch.min.js"></script>
    <!-- <script src="/vendor/sweetalert2/sweetalert2.min.js"></script> -->

    <script src="/vendor/tooltipster.bundle.min.js"></script>
    <script src="/vendor/hopscotch/js/hopscotch.min.js"></script>

    @vite('resources/js/agenda.js')
    <script>
        $(function() {
            var tour = {
                id: "hello-hopscotch",

                i18n: {
                    nextBtn: "Siguiente",
                    prevBtn: "Atras",
                    doneBtn: "Listo"
                },

                steps: [{
                        title: "Crear Cita",
                        content: "Selecciona una hora en el calendario",
                        target: "#calendar .fc-thu",
                        placement: "top",

                    }

                ],
                onEnd: function() {

                    localStorage.setItem("tour_crear_cita_viewed", 1)

                }

            };

            if (!localStorage.getItem("tour_crear_cita_viewed")) {
                hopscotch.startTour(tour);
            }


            $('body').on('mouseenter', '.tooltip:not(.tooltipstered)', function() {
                $(this)
                    .tooltipster({
                        theme: ['tooltipster-noir', 'tooltipster-gps']
                    })
                    .tooltipster('open');
            });
        });
    </script>
@endpush
