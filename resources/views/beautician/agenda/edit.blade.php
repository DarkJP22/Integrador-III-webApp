@extends('layouts.beauticians.app')
@section('header')
    <link rel="stylesheet" href="/vendor/iCheck/all.css">
    <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
@endsection
@section('content')

    <section class="content-header">
        <h1>Consulta Médica</h1>

    </section>
    <section class="content">


        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $patient->first_name }}</h3>
                <div class="buttons-appointment-finish" style="position: absolute; right: 18px; top: 3px; z-index: 99">
                    {{-- @if (\Carbon\Carbon::now()->ToDateString() <= $appointment->date)
					<a href="#" class="btn-revalorizar-appointment btn btn-secondary tippyTooltip" title="Permite guardar la consulta temporalmente para poder acceder a ella durante el transcurso del dia">Revaluar</a>
				@endif --}}
                    @if (!$appointment->finished)
                        <a href="#" class="btn-finish-appointment btn btn-danger">Terminar Consulta</a>
                    @endif
                </div>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <strong><i class="fa fa-calendar margin-r-5"></i> {{ $appointment->title }}</strong>

                <p class="text-muted">
                    {{ trans('utils.gender.' . $patient->gender) }} - Edad: {{ $patient->age }}
                </p>
                <p class="text-muted">{{ $patient->phone }}</p>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        @if ($patient->optreatments)
                            <li class="{{ isset($tab) ? ($tab == 'home' ? 'active' : '') : 'active' }}"><a href="#home"
                                    data-toggle="tab">Seguimiento</a></li>
                        @endif
                        <li class="{{ isset($tab) ? ($tab == 'evaluation' ? 'active' : '') : ((!$patient->optreatments) ? 'active': '') }}"><a href="#evaluation"
                                data-toggle="tab">Evaluacion Física</a></li>
                        @if (Optional($appointment->optreatment)->category === 'corporal')
                            <li class="{{ isset($tab) ? ($tab == 'anthropometry' ? 'active' : '') : '' }}"><a
                                    href="#anthropometry" data-toggle="tab">Antropometria</a></li>
                        @endif
                        <li class="{{ isset($tab) ? ($tab == 'treatment' ? 'active' : '') : '' }}"><a href="#treatment"
                                data-toggle="tab">Tratamiento</a></li>
                        <li class="{{ isset($tab) ? ($tab == 'documentation' ? 'active' : '') : '' }}"><a
                                href="#documentation" data-toggle="tab">Documentación</a></li>
                        <li class="{{ isset($tab) ? ($tab == 'recomendations' ? 'active' : '') : '' }}"><a
                                href="#recomendations" data-toggle="tab">Recomendaciones</a></li>



                    </ul>
                    <div class="tab-content">
                        @if ($patient->optreatments)
                            <div class="{{ isset($tab) ? ($tab == 'home' ? 'active' : '') : 'active' }} tab-pane"
                                id="home">

                                <reason :patient="{{ $patient }}" :appointment="{{ $appointment }}"
                                    :appointments="{{ $appointments }}"></reason>

                            </div>
                        @endif
                        <!-- /.tab-pane -->
                        {{-- @if ($appointment->optreatment_id) --}}
                        <div class="{{ isset($tab) ? ($tab == 'evaluation' ? 'active' : '') : '' }} tab-pane"
                            id="evaluation">
                            <evaluation :appointment="{{ $appointment }}"
                                :evaluations="{{ json_encode($evaluations) }}"
                                :notes="{{ json_encode($evaluationNotes) }}" :options="{{ $optionsEvaluation }}">
                            </evaluation>
                        </div>
                        @if (Optional($appointment->optreatment)->category === 'corporal')
                            <div class="{{ isset($tab) ? ($tab == 'anthropometry' ? 'active' : '') : '' }} tab-pane"
                                id="anthropometry">
                                <anthropometry :appointment="{{ $appointment }}"
                                    :anthropometry="{{ json_encode($anthropometry) }}"></anthropometry>
                            </div>
                        @endif
                        <div class="{{ isset($tab) ? ($tab == 'treatment' ? 'active' : '') : '' }} tab-pane"
                            id="treatment">
                            <treatment :appointment="{{ $appointment }}"
                                :estreatments="{{ json_encode($treatments) }}"
                                :notes="{{ json_encode($treatmentNotes) }}" :options="{{ $optionsTreatment }}">
                            </treatment>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="{{ isset($tab) ? ($tab == 'documentation' ? 'active' : '') : '' }} tab-pane"
                            id="documentation">

                            <documentation :appointment="{{ $appointment }}" :documentations="{{ $documentations }}">
                            </documentation>
                        </div>
                        <div class="{{ isset($tab) ? ($tab == 'recomendations' ? 'active' : '') : '' }} tab-pane"
                            id="recomendations">

                            <recomendations :appointment="{{ $appointment }}"
                                :recomendations="{{ json_encode($recomendations) }}"
                                :notes="{{ json_encode($recomendationNotes) }}"
                                :options="{{ $optionsRecomendation }}"></recomendations>
                        </div>
                        {{-- @endif --}}
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            {{-- <div class="col-md-3" style="position: relative;">
                <div class="buttons-summary pull-right">
                    <a href="/beautician/agenda/appointments/{{ $appointment->id }}/print" target="_blank"
                        class="btn btn-secondary btn-sm"><i class="fa fa-print"></i></a>
                    <a href="/beautician/agenda/appointments/{{ $appointment->id }}/pdf" class="btn btn-secondary btn-sm"
                        target="_blank">PDF</a>
                </div> --}}


                <summary-esthetic :appointment="{{ $appointment }}" :patient="{{ $patient }}"
                    :evaluations="{{ json_encode($evaluations) }}"
                    :evaluation-notes="{{ json_encode($evaluationNotes) }}"
                    :anthropometry="{{ json_encode($anthropometry) }}" :estreatments="{{ json_encode($treatments) }}"
                    :treatment-notes="{{ json_encode($treatmentNotes) }}"
                    :recomendations="{{ json_encode($recomendations) }}"
                    :recomendation-notes="{{ json_encode($recomendationNotes) }}" :is-current="true"></summary-esthetic>

            {{-- </div> --}}
        </div>

    </section>

@endsection
@push('scripts')
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/moment/min/locales.min.js"></script>
    <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="/vendor/iCheck/icheck.min.js"></script>
    <script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('a[href="#home"]').on('click', function(e) {
                var tab = '?tab=home';
                window.history.pushState({}, null, window.location.pathname + tab);
            })
            $('a[href="#evaluation"]').on('click', function(e) {
                var tab = '?tab=evaluation';
                window.history.pushState({}, null, window.location.pathname + tab);
            })
            $('a[href="#anthropometry"]').on('click', function(e) {
                var tab = '?tab=anthropometry';
                window.history.pushState({}, null, window.location.pathname + tab);
            })
            $('a[href="#treatment"]').on('click', function(e) {
                var tab = '?tab=treatment';
                window.history.pushState({}, null, window.location.pathname + tab);
            })
            $('a[href="#documentation"]').on('click', function(e) {
                var tab = '?tab=documentation';
                window.history.pushState({}, null, window.location.pathname + tab);
            })
            $('a[href="#recomendations"]').on('click', function(e) {
                var tab = '?tab=recomendations';
                window.history.pushState({}, null, window.location.pathname + tab);
            })

            $('.documentation-gallery').magnificPopup({
                delegate: 'a', // the selector for gallery item
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
            $('#datetimepickerDocumentation').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'es',

            });


            $('.btn-finish-appointment').on('click', function(e) {
                $.ajax({
                    type: 'PUT',
                    url: '/agenda/appointments/{{ $appointment->id }}/finish',
                    data: {},
                    success: function(resp) {

                        console.log('cita finalizada');


                        var html = "Que deseas hacer?";


                        Swal.fire({
                            title: 'Consulta Terminada!',
                            html: html,
                            //type: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#67BC9A',
                            cancelButtonColor: '#dd4b39',
                            confirmButtonText: 'Agendar seguimiento',
                            cancelButtonText: 'Volver a agenda',
                            //confirmButtonClass: 'btn btn-success',
                            //cancelButtonClass: 'btn btn-danger',
                            //buttonsStyling: false
                        }).then((result) => {

                            if (result.value) {
                                window.location = '/beautician/patients/' +
                                    {{ $appointment->patient_id }} +
                                    '/agenda/treatment-appointments';

                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location = '/beautician/agenda';
                            } else {
                                //window.location = '/agenda/appointments/{{ $appointment->id }}';
                            }


                        })

                    },
                    error: function() {
                        console.log('error finalizando cita');

                    }

                });


            });



        });
    </script>
@endpush
