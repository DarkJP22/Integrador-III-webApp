@extends('layouts.assistants.app')
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
    @php
        $user_settings = $medic->getAllSettings();
    @endphp
    <section class="content-header">
        <h1>
            Dr(a). {{ $medic->name }}
        </h1>

    </section>
    <section class="content">


        <div class="row">
            <div class="col-md-3">


                <!-- /. box -->


                @include('assistant.schedules._modal-wizard')


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

                        <div><small>(Arrastra los elementos coloreados hacia la hora deseada para programar tu agenda y
                                expande hasta el fin de la jornada o has click sobre la hora para crear una
                                programación)</small></div>

                    </div>
                    <div class="box-body">
                        <!-- the events -->
                        <div id="external-offices">

                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>

                @include('assistant.schedules._copySchedule')


            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box box-default box-calendar medico-calendar">
                    <div class="box-body no-padding">
                        @include('layouts._loading')
                        <!-- THE CALENDAR -->

                        <div id="calendar" data-slotDuration="{{ $user_settings['slotDuration'] }}"
                             data-minTime="{{ $user_settings['minTime'] }}"
                             data-maxTime="{{ $user_settings['maxTime'] }}"
                             data-freeDays="{{ $user_settings['freeDays'] }}" data-schedule="{{ $wizard }}"
                             data-medic="{{ $medic->id }}" data-office="{{ $office->id }}"></div>
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

    @vite('resources/js/assistantSchedule.js')
    <script>
        $(function () {
            $('body').on('mouseenter', '.tooltip:not(.tooltipstered)', function () {
                $(this)
                    .tooltipster({
                        theme: ['tooltipster-noir', 'tooltipster-gps']
                    })
                    .tooltipster('open');
            });

            // var get_calendar;
            // var calendar, endDay, firstDay, firstWeekDay, headerRow, i, j, lastWeekDay, len, len1, month, monthRange, row, startDate, week, weekRange, weeks, year,
            //   indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

            // get_calendar = function(year, month) {
            //   startDate = moment([year, month]);
            //   firstDay = moment(startDate).startOf('month');
            //   endDay = moment(startDate).endOf('month');
            //   monthRange = moment.range(firstDay, endDay);
            //   weeks = [];
            //   monthRange.by('days', function(moment) {
            //     var ref;
            //     ref = void 0;
            //     if (ref = moment.week()) {
            //       indexOf.call(weeks, ref) < 0;
            //       return weeks.push(moment.week());
            //     }
            //   });
            //   calendar = [];
            //   i = 0;
            //   len = weeks.length;
            //   while (i < len) {
            //     week = weeks[i];
            //     if (i > 0 && week < weeks[i - 1]) {
            //       firstWeekDay = moment([year, month]).add(1, 'year').week(week).day(1);
            //       lastWeekDay = moment([year, month]).add(1, 'year').week(week).day(7);
            //     } else {
            //       firstWeekDay = moment([year, month]).week(week).day(1);
            //       lastWeekDay = moment([year, month]).week(week).day(7);
            //     }
            //     weekRange = moment.range(firstWeekDay, lastWeekDay);
            //     calendar.push(weekRange);
            //     i++;
            //   }
            //   return calendar;
            // };
            // console.log(get_calendar(2018, 12));
        });
    </script>
@endpush
