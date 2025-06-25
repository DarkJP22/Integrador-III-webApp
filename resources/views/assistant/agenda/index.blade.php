@extends('layouts.assistants.app')
@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
    <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
@endsection
@section('content')

    <section class="content-header">
        <h1>
            Agenda del dia @if ($medic)
                <small>Dr(a). {{ $medic->name }}</small>
            @endif
        </h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-4">




                <div class="box box-solid box-medics">
                    <div class="box-header with-border">
                        <h4 class="box-title">Médicos </h4> <a href="/assistant/agenda/rooms" class="pull-right"> Vista Salas</a>
                        <div><small>(Haz click en un medico para ver su agenda de citas)</small></div>
                        <div>
                            <form action="/assistant/agenda" method="GET">


                                <input type="text" name="q" placeholder="Buscar..." class="form-control" value="{{ isset($q) ? $q : '' }}">
                            </form>

                        </div>
                    </div>
                    <div class="box-body">
                        <!-- the events -->
                        <div id="external-medics">
                            <ul class="medic-list medic-list-in-box">
                                @foreach ($medics as $doctor)
                                    <li class="item {{ isset($medic) && $doctor->id == $medic->id ? 'medic-list-selected' : '' }}">
                                        <div class="medic-img">
                                            <!--/img/default-50x50.gif-->

                                            <img src="{{ $doctor->avatar_path }}" alt="Medic Image" width="50" height="50">
                                        </div>
                                        <div class="medic-info">
                                            <a href="/assistant/agenda?medic={{ $doctor->id }}{{ request('page') ? '&page=' . request('page') : '' }}&q={{ isset($q) ? $q : '' }}" class="medic-title">{{ $doctor->name }}
                                            </a>


                                            <a href="/assistant/agenda?medic={{ $doctor->id }}{{ request('page') ? '&page=' . request('page') : '' }}&q={{ isset($q) ? $q : '' }}" class="btn btn-primary btn-sm pull-right" title="Ver calendario"><i class="fa fa-calendar"></i></a>
                                            <!-- <a href="/agenda/print?medic={{ $doctor->id }}" class="btn btn-secondary btn-sm pull-right" target="_blank" title="Imprimir Agenda"><i class="fa fa-print"></i></a> -->
                                            <button-agenda-print :url="'/agenda/print?medic={{ $doctor->id }}'"></button-agenda-print>

                                            <span class="medic-description">
                                                @if ($doctor->specialities->count())
                                                    @foreach ($doctor->specialities as $speciality)
                                                        {{ $speciality->name }}
                                                    @endforeach
                                                @else
                                                    Médico General
                                                @endif
                                            </span>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                            @if ($medics)
                                <div class="pagination-container">{!! $medics->render() !!}</div>
                            @endif

                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>



            </div>
            <!-- /.col -->
            <div class="col-md-8">

                @if ($medic)
                    <div class="box box-default box-calendar">
                        <div class="box-body no-padding">
                            @include('layouts._loading')
                            <!-- THE CALENDAR -->
                            @include('agenda._simbologia')
                           @php
                               $user_settings = $medic->getAllSettings();
                           @endphp
                            <div id="calendar" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-freeDays="{{ $user_settings['freeDays'] }}" data-medic="{{ $medic->id }}" data-office="{{ $office->id }}" data-currentDate="{{ $currentDate }}"></div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /. box -->
                @else
                    <div class="box box-default box-calendar">
                        <div class="box-body ">
                            @include('layouts._loading')
                            <!-- THE CALENDAR -->
                            <div class="callout callout-info">
                                <h4>Información importante!</h4>

                                <p>Selecciona un Médico para ver su agenda completa</p>
                            </div>
                            @include('agenda._simbologia')
                            <div class="table-responsive table-responsive2">

                                <table class="table table-bordered">
                                    <thead>

                                        <tr>
                                            @foreach ($medicsWithSchedules as $doc)
                                                <th class="text-center">
                                                    <div>{{ $doc->name }}</div>
                                                    <small><span class="label label-secondary">
                                                            @if ($doc->specialities->count())
                                                                @foreach ($doc->specialities as $speciality)
                                                                    {{ $speciality->name }}
                                                                @endforeach
                                                            @else
                                                                Médico General
                                                            @endif
                                                        </span></small>
                                                    <div class="input-group input-group-sm" style="margin-top: 1rem;">


                                                        <input type="text" class="date form-control" name="go_date" id="go_date_{{ $doc->id }}">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-primary btn-flat btn-gotodate" data-mid="{{ $doc->id }}">Ir!</button>
                                                        </span>
                                                    </div>
                                                </th>
                                            @endforeach

                                        </tr>


                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach ($medicsWithSchedules as $doc)
                                                <td class="calendar-medic-day">
                                                    @php
                                                        $user_settings = $doc->getSettings(['slotDuration', 'minTime', 'maxTime', 'freeDays']);
                                                    @endphp
                                                    <div id="calendar-m{{ $doc->id }}" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-freeDays="{{ $user_settings['freeDays'] }}" data-medic="{{ $doc->id }}" data-office="{{ $office->id }}"></div>
                                                </td>
                                            @endforeach
                                        </tr>

                                    </tbody>
                                </table>

                            </div>



                        </div>
                        <!-- /.box-body -->
                    </div>
                @endif
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </section>

    <modal-clinic-new-appointment :patient="{{ isset($p) ? $p : 'false' }}" :office="{{ $office->id }}" :optreatments="{{ $optreatments }}" :is-esthetic="{{ $office->is_esthetic ?? 'false' }}" endpoint="/assistant/patients">

    </modal-clinic-new-appointment>



    <form method="post" id="form-active-inactive">
        {{ csrf_field() }}
    </form>

@endsection
@push('scripts')
    <!-- <script src="https://unpkg.com/vue-select@1.3.3"></script>cv -->
    <script src="/vendor/select2/js/select2.full.min.js"></script>
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
    <script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="/vendor/fullcalendar/dist/locale/es.js"></script>
    <script src="/vendor/jquery.ui.touch-punch.min.js"></script>
    <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="/vendor/tooltipster.bundle.min.js"></script>

    @if ($medic)
        @vite('resources/js/medicAgenda.js')
    @else
        @vite('resources/js/dailyAgenda.js')
    @endif
@endpush
