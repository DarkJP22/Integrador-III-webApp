$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendar = $('#calendar');
    var slotDuration = calendar.attr('data-slotDuration');//'00:30:00';
    var minTime = '06:00:00';
    var maxTime = '18:00:00';
    var eventDurationNumber = moment.duration(slotDuration).asMinutes();
    var eventDurationMinHours = 'minutes';

    var freeDays = [0];
    var businessHours = [1, 2, 3, 4, 5, 6, 0];
    var externalEvent = $('.external-event');
    //var infoBox = $('#infoBox');
    var modalForm = $('#myModal');
    var modalReminder = $('#modalReminder');
    var noCrear = false;

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
    } 

    $('.dropdown-toggle').dropdown();


    function isOverlapping(event) {

        var array = calendar.fullCalendar('clientEvents');

        for (const i in array) {
            if (event._id != array[i]._id && !array[i].rendering) {
                if (event.end > array[i].start && event.start < array[i].end) {
                    return true;
                }
            }
        }
        return false;
    }

    // function dayNumber(date) {

    //     return $.fullCalendar.moment(date).day();
    // }

    ini_events($('#external-events div.external-event'));

    function ini_events(ele) {

        ele.each(function () {

            var eventObject = {
                title: $.trim($(this).text()), // use the element's text as the event title
                user_id: $(this).data('doctor'),
                patient_id: $(this).data('patient'),
                office_id: $(this).data('office'),
                start: moment(),
                end: moment(),
                backgroundColor: $(this).css('background-color'),
                borderColor: $(this).css('border-color') ? $(this).css('border-color') : $(this).css('background-color'),
                constraint: 'availableForReservation'


            };

            // store the Event Object in the DOM element so we can get to it later
            $(this).data('event', eventObject);

            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex: 1070,
                revert: true, // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });

        });


    } //init events

    var appointments = [];
    var schedules = [];
    initCalendar(appointments, schedules);

    function initCalendar(appointments/*, schedules*/) {
        minTime = calendar.attr('data-minTime') ? calendar.attr('data-minTime') : '06:00:00';
        maxTime = calendar.attr('data-maxTime') ? calendar.attr('data-maxTime') : '18:00:00';

        businessHours = [1, 2, 3, 4, 5, 6, 0];

        for (const d in businessHours) {
            for (const f in freeDays) {
                if (freeDays[f] == businessHours[d]) {
                    var index = businessHours.indexOf(businessHours[d]);
                    if (index > -1) {
                        businessHours.splice(index, 1);
                    }

                }
            }
        }

        calendar.fullCalendar({
            locale: 'es',
            defaultView: 'agendaWeek',
            timeFormat: 'h(:mm)a',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'agendaWeek,agendaDay'
            },
            //Random default events
            events: appointments,
            forceEventDuration: true,
            slotDuration: slotDuration,
            defaultTimedEventDuration: slotDuration,
            editable: false,
            droppable: true, // this allows things to be dropped onto the calendar !!!
            //eventOverlap: false,
            //businessHours: schedules,
            businessHours: {
                // days of week. an array of zero-based day of week integers (0=Sunday)
                dow: businessHours,//[ 1, 2, 3, 4, 5, 6], // Monday - Thursday

                start: minTime, // a start time (10am in this example)
                end: maxTime, // an end time (6pm in this example)
            },
            eventConstraint: 'businessHours',
            minTime: minTime,
            maxTime: maxTime,
            scrollTime: minTime,
            nowIndicator: true,
            timezone: 'local',
            allDaySlot: false,
            
            eventReceive: function (event) {
              
                var currentDate = new Date();
                if (event.start < currentDate || noCrear) {

                    calendar.fullCalendar('removeEvents', event._id);
                    // nocrear = false;
                    return false;
                }

                if (isOverlapping(event)) {

                    flash('Hora no permitida!. Se encuentra ocupada', 'danger');

                    calendar.fullCalendar('removeEvents', event._id);

                    return false;
                }

                saveAppointment(event);
            },
            eventResize: function (event, delta, revertFunc) {


                updateAppointment(event, revertFunc);


            },
            eventDrop: function (event, delta, revertFunc) {
              
                var currentDate = new Date();


                if (event.start < currentDate) {

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

                    revertFunc();

                    return false;
                }

               

                updateAppointment(event, revertFunc);


            },
           
            eventRender: function (event, element) {
               
            
                element.append('<span class=\'appointment-details\' ></span>');

                if (event.rendering == 'background') {
                    element.append('<span class="title-bg-event">'+ event.title +'</span>');

                }
                element.append('<div data-createdby="' + event.created_by + '"></div>');
                element.append('<div data-id="' + event.id + '"></span>');

                var horaStart = event.start.format('HH:mm');
                var horaEnd = (event.end) ? event.end.format('HH:mm') : '';
                var officeInfoDisplay = '';
                if (event.patient_id && event.patient) {
                    officeInfoDisplay = '';
                    if (event.office) {
                        var officeInfo = event.office;//JSON.parse(event.office_info);

                        officeInfoDisplay = 'en ' + officeInfo.type + ' ' + officeInfo.name + ' <br>Dirección: ' + officeInfo.address + ', ' + officeInfo.provinceName; //+ ', Tel: <a href="tel:' + officeInfo.phone + '">' + officeInfo.phone + '</a><br>'


                    }
                    if (event.created_by == $('.external-event').data('createdby')) {
                        element.find('.appointment-details').click(function () {

                            Swal.fire({
                                title: 'Cita con el Paciente ' + event.patient.first_name,
                                html: 'Fecha: ' + event.start.format('YYYY-MM-DD') + ' De: ' + horaStart + ' a: ' + horaEnd + ' <br>' + officeInfoDisplay,
                                showCancelButton: true,
                                confirmButtonColor: '#67BC9A',
                                cancelButtonColor: '#dd4b39',
                                cancelButtonText: 'Ok',
                                confirmButtonText: 'Eliminar cita'
                            }).then((result) => {

                                if (result.value) {
                                    var resp = deleteAppointment(event.id, event);

                                    if (resp) {

                                        Swal.fire(
                                            'Cita cancelada!',
                                            'Tu cita ha sido eliminada del calendario.',
                                            'success'
                                        );
                                    }
                                }

                            });




                        });
                    }


                } else {
                   
                    if(event.rendering != 'background'){
                        officeInfoDisplay = '';
                        var titleAlert = event.title;
                        var textAlert = 'Fecha: ' + event.start.format('YYYY-MM-DD') + ' De: ' + horaStart + ' a: ' + horaEnd + officeInfoDisplay;


                        if (event.office) {
                            officeInfo = event.office;//JSON.parse(event.office);

                            officeInfoDisplay = '<br>Dirección: ' + officeInfo.address + ', ' + officeInfo.province + ' <br>';

                            titleAlert = 'Este horario está reservado para atención en ' + officeInfo.type + ' ' + officeInfo.name;

                            textAlert = 'Favor llamar a este número: <a href="tel:' + officeInfo.phone + '">' + officeInfo.phone + '</a> <br>Fecha: ' + event.start.format('YYYY-MM-DD') + ' De: ' + horaStart + ' a: ' + horaEnd + officeInfoDisplay;
                        }
                        element.find('.appointment-details').click(function () {

                            Swal.fire({
                                title: titleAlert,
                                html: textAlert

                            });



                        });

                    }


                }

            },
            dayClick: function (date, jsEvent/*, view*/) {
                var currentDate = new Date();

                if (date < currentDate || $(jsEvent.target).hasClass('fc-nonbusiness')) {

                    flash('Hora no permitida!.No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');
                    return false;
                }

                if ($(jsEvent.target).parent('div').hasClass('fc-bgevent')) { //para prevenir que en eventos de fondo se agregue citas

                    if (calendar.attr('data-appointmentsday') >= 2) {
                        flash('No se puede crear más de dos citas al dia!!', 'danger');


                        return false;
                    }


                    modalForm.modal({ backdrop: 'static', show: true });
                    modalForm.find('.modal-body').attr('data-modaldate', date.format());
                    modalForm.find('.modal-body').attr('data-date', date.format('dddd, MMMM Do YYYY')).attr('data-hour', date.format('hh:mm a'));
                
                }else{
                    flash('Hora no permitida!.No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');
                    return false;
                }

               



            },
            viewRender: function (view) {
                console.log(view.start.format() + ' - ' + view.end.format());
               
                calendar.fullCalendar('removeEventSources');

                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/medics/' + externalEvent.data('doctor') + '/schedules?office=' + externalEvent.data('office') + '&date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {

                        var schedules = [];
                        $.each(resp, function (index, item) {

                            item.allDay = parseInt(item.allDay); // = false;
                            item.id = 'availableForReservation';
                            item.rendering = 'background';
                            item.title='Disponible';
                            schedules.push(item);
                           

                        });

                       

                        calendar.fullCalendar('addEventSource', schedules);
                        




                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }

                }); //ajax schedules

              

                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/medics/' + externalEvent.data('doctor') + '/appointments?calendar=1&office=' + externalEvent.data('office') + '&date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {


                        var appointments = [];

                        $.each(resp, function (index, item) {

                            item.allDay = parseInt(item.allDay); // = false;

                            if ((item.patient_id != 0 && item.created_by != externalEvent.data('createdby')) || item.patient_id == 0) {
                                //item.rendering = 'background';
                                item.title = 'Ocupado';
                            }
                            item.constraint = 'availableForReservation';
                            

                            appointments.push(item);
                        });

                       

                        calendar.fullCalendar('addEventSource', appointments);
                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }
                }); // ajax appointments



            } //view render


        }); //fullcalendar

        calendar.fullCalendar('today');

    } //init calendar


    function crud(method, url, data, revertFunc) {
        $('.loader').show();

        $.ajax({
            type: method || 'POST',
            url: url,
            dataType: 'json',
            data: data,
            success: function (resp) {
                $('.loader').hide();

                if (method == 'POST') {
                    // calendar.fullCalendar('removeEvents', data.idRemove)

                    if (resp == 'max-per-day') {
                        flash('No se puede crear más de dos citas al dia!!', 'danger');
                        
                        return;
                    }
                    /*if(isOverlapping(resp))
                      resp.allDay = 1; // si se montan poner el evento en todo el dia*/

                    resp.appointment.allDay = parseInt(resp.appointment.allDay);

                    if (resp.appointment.allDay) {

                        deleteAppointment(resp.appointment.id);

                    } else {

                        calendar.fullCalendar('renderEvent', resp.appointment, true);
                        calendar.attr('data-appointmentsday', resp.appointmentsToday);

                        modalReminder.modal({ backdrop: 'static', show: true });
                        modalReminder.find('.modal-body').attr('data-appointment', resp.appointment.id).show();




                    }

                   
                }
                if (method == 'DELETE') {

                    calendar.fullCalendar('removeEvents', data.idRemove);
                    calendar.attr('data-appointmentsday', resp.appointmentsToday);
                    modalForm.find('.btn-finalizar-cita').attr('data-appointment', '');
                    modalForm.find('.btn-cancelar-cita').attr('data-appointment', '');


                }

                
                calendar.fullCalendar('refetchEvents');

            },
            error: function (resp) {
                $('.loader').hide();
                if (revertFunc) {
                    revertFunc();
                }
                calendar.fullCalendar('refetchEvents');
                flash(resp.responseJSON.message, 'danger');

            }
        });
    } //CRUD

    function saveAppointment(event) {
       
        var appointment = {
            title: event.title,
            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            backgroundColor: event.backgroundColor, //Success (green)
            borderColor: event.borderColor,
            user_id: event.user_id,
            patient_id: (event.patient_id) ? event.patient_id : 0,
            office_id: (event.office_id) ? event.office_id : 0,
            office_info: (event.office_info) ? event.office_info : '',
            allDay: 0

        };

        // if (isOverlapping(appointment)) {
        //     appointment.allDay = 1;
        // }

        crud('POST', '/reservations', appointment);

    } //save appointment

    function updateAppointment(event, revertFunc) {

        var appointment = {
            title: event.title,
            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            patient_id: event.patient_id,
            office_id: event.office_id,
            id: event.id,
            office_info: event.office_info,
            allDay: event.allDay ? 1 : 0
        };

        crud('PUT', '/reservations/' + appointment.id, appointment, revertFunc);


    } // update appointment

    function deleteAppointment(id) {

        crud('DELETE', '/reservations/' + id , { idRemove: id });

    }// delete appointments


    modalForm.on('shown.bs.modal', function () {
        //
        // var button = $(event.relatedTarget) // Button that triggered the modal
        var date = $(this).find('.modal-body').attr('data-date'); // Extract info from data-* attributes
        var hour = $(this).find('.modal-body').attr('data-hour');

        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this);
        modal.find('.modal-title').html('Su cita se programará para el  <span class="label bg-primary">' + date + '</span> a las <span class="label bg-primary">' + hour + '</span>');
        //modal.find('.modal-body').data('appointment','');
    });


    function createEventFromModal() {
        var event = $('div.external-event');
        var date = $.fullCalendar.moment(modalForm.find('.modal-body').attr('data-modaldate'));
        var patient_id = modalForm.find('.widget-user-2').attr('data-patient');
        var eventObject = {
            title: $.trim(event.text()), // use the element's text as the event title
            user_id: event.data('doctor'),
            patient_id: patient_id,
            office_id: event.data('office')

        };

        var originalEventObject = eventObject;

        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);

        // assign it the date that was reported
        copiedEventObject.start = date;

        if (date.isValid()) {


            copiedEventObject.allDay = false;//allDay;
            copiedEventObject.backgroundColor = event.css('background-color');
            copiedEventObject.borderColor = event.css('border-color') ? event.css('border-color') : event.css('background-color');
            copiedEventObject.overlap = false;

            
            calendar.fullCalendar('renderEvent', copiedEventObject);

            saveAppointment(copiedEventObject);
        }
        //Remove event from text input

        modalForm.find('.modal-body').attr('data-modaldate', '');
        modalForm.modal('hide');



    } //create from modal

    $('.btn-finalizar-reminder').on('click', function (e) {
        e.preventDefault();

        var id = modalReminder.find('.modal-body').attr('data-appointment');
        var reminder_time = modalReminder.find('#reminder_time').val();
        $('.loader').show();

        $.ajax({
            type: 'POST',
            dataType:'json',
            url: '/appointments/' + id + '/reminders',
            data: { reminder_time: reminder_time },
            success: function () {

                $('.loader').hide();

                modalReminder.modal('hide');
            },
            error: function () {

                $('.loader').hide();

                console.log('error saving reminder');

                modalReminder.modal('hide');

            }
        });

    });

    $('.btn-finalizar-cita').on('click', function (e) {
        e.preventDefault();
        createEventFromModal();

    });



});
