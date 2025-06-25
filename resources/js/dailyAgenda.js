$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var minTime = '06:00:00';
    var maxTime = '18:00:00';
    var slotDuration = '00:30';
    var eventDurationNumber = moment.duration(slotDuration).asMinutes(); 
    var eventDurationMinHours = 'minutes';
    //let freeDays = [];
    var businessHours = [1, 2, 3, 4, 5, 6, 0];
    var modalForm = $('#myModal');
    //var searchPatients = $('.modal-search-patients');

    //Initialize Select2 Elements
    $('.date').datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'es',

    });


    $('.btn-gotodate').on('click', function () {
        var mid = $(this).data('mid');
        var date = $('#go_date_' + mid).val();
        $('#calendar-m' + mid).fullCalendar('gotoDate', date);
    });


    // function dayNumber(date) {

    //     return $.fullCalendar.moment(date).day();
    // }

    // function isOverlapping(calendar, event) {

    //     var array = calendar.fullCalendar('clientEvents');

    //     for (i in array) {
    //         if (event.idRemove != array[i]._id) {
    //             if (event.end > array[i].start._i && event.start < array[i].end._i) {
    //                 return true;
    //             }
    //         }
    //     }
    //     return false;
    // }

    init_schedules_appointments($('td.calendar-medic-day'));

    function init_schedules_appointments(ele) {

        ele.each(function () {

            var medic = $(this).find('> div').data('medic');
            // var office = $(this).find('> div').data('office');

            // fetch_schedules_and_appointments(medic,office);
            init_calendar(medic, [], []);

        });
    }


    function init_calendar(medic, appointments/*, schedules*/) {

        var calendar = $('#calendar-m' + medic);

        minTime = calendar.attr('data-minTime') ? calendar.attr('data-minTime') : '06:00:00';
        maxTime = calendar.attr('data-maxTime') ? calendar.attr('data-maxTime') : '18:00:00';
        slotDuration = $('#selectSlotDuration').val() ? $('#selectSlotDuration').val() : calendar.attr('data-slotDuration');
        eventDurationNumber = moment.duration(slotDuration).asMinutes(); 
        eventDurationMinHours = 'minutes'; 
        // freeDays = calendar.attr('data-freeDays') ? JSON.parse(calendar.attr('data-freeDays')) : [];


        calendar.fullCalendar({
            locale: 'es',
            defaultView: 'agendaDay',
            timeFormat: 'h(:mm)a',
            events: appointments,
            forceEventDuration: true,
            slotDuration: slotDuration,
            defaultTimedEventDuration: slotDuration,
            editable: true,
            droppable: true, // this allows things to be dropped onto the calendar !!!
            //eventOverlap: false,
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
                if (event.start < currentDate) {

                    calendar.fullCalendar('removeEvents', event._id);

                    return false;
                }

                saveAppointment(calendar, event, event._id);

            },
            eventResize: function (event, _delta, revertFunc/*, jsEvent*/) {


                updateAppointment(calendar, event, revertFunc);


            },
            eventDrop: function (event, delta, revertFunc) {

                var currentDate = new Date();


                if (event.start < currentDate) {

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

                    revertFunc();

                    return false;
                }

                updateAppointment(calendar, event, revertFunc);


            },
            eventRender: function (event, element) {

                if (element.hasClass('fc-nonbusiness')) {

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');
                    
                    return false;
                }

                element.append('<span class=\'appointment-details\' ></span>');

                if (event.rendering == 'background') {
                    element.append('<span class="title-bg-event">' + event.title + '</span>');

                }

                if(event.opacity){
                    $(element).css('opacity', event.opacity);
                    $(element).css('z-index', 2);
                }


                element.append('<div data-createdby="' + event.created_by + '"></div>');

                var horaStart = event.start.format('HH:mm');
                var horaEnd = (event.end) ? event.end.format('HH:mm') : '';

                if (event.patient_id && event.patient) {
                    var officeInfoDisplay = '';

                    if (event.office) {
                        var officeInfo = event.office;//JSON.parse(event.office_info);

                        officeInfoDisplay = 'en ' + officeInfo.type + ' ' + officeInfo.name + ' <br>Dirección: ' + officeInfo.address + ', ' + officeInfo.province + ', Tel: <a href="tel:' + officeInfo.phone + '">' + officeInfo.phone + '</a><br>';


                    }
                    
                    element.find('.appointment-details').click(function () {
                        
                        let html ='<b>Tel. Paciente:</b> <a href="tel:' + event.patient.phone_number + '">'+ event.patient.phone_number +'</a><br>Fecha: ' + event.start.format('YYYY-MM-DD') + ' De: ' + horaStart + ' a: ' + horaEnd + ' <br>' + officeInfoDisplay + '<a href="/agenda/' + event.id + '/print" class="btn btn-primary btn-print" target="_blank">Imprimir</a>';

                        html += event.confirmed ? '<a href="/agenda/' + event.id + '/unconfirm" class="btn btn-secondary btn-unconfirm" target="_blank" data-appointment="' + event.id + '">Desconfirmar</a>' : '<a href="/agenda/' + event.id + '/confirm" class="btn btn-secondary btn-confirm" target="_blank" data-appointment="' + event.id + '">Confirmar</a>';
                        Swal.fire({
                            title: 'Cita con el Paciente ' + event.patient.first_name,
                            html: html,
                            input: 'text',
                            inputPlaceholder:'Motivo eliminar cita',
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Escribe el motivo de la eliminación!';
                                }
                            },
                            showCancelButton: true,
                            confirmButtonColor: '#dd4b39',
                            cancelButtonColor: '#67BC9A',
                            cancelButtonText: 'Ok',
                            confirmButtonText: 'Eliminar cita'
                        }).then((result) => {

                            if (result.value) {
                                var resp = deleteAppointment(calendar, event.id, result.value);

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


            },

            dayClick: function (date, jsEvent, view) {


                if (view.name === 'month') {

                    calendar.fullCalendar('gotoDate', date);
                    calendar.fullCalendar('changeView', 'agendaWeek');

                    return false;
                }

                var currentDate = new Date();


                if (date < currentDate || $(jsEvent.target).hasClass('fc-nonbusiness')) {

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');


                    return false;
                }


                if ($(jsEvent.target).parent('div').hasClass('fc-bgevent')) { //para prevenir que en eventos de fondo se agregue citas
                    modalForm.modal({ backdrop: 'static', show: true });
                    modalForm.find('#modal-new-event').attr('data-modaldate', date.format());
                    modalForm.find('.modal-body').attr('data-modaldate', date.format());
                    modalForm.find('.modal-body').attr('data-date', date.format('dddd, MMMM Do YYYY')).attr('data-hour', date.format('hh:mm a'));
                    modalForm.find('.modal-body').attr('data-medic', calendar.data('medic'));
                    window.emitter.emit('openModalClinicNewAppointment');

                }else{

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

                    return false;
                }






              



            },
            viewRender: function (view) {

                calendar.fullCalendar('removeEventSources');

                $.ajax({
                    type: 'GET',
                    url: '/medics/' + medic + '/calendars/schedules?office=' + calendar.data('office') + '&date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {

                        var schedules = [];
                        $.each(resp, function (index, item) {

                            item.allDay = parseInt(item.allDay); // = false;
                            item.id = 'availableForReservation';
                            item.rendering = 'background';
                            item.schedule = 1;
                            schedules.push(item);

                            // var working_hours = {
                            //     // days of week. an array of zero-based day of week integers (0=Sunday)
                            //     dow: [dayNumber(item.date)], // Monday - Thursday

                            //     start: item.start,//.split('T')[1], // a start time (10am in this example)
                            //     end: item.end//.split('T')[1], // an end time (6pm in this example)
                            // }

                            // schedules.push(working_hours);

                        });

                        // var bh = schedules;//$('#calendar').fullCalendar('option', 'businessHours');


                        // for (var i = bh.length - 1; i >= 0; i--) {

                        //     if (moment(bh[i].start).isBetween(view.start, view.end)) {
                        //         bh[i].start = bh[i].start.split('T')[1];
                        //         bh[i].end = bh[i].end.split('T')[1];
                        //     }

                        // }

                        calendar.fullCalendar('addEventSource', schedules);
                        //calendar.fullCalendar('option', 'businessHours', bh);


                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }

                }); //ajax schedules

                //calendar.fullCalendar('removeEventSources')

                $.ajax({
                    type: 'GET',
                    url: '/medics/' + medic + '/calendars/appointments?calendar=1&office=' + calendar.data('office') + '&date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {


                        var appointments = [];

                        $.each(resp, function (index, item) {

                            item.allDay = parseInt(item.allDay); // = false;

                            if ((item.patient_id != 0 && item.office_id != calendar.data('office')) || item.patient_id == 0) {
                                item.rendering = 'background';
                            }

                            item.constraint = 'availableForReservation';

                            if(item.confirmed){

                                item.backgroundColor = '#dd4b39';
                            }

                            if (item.user_id != medic) {

                                item.backgroundColor = '#FF8F35';
                                item.rendering = 'background';
                                item.opacity = 0.8;
                                item.title = item.room?.name || 'Consultorio';
                                

                                if(item.room && item.room?.name != 'Consultorio'){

                                    appointments.push(item);
                                }

                            }else{
                                appointments.push(item);
                            }

                          
                        });

                        //init_calendar(medic, appointments, schedules);
                        calendar.fullCalendar('addEventSource', appointments);

                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }
                }); // ajax appointments



            } // view render

        }); //fullcalendar

    } //init calendar


    function crud(calendar, method, url, data, revertFunc) {
        $('.loader').show();
        $.ajax({
            type: method || 'POST',
            dataType: 'json',
            url: url,
            data: data,
            success: function (resp) {
                $('.loader').hide();
                if (method == 'POST') {

                    resp.allDay = parseInt(resp.allDay);

                    if (resp.allDay) {

                        deleteAppointment(resp.id);

                    } else {

                        calendar.fullCalendar('renderEvent', resp, true);

                    }

                   


                }
                if (method == 'DELETE') {

                    calendar.fullCalendar('removeEvents', data.idRemove);

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

    function saveAppointment(calendar, event) {
        slotDuration = calendar.attr('data-slotDuration');
        eventDurationNumber = moment.duration(slotDuration).asMinutes();
        eventDurationMinHours = 'minutes';

        var appointment = {
            title: event.title,
            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            backgroundColor: event.backgroundColor, //Success (green)
            borderColor: event.borderColor,
            office_id: event.office_id,
            patient_id: (event.patient_id) ? event.patient_id : 0,
            user_id: event.user_id,
            room_id: event.room_id,
            allDay: 0,
            optreatment_ids: event.optreatment_ids,

        };

        // if (isOverlapping(calendar, appointment)) {
        //     appointment.allDay = 1;
        // }

        crud(calendar, 'POST', '/appointments', appointment);

    }//save appointment

    function updateAppointment(calendar, event, revertFunc) {
        slotDuration = calendar.attr('data-slotDuration');
        eventDurationNumber = moment.duration(slotDuration).asMinutes();
        eventDurationMinHours = 'minutes';

        var appointment = {

            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            patient_id: event.patient_id,
            id: event.id,
            allDay: event.allDay ? 1 : 0
        };

        crud(calendar, 'PUT', '/appointments/' + appointment.id, appointment, revertFunc);

    }// update appointment

    function deleteAppointment(calendar, id, reason = '') {

        crud(calendar, 'DELETE', '/appointments/' + id, { idRemove: id, reason: reason });

    } //delete appointment

    modalForm.on('shown.bs.modal', function () {

        var date = $(this).find('.modal-body').attr('data-date');
        var hour = $(this).find('.modal-body').attr('data-hour');


        var modal = $(this);
        modal.find('.modal-title').html('Crear cita para el  <span class="label bg-yellow">' + date + '</span> a las <span class="label bg-yellow">' + hour + '</span>');

    });

    function createEventFromModal() {

        var currColor = '#374850'; //Red by default
        var val = modalForm.find('#modal-new-event').val();
        var valSelect = modalForm.find('.modal-body').find('.widget-user-2').attr('data-patient');//val();
        var valName = modalForm.find('.modal-body').find('.widget-user-2').attr('data-title');
        var office_id = modalForm.find('.modal-body').find('.widget-user-2').attr('data-office');
        var room_id = modalForm.find('.modal-body').find('.widget-user-2').attr('data-room');
        var optreatment_ids = modalForm.find('.modal-body').find('.widget-user-2').attr('data-optreatments');
        optreatment_ids = optreatment_ids ? optreatment_ids.split(',') : [];
        var medic_id = modalForm.find('.modal-body').attr('data-medic');
        var date = $.fullCalendar.moment(modalForm.find('#modal-new-event').attr('data-modaldate'));
        var end = (modalForm.find('#modal-new-event').attr('data-modaldate-end')) ? $.fullCalendar.moment(modalForm.find('#modal-new-event').attr('data-modaldate-end')) : '';
        var calendar = $('#calendar-m' + medic_id);

        if (valSelect.length == 0 || !office_id) {
            return;
        }


        //Create events

        var eventObject = {
            title: $.trim(val + ' - ' + valName), // use the element's text as the event title
            user_id: medic_id,
            patient_id: valSelect,
            office_id: office_id,
            room_id: room_id,
            optreatment_ids: optreatment_ids
            //created_by: $('input[name=user_id]').val()

        };

        var originalEventObject = eventObject;//event.data('eventObject');

        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);


        // assign it the date that was reported
        copiedEventObject.start = date;

        if (end)
            copiedEventObject.end = end;

        if (date.isValid()) {


            copiedEventObject.allDay = false;//allDay;
            copiedEventObject.backgroundColor = currColor; //event.css("background-color");
            copiedEventObject.borderColor = currColor;//event.css("border-color");
            copiedEventObject.overlap = false;

            calendar.fullCalendar('renderEvent', copiedEventObject); // get _id from event in the calendar (this is for if user will remove the event)


            saveAppointment(calendar, copiedEventObject);
        }
        //Remove event from text input
        modalForm.find('#modal-new-event').val('');
        //$(".modal-search-patients").val("").trigger('change');
        //$(".modal-search-patients").text("").trigger('change');
        modalForm.find('#modal-new-event').attr('data-modaldate', '');
        modalForm.modal('hide');
    } //create from modal function

    // searchPatients.select2({
    //     placeholder: "Buscar paciente",
    //     ajax: {
    //         url: "/medic/patients/list",
    //         dataType: 'json',
    //         delay: 250,
    //         data: function (params) {
    //             return {
    //                 q: params.term // search term

    //             };
    //         },
    //         processResults: function (data) {
    //             searchPatients.empty();
    //             // console.log(data.data);
    //             var items = []

    //             $.each(data.data, function (index, value) {
    //                 item = {
    //                     id: value.id,
    //                     text: value.first_name
    //                 }
    //                 items.push(item);
    //             })


    //             return {
    //                 results: items,

    //             };
    //         }



    //     }
    // });

    $('.btn-finalizar-cita').click(function (e) {
        e.preventDefault();

        createEventFromModal();

    });

    $('body').on('click', '.btn-confirm', function (e) {
        e.preventDefault();
        
        var appointment_id = $(this).attr('data-appointment');

        $.ajax({
            type: 'PUT',
            url: '/agenda/'+ appointment_id +'/confirm',
            data: {},
            success: function () {

                console.log('cita confirmada');

                // var urlFacturacion = '/invoices/create?p={{ $appointment->patient_id }}&appointment={{$appointment->id }}';

                flash('Cita confirmada', 'success');
                window.location.reload();


            },
            error: function () {
                console.log('error confirmado cita');

            }

        });


    });

    $('body').on('click', '.btn-unconfirm', function (e) {
        e.preventDefault();
        
        var appointment_id = $(this).attr('data-appointment');

        $.ajax({
            type: 'PUT',
            url: '/agenda/'+ appointment_id +'/unconfirm',
            data: {},
            success: function () {

                console.log('cita desconfirmada');

                // var urlFacturacion = '/invoices/create?p={{ $appointment->patient_id }}&appointment={{$appointment->id }}';

                flash('Cita desconfirmada', 'success');
                window.location.reload();


            },
            error: function () {
                console.log('error desconfirmando cita');

            }

        });


    });


});