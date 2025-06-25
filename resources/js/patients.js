$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var miniCalendar = $('#miniCalendar');
    var ulSchedules = $('#schedule-list');
    var isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function () {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    if (isMobile.any()) {
        $('.box-create-appointment').hide();
        $('.breadcrumb').hide();

    } else {
        //$('.box-create-appointment').show();
        $('.box-search-filters').removeClass('collapsed-box');
    }


    $('.dropdown-toggle').dropdown();

    var slotDuration = $('#initAppointment').find('.modal-body').attr('data-slotDuration');
    var eventDurationNumber = moment.duration(slotDuration).asMinutes(); 
    var eventDurationMinHours = 'minutes'; 
    var stepping = moment.duration(slotDuration).asMinutes();


    // $('#datetimepicker1').datetimepicker({
    //     format: 'YYYY-MM-DD',
    //     locale: 'es',
    //     defaultDate: new Date()


    // });
    // $('#datetimepicker2').datetimepicker({
    //     format: 'HH:mm',
    //     stepping: stepping,
    //     defaultDate: new Date()
    //     //useCurrent: false

    // });
    // $('#datetimepicker3').datetimepicker({
    //     format: 'HH:mm',
    //     stepping: stepping,
    //     useCurrent: false//Important! See issue #1075
    // });

    $('#initAppointment').on('shown.bs.modal', function (event) {

        var button = $(event.relatedTarget);
        var patient_id = button.attr('data-patient');
        var patient = button.attr('data-patientname');


        $(this).find('.modal-body').find('#patient-name').text(patient);
        $(this).find('.modal-body').attr('data-modalpatient', patient_id);

        miniCalendar.fullCalendar('render');


    });

    /** load events from db **/
    var appointmentsFromCalendar = [];
    

    function isOverlapping(event) {

        var array = appointmentsFromCalendar;

        for (const i in array) {

            if (event.end > array[i].start && event.start < array[i].end) {
                return true;
            }

        }
        return false;
    }

    $('.search-offices').select2({
        placeholder: 'Selecciona el Consultorio donde quiere iniciar la consulta',
        ajax: {
            url: '/calendars/offices',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term

                };
            },
            processResults: function (data) {


                $('.search-offices').empty();
                var items = [];

                $.each(data, function (index, value) {

                    const item = {
                        id: value.id,
                        text: value.name,
                        office_info: JSON.stringify(value)

                    };
                    items.push(item);
                });

    
                return {
                    results: items,

                };
            }

        },
        templateSelection: function (container) {
            $(container.element).attr('data-office', container.office_info);

            return container.text;
        }
    });


    $('#initAppointment').find('.add-cita').on('click', function (e) {
        e.preventDefault();

        var modal = $('#initAppointment');
        var patient_id = modal.find('.modal-body').attr('data-modalpatient');
        var office_id = modal.find('input[name="office_id"]').val();//modal.find('#search-offices').val();
        var date = modal.find('input[name="date"]').val();
        var start = modal.find('input[name="start"]').val();
        var end = modal.find('input[name="end"]').val();

        //var start = date + 'T'+ ini + ':00';
        // var end = date + 'T'+ ((fin) ? fin : ini) + ':00';

        if (!office_id) {

            $('#initAppointment').find('#search-offices').select2('focus');
            $('#initAppointment').find('#search-offices').select2('open');
           
            flash('Selecciona un consultorio dentro del calendario. Por favor revisar!!!', 'danger');
           
            return false;
        }

        if (!date || !start) {
            flash('Fecha invalida. Por favor revisar!!!', 'danger');

            return false;
        }

        if (moment(start).isAfter(end)) {
            flash('Fecha invalida. La hora de inicio no puede ser mayor que la hora final!!!', 'danger');

            return false;
        }
        var currentDate = new Date();
        
        if (moment(date).isBefore(moment(currentDate).format('YYYY-MM-DD'))) {

            flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

            return false;
        }



        if (moment(start).isSame(end)) {
            end = moment(start).add(eventDurationNumber, eventDurationMinHours).stripZone().format();
        }

        var appointment = {
            title: 'Cita',
            date: date,
            start: start,
            end: end,
            backgroundColor: '#3c8dbc', //Success (green)
            borderColor: '#3c8dbc',
            patient_id: patient_id,
            office_id: office_id,
            office_info: '',
            allDay: 0

        };

        if (isOverlapping(appointment)) {
            
            flash('No se puede iniciar esta cita por que ya hay una cita en el mismo horario. Por favor revisa tus citas programadas !!!', 'danger');
           
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '/appointments',
            data: appointment,
            success: function (resp) {

                resp.allDay = parseInt(resp.allDay);

                $('#initAppointment').find('.add-cita').attr('disabled', 'disabled');
                $('#initAppointment').find('.btn-cancel').attr('disabled', 'disabled');
                flash('Cita Creada Correctamente!!. Iniciando su cita, espere un momento');
                
                setTimeout(function () {
                    
                    modal.find('#patient-name').text('');
                    modal.find('.modal-body').attr('data-modalpatient', '');
                   
                    modal.modal('hide');
                    $('#initAppointment').find('.add-cita').removeAttr('disabled');
                    $('#initAppointment').find('.btn-cancel').removeAttr('disabled');
                    window.location.href = '/agenda/appointments/' + resp.id;
                }, 3000);






            },
            error: function () {
                console.log('error saving appointment');

            }
        });






    });


    initMiniCalendar([]);
    function initMiniCalendar(appointments) {


        miniCalendar.fullCalendar({
            locale: 'es',
            defaultView: 'month',
            timeFormat: 'h(:mm)a',
            header: {
                left: 'prev,next ',
                center: 'title',
                right: ''
            },
            //Random default events
            events: appointments,
            timezone: 'local',
            allDaySlot: false,

            eventRender: function (event, element) {



                //element.append( "<span class='closeon fa fa-trash'></span>" );
                var office_id = (event.office) ? event.office.id : '';
                var office_name = (event.office) ? event.office.name : '';


                var textTooltip = office_name + ' De: ' + event.start.format('HH:mm') + ' a: ' + event.end.format('HH:mm');



                element.append('<span class="appointment-details tooltip" data-office="' + office_id + '" data-officename="' + office_name + '" data-toggle="tooltip" title="' + textTooltip + '"></span>');


                if (event.rendering == 'background') {
                    element.append('<span class="title-bg-event" data-title="' + event.title + '">' + event.title + '</span>');


                }


                //element.append('<div data-createdby="'+ event.created_by +'"></div>');



                element.find('.appointment-details').click(function () {

                    //calendar.fullCalendar('gotoDate', event.date);
                    ulSchedules.empty();
                    $('.schedule-clinic').text(office_name);
                    const schedule = event;

                    var intervals = createIntervalsFromHours(moment(schedule.start).format('YYYY-MM-DD'), moment(schedule.start).format('HH:mm'), moment(schedule.end).format('HH:mm'), eventDurationNumber);


                    //var events = [];
                    //var title = 'Disponible';
                    var startEvent;
                    var endEvent;
                    var reserved;
                    // var appointmentId;
                    var reservedType;
                    for (var i = 0; i < intervals.length - 1; i++) {

                        startEvent = moment(schedule.start).format('YYYY-MM-DD') + 'T' + intervals[i] + ':00';
                        endEvent = moment(schedule.start).format('YYYY-MM-DD') + 'T' + intervals[i + 1] + ':00';
                        reservedType = isReserved(startEvent, endEvent);
                        if (reservedType.res) {
                            if (reservedType.res == 1) {
                                //  title = 'No Disponible';
                                reserved = 1;
                            }
                            else {
                                //  title = 'Reservado';
                                reserved = 2;
                                //appointmentId = reservedType.id;
                            }

                        } else {
                            // title = 'Disponible';
                            reserved = 0;
                        }

                        var startTime = intervals[i] + ':00';
                        var endTime = intervals[i + 1] + ':00';
                        

                        if (!reserved)
                            ulSchedules.append('<option value="' + schedule.office_id + '" data-date="' + moment(schedule.start).format('YYYY-MM-DD') + '" data-office="' + schedule.office_id + '" data-start="' + startEvent + '" data-end="' + endEvent + '"> ' + startTime + ' - ' + endTime + '</option>');

                    }

                    ulSchedules.prepend('<option value="" selected><span style="color:red;">--</span></option>');

                  

                });





            },

            // dayClick: function (date, jsEvent, view) {




            // },
            viewRender: function (view) {
                console.log(view.start.format() + ' - ' + view.end.format());

                miniCalendar.fullCalendar('removeEventSources');

                $.ajax({
                    type: 'GET',
                    url: '/calendars/appointments?calendar=1&date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {

                        appointmentsFromCalendar = [];

                        $.each(resp, function (index, item) {

                            item.allDay = parseInt(item.allDay); // = false;

                            if (item.patient_id == 0) item.rendering = 'background';

                            appointmentsFromCalendar.push(item);
                        });



                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }

                }); //ajax appoitnments

                $.ajax({
                    type: 'GET',
                    url: '/calendars/schedules?date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {


                        var schedules = [];

                        $.each(resp, function (index, item) {

                            item.allDay = parseInt(item.allDay); // = false;

                            /*if(item.patient_id == 0) item.rendering = 'background';*/

                            schedules.push(item);
                        });


                        miniCalendar.fullCalendar('addEventSource', schedules);
                        //calendar.fullCalendar( 'updateEvents', schedules )
                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }
                }); //ajax schedules










            } //view render

        });

    } // init mini calendar

    function createIntervalsFromHours(date, from, until, slot) {

        until = Date.parse(date + ' ' + until);
        from = Date.parse(date + ' ' + from);

        var intervalLength = (slot) ? slot : 30;
        var intervalsPerHour = 60 / intervalLength;
        var milisecsPerHour = 60 * 60 * 1000;

        var max = (Math.abs(until - from) / milisecsPerHour) * intervalsPerHour;

        var time = new Date(from);
        var intervals = [];
        for (var i = 0; i <= max; i++) {
            //doubleZeros just adds a zero in front of the value if it's smaller than 10.
            var hour = doubleZeros(time.getHours());
            var minute = doubleZeros(time.getMinutes());
            intervals.push(hour + ':' + minute);
            time.setMinutes(time.getMinutes() + intervalLength);
        }
        return intervals;
    }

    function doubleZeros(item) {

        return (item < 10) ? '0' + item : item;
    }

    function isReserved(startSchedule, endSchedule) {
        var res = {
            res: 0,
            id: 0
        };

        for (var j = 0; j < appointmentsFromCalendar.length; j++) {

            if (appointmentsFromCalendar[j].end > startSchedule && appointmentsFromCalendar[j].start < endSchedule) {


                res.res = 1;
                res.id = appointmentsFromCalendar[j].id;

            }

        }

        return res;

    }


    ulSchedules.change(function () {
        var selected = $(this).find('option:selected');

        $('input[name="start"]').val(selected.data('start'));
        $('input[name="end"]').val(selected.data('end'));
        $('input[name="date"]').val(selected.data('date'));
        $('input[name="office_id"]').val(selected.data('office'));

    });

});