@extends('layouts.beauticians.app')
@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
    <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="/vendor/tooltipster.bundle.min.css">
    <link rel="stylesheet" href="/vendor/hopscotch/css/hopscotch.min.css">
    <style>
        .tooltipster-sidetip.tooltipster-noir.tooltipster-gps .tooltipster-box {
            background: #605ca8;
            border: 3px solid #605ca8;
            border-radius: 6px;
            box-shadow: 5px 5px 2px 0 rgba(0, 0, 0, 0.4);
        }

        .tooltipster-sidetip.tooltipster-noir.tooltipster-gps .tooltipster-content {
            color: white;
            padding: 8px;
        }
    </style>
@endsection
@section('content')
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


                <!-- /. box -->


                @include('schedules._modal-wizard')


                <div class="form-horizontal">
                    <div class="form-group">

                        <label for="selectSlotDuration" class="col-sm-7 control-label">Pacientes cada: </label>
                        <div class="col-sm-5">
                            <select name="selectSlotDuration" id="selectSlotDuration" class="form-control">
                                @foreach ($slotDurations as $slot)

                                    <option value="{{ $slot->value }}" {{ $user_settings ? ($user_settings['slotDuration'] == $slot->value ? 'selected' : '') : '' }}>{{ $slot->text }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>


                <div class="box box-solid box-offices">
                    <div class="box-header with-border">
                        <h4 class="box-title">Agenda </h4>

                        <div><small>(Arrastra los elementos coloreados hacia la hora deseada para programar tu agenda y expande hasta el fin de la jornada o has click sobre la hora para crear una programación)</small></div>

                    </div>
                    <div class="box-body">
                        <!-- the events -->
                        <div id="external-offices">

                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>

                @include('schedules._copySchedule')




            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box box-default box-calendar medico-calendar">
                    <div class="box-body no-padding">
                        @include('layouts._loading')
                        <!-- THE CALENDAR -->

                        <div id="calendar" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-freeDays="{{ $user_settings['freeDays'] }}" data-schedule="{{ $wizard }}"></div>
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
@endsection

@push('scripts')
    <script src="/vendor/select2/js/select2.full.min.js"></script>
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
    <script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="/vendor/fullcalendar/dist/locale/es.js"></script>
    <script src="/vendor/jquery.ui.touch-punch.min.js"></script>
    <script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
    <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="/vendor/tooltipster.bundle.min.js"></script>
    <script src="/vendor/hopscotch/js/hopscotch.min.js"></script>

    @vite('resources/js/schedule.js')
    <script>
        $(function() {
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
