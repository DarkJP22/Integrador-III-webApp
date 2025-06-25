@extends('layouts.medics.app')
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
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        @include('agenda._buttons')

                    </div>

                </div>

            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $patient->first_name }}</h3>
                <div class="buttons-appointment-finish" style="position: absolute; right: 18px; top: 3px; z-index: 99">

                    @if (!$appointment->finished)
                        <a href="#" class="btn-revalorizar-appointment btn btn-secondary tippyTooltip"
                           title="Permite guardar la consulta temporalmente para poder acceder a ella durante el transcurso del dia">Revaluar</a>
                        <a href="#" class="btn-finish-appointment btn btn-danger">Terminar Consulta</a>
                    @endif
                    @if ($appointment->revalorizar && \Carbon\Carbon::now()->ToDateString() <= $appointment->date->addWeek())
                        <span class="label label-warning">Consulta en revaluación</span>
                    @elseif($appointment->finished)
                        <span class="label label-danger">Consulta Finalizada</span>
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
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#history" data-toggle="tab">Historial</a></li>
                        <li><a href="#notes" data-toggle="tab">Notas de padecimiento</a></li>
                        {{--                        <li><a href="#physical" data-toggle="tab">Examen Fisico</a></li>--}}
                        <li><a href="#labexam" data-toggle="tab">Pruebas Diagnósticas</a></li>
                        <li><a href="#diagnostic" data-toggle="tab">Diagnostico y Tratamiento</a></li>
                        <!-- <li><a href="#invoice" data-toggle="tab" class="invoice-tab">Facturar</a></li> -->

                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="history">
                            <div class="row">

                                <div class="col-md-6">
                                    @include('patients._summary-control', ['patient' => $patient, 'history' => $history])
                                    <!-- {{-- @include('patients._medicines-medic', ['patient' => $patient])  --}} -->

                                </div>
                            </div>
                            <div class="row">
                                @foreach ($appointments as $lastAppointment)
                                    <div class="col-md-4">
                                        @can('showAuthorized', $lastAppointment)
                                            <summary-appointment history="" :vital_signs="{{ json_encode($lastAppointment->vitalSigns) }}"
                                                                 :medicines="{{ $lastAppointment->patient->medicines }}" :notes="{{ $lastAppointment->diseaseNotes }}"
                                                                 :exams="{{ $lastAppointment->physicalExams }}" :diagnostics="{{ $lastAppointment->diagnostics }}"
                                                                 :treatments="{{ $lastAppointment->treatments }}" instructions="{{ $lastAppointment->medical_instructions }}"
                                                                 :labexams="{{ $lastAppointment->labexams }}">
                                                Historia Clinica {{ $lastAppointment->id }}
                                                - {{ \Carbon\Carbon::parse($lastAppointment->start)->format('Y-m-d H:i') }} </summary-appointment>
                                        @else
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <i class="fa fa-book"></i>

                                                    <h3 class="box-title">
                                                        Historia Clínica {{ $lastAppointment->id }} - {{ $lastAppointment->start }}<br>
                                                        <small>Dr(a). {{ $lastAppointment->user?->name }}</small>
                                                    </h3>

                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body summary-flex">
                                                    <authorization-patient-by-code :callback="'/agenda/appointments/{{ $appointment->id }}'"
                                                                                   :patient="{{ $lastAppointment->patient }}"></authorization-patient-by-code>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                @endforeach
                            </div>


                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="notes">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Métrica</h3>

                                            <div class="box-tools pull-right">

                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body">

                                            @if ($appointment->vitalSigns)
                                                <signs :signs="{{ $appointment->vitalSigns }}"></signs>
                                            @endif

                                        </div>
                                        <!-- /.box-body -->
                                    </div>

                                </div>
                                <div class="col-md-6">

                                    <disease-notes :notes="{{ $appointment->diseaseNotes }}"
                                                   :read="{{ \Carbon\Carbon::now()->ToDateString() > $appointment->date->addWeek() ? 'true' : 'false' }}"
                                                   :appointment="{{ $appointment }}"></disease-notes>
                                    {{--                                    @if (\Carbon\Carbon::now()->ToDateString() > $appointment->date->addWeek() || $appointment->finished == 1)--}}
                                    {{--                                        @include('patients._files', ['files' => $files, 'read' => true])--}}
                                    {{--                                    @else--}}
                                    {{--                                        @include('patients._files', ['files' => $files])--}}
                                    {{--                                    @endif--}}

                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        {{--                        <div class="tab-pane" id="physical">--}}
                        {{--                            <physical-exams :physical="{{ $appointment->physicalExams }}" :read="{{ \Carbon\Carbon::now()->ToDateString() > $appointment->date->addWeek() ? 'true' : 'false' }}" :appointment="{{ $appointment }}"></physical-exams>--}}
                        {{--                        </div>--}}
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="labexam">
                            <div class="row">
                                <div class="col-md-6">
                                    <lab-exams :patient-id="{{ $appointment->patient_id }}" :appointment-id="{{ $appointment->id }}"
                                               :read="{{ \Carbon\Carbon::now()->ToDateString() > $appointment->date->addWeek() ? 'true' : 'false' }}"
                                               :appointment="{{ $appointment }}"></lab-exams>
                                </div>
                                <div class="col-md-6">
                                    <lab-results :patient-id="{{ $appointment->patient_id }}"
                                                 :read="{{ \Carbon\Carbon::now()->ToDateString() > $appointment->date->addWeek() ? 'true' : 'false' }}"
                                                 :results="{{ $patient->labresults }}" :appointment="{{ $appointment }}"></lab-results>

                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="diagnostic">

                            <div class="row">
                                <div class="col-md-12">
                                    <diagnostics :diagnostics="{{ $appointment->diagnostics }}" :appointment-id="{{ $appointment->id }}"
                                                 :read="{{ \Carbon\Carbon::now()->ToDateString() > $appointment->date->addWeek() ? 'true' : 'false' }}"
                                                 :appointment="{{ $appointment }}"></diagnostics>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="/agenda/appointments/{{ $appointment->id }}/treatment/print" target="_blank" class="btn btn-secondary"
                                       style="position: absolute; right: 18px; top: 6px; z-index: 99"><i class="fa fa-print"></i> Imprimir Receta</a>
                                    <treatments :treatments="{{ $appointment->treatments }}" :appointment-id="{{ $appointment->id }}"
                                                :read="{{ \Carbon\Carbon::now()->ToDateString() > $appointment->date->addWeek() ? 'true' : 'false' }}"
                                                :appointment="{{ $appointment }}"></treatments>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">

                                </div>
                                <div class="col-md-12">
                                    <instructions :appointment="{{ $appointment }}"
                                                  :read="{{ \Carbon\Carbon::now()->ToDateString() > $appointment->date->addWeek() ? 'true' : 'false' }}"></instructions>
                                </div>
                            </div>
                        </div>

                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <div class="col-md-3" style="position: relative;">
                <div class="buttons-summary pull-right">
                    <a href="/agenda/appointments/{{ $appointment->id }}/print" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i></a>
                    <a href="/agenda/appointments/{{ $appointment->id }}/pdf" class="btn btn-secondary btn-sm" target="_blank">PDF</a>
                </div>


                <summary-appointment :history="{{ $history }}" :medicines="{{ $patient->medicines()->where('user_id', auth()->id())->get() }}"
                                     :notes="{{ $appointment->diseaseNotes }}" :exams="{{ $appointment->physicalExams }}" :diagnostics="{{ $appointment->diagnostics }}"
                                     :treatments="{{ $appointment->treatments }}" instructions="{{ $appointment->medical_instructions }}" :labexams="{{ $appointment->labexams }}"
                                     :is-current="true"></summary-appointment>

            </div>
        </div>

    </section>
@endsection
@push('scripts')
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/moment/min/locales.min.js"></script>
    {{-- <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>  --}}
    <script src="/vendor/iCheck/icheck.min.js"></script>
    <script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $(function () {
            // $('#datetimepickerLabResult').datetimepicker({
            // 	format:'YYYY-MM-DD',
            //     locale: 'es',

            //  });
            //  $('#datetimepickerLabExam').datetimepicker({
            // 	format:'YYYY-MM-DD',
            //     locale: 'es',

            //  });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.btn-revalorizar-appointment').on('click', function (e) {
                $.ajax({
                    type: 'PUT',
                    url: '/agenda/appointments/{{ $appointment->id }}/revalorizar',
                    data: {},
                    success: function (resp) {

                        console.log('cita finalizada y con opcion a revalorizar');


                        var html = "Puedes acceder a la consulta más adelante por medio del boton de revalorizar";


                        Swal.fire({
                            title: 'Consulta Terminada Temporalmente!',
                            html: html,
                            //type: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#67BC9A',
                            cancelButtonColor: '#dd4b39',
                            confirmButtonText: 'Ok',

                            //confirmButtonClass: 'btn btn-success',
                            //cancelButtonClass: 'btn btn-danger',
                            //buttonsStyling: false
                        }).then((result) => {

                            if (result.value) {
                                window.location = '/agenda';


                            } else {
                                //window.location = '/agenda/appointments/{{ $appointment->id }}';
                            }


                        })

                    },
                    error: function () {
                        console.log('error finalizando y revalorizando cita');

                    }

                });


            });
            $('.btn-finish-appointment').on('click', function (e) {
                $.ajax({
                    type: 'PUT',
                    url: '/agenda/appointments/{{ $appointment->id }}/finish',
                    data: {},
                    success: function (resp) {

                        console.log('cita finalizada');

                        var urlFacturacion = '/invoices/create?p={{ $appointment->patient_id }}';
                        var html = "Que deseas hacer?";

                        @if (auth()->user()->subscriptionPlanHasFe() ||
                                auth()->user()->permissionCentroMedico())
                            html += "<br><br> <a href='" + urlFacturacion + "' class='btn btn-primary btn-facturar' style='padding: 10px 32px;font-size:17px;'>Facturar</a>";
                        @endif

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
                                window.location = '/agenda/create?clinic={{ $appointment->office_id }}&p={{ $appointment->patient_id }}';

                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location = '/agenda';
                            } else {
                                //window.location = '/agenda/appointments/{{ $appointment->id }}';
                            }


                        })

                    },
                    error: function () {
                        console.log('error finalizando cita');

                    }

                });


            });

            $('body').on('click', '.btn-facturar', function (e) {
                e.preventDefault()

                $.ajax({
                    type: 'PUT',
                    url: '/agenda/appointments/{{ $appointment->id }}/bill',
                    data: {},
                    success: function (resp) {

                        console.log('cita facturada');

                        var urlFacturacion = '/invoices/create?p={{ $appointment->patient_id }}&appointment={{ $appointment->id }}';


                        window.location = urlFacturacion;


                    },
                    error: function () {
                        console.log('error facturando cita');

                    }

                });


            });

        });
    </script>
@endpush
