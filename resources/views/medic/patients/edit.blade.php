@extends('layouts.medics.app')
@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
@endsection
@section('content')
    <section class="content-header">
        <h1>Paciente {{ $patient->fullname }}</h1>

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
        <div class="row">
            <div class="col-md-4">

                <avatar-form :user="{{ $patient }}" url="/api/patients/"></avatar-form>
                <div class="box box-default">
                    <div class="box-body box-profile ">
                        <emergency-contacts :patient="{{ $patient }}"></emergency-contacts>
                    </div>
                </div>
                @if(auth()->user()->type_of_health_professional === \App\Enums\TypeOfHealthProfessional::MEDICO	)
                    @if(auth()->user()->hasAuthorizationOf($patient->id))
                        <lab-results :patient-id="{{ $patient->id }}" :results="{{ $patient->labresults }}">Resultados de Laboratorio</lab-results>
                    @else
                        <authorization-patient-by-code :patient="{{ $patient }}"></authorization-patient-by-code>
                    @endif
                @endif

            </div>

            <div class="col-md-8">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }}"><a href="#basic" data-toggle="tab">Información Básica</a></li>
                        @if(auth()->user()->type_of_health_professional === \App\Enums\TypeOfHealthProfessional::MEDICO	)
                            <li class="{{ isset($tab) ? ($tab =='history') ? 'active' : '' : '' }}"><a href="#history" data-toggle="tab">Historial Médico</a></li>
                        @endif
                        <li class="{{ isset($tab) ? ($tab =='appointments') ? 'active' : '' : '' }}"><a href="#appointments" data-toggle="tab">Consultas</a></li>


                    </ul>
                    <div class="tab-content">
                        <div class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }} tab-pane" id="basic">
                            <form method="POST" action="{{ url('/general/patients/'.$patient->id) }}" class="form-horizontal">
                                {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
                                @include('patients._form')

                                @can('update', $patient)
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">Actualizar</button>
                                        </div>
                                    </div>
                                @endcan
                            </form>

                        </div>
                        <!-- /.tab-pane -->
                        @if(auth()->user()->type_of_health_professional === \App\Enums\TypeOfHealthProfessional::MEDICO	)
                            <div class="{{ isset($tab) ? ($tab =='history') ? 'active' : '' : '' }} tab-pane" id="history">
                                <patient-clinic-history :patient="{{ $patient }}"></patient-clinic-history>
                            </div>
                        @endif
                        <!-- /.tab-pane -->
                        <div class="{{ isset($tab) ? ($tab =='appointments') ? 'active' : '' : '' }} tab-pane" id="appointments">
                            @if(auth()->user()->hasAuthorizationOf($patient->id))
                                @include('patients._buttonInitAppointment')

                                @include('appointments._historical')
                            @else
                                <authorization-patient-by-code :patient="{{ $patient }}"></authorization-patient-by-code>
                            @endif
                        </div>
                        <!-- /.tab-pane -->


                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
                @include('patients._medicines')

            </div>

        </div>
    </section>

    @include('patients._initAppointment')
    @include('layouts._modal-subscriptions')
    @include('layouts._modal-pending-payments')

    <form method="post" id="form-delete" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>
@endsection
@push('scripts')
    <script src="/vendor/select2/js/select2.full.min.js"></script>
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/moment/locale/es.js"></script>
    <script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
    <script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="/vendor/fullcalendar/dist/locale/es.js"></script>
    @vite('resources/js/patients.js')

@endpush

