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
    var freeDays = [];
    var businessHours = [1, 2, 3, 4, 5, 6, 0];
    var calendar = $('#calendar');
    var modalForm = $('#myModal');
    // var searchPatients = $('.modal-search-patients');
    // var currentDate = calendar.attr('data-currentDate');

    // function isOverlapping(event) {

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

    // function dayNumber(date) {
    //     //
    //     return $.fullCalendar.moment(date).day();
    // }

    ini_events($('#external-events div.external-event'));

    function ini_events(ele) {
        ele.each(function () {

            var eventObject = {
                title: $.trim($(this).text()), // use the element's text as the event title
                office_id: $(this).data('office'),
                patient_id: $(this).data('patient'),
                start: moment(),
                end: moment(),
                backgroundColor: $(this).css('background-color'),
                borderColor: $(this).css('border-color')


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

   
    initCalendar([], []);

    function initCalendar(appointments/*, schedules*/) {

        minTime = calendar.attr('data-minTime') ? calendar.attr('data-minTime') : '06:00:00';
        maxTime = calendar.attr('data-maxTime') ? calendar.attr('data-maxTime') : '18:00:00';
        slotDuration = $('#selectSlotDuration').val() ? $('#selectSlotDuration').val() : calendar.attr('data-slotDuration');
        eventDurationNumber = moment.duration(slotDuration).asMinutes();
        eventDurationMinHours = 'minutes';
        freeDays = calendar.attr('data-freeDays') ? JSON.parse(calendar.attr('data-freeDays')) : [];

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
            //defaultDate: currentDate,
            timeFormat: 'h(:mm)a',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            //Random default events
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

                saveAppointment(event, event._id);

            },
            eventResize: function (event, delta, revertFunc/*, jsEvent*/) {
                
                const allowed = (event.created_by == window.App.user.id) || !window.App.isPharmacy;

                if (!allowed) {
                    flash('Accion no permitida!. Solo puedes modificar citas que hayas creado', 'danger');

                    revertFunc();

                    return false;
                }


                updateAppointment(event, revertFunc);


            },
            eventDrop: function (event, delta, revertFunc) {

                var currentDate = new Date();


                if (event.start < currentDate) {

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

                    revertFunc();

                    return false;
                }

                const allowed = (event.created_by == window.App.user.id) || !window.App.isPharmacy;

                if (!allowed)
                {
                    flash('Accion no permitida!. Solo puedes modificar citas que hayas creado', 'danger');

                    revertFunc();

                    return false;
                }

                updateAppointment(event, revertFunc);


            },
            eventRender: function (event, element) {

                if (element.hasClass('fc-nonbusiness')) {

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

                    return false;
                }


                //element.append( "<span class='closeon fa fa-trash'></span>" );
                element.append('<span class=\'appointment-details\' ></span>');

                if (event.rendering == 'background') {
                    element.append('<span class="title-bg-event" data->' + event.title + '</span>');

                }

                if(event.opacity){
                    $(element).css('opacity', event.opacity);
                    (element).css('z-index', 2);
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
                    
                    if ((event.created_by == window.App.user.id) || !window.App.isPharmacy) {
                       
                   
                        element.find('.appointment-details').click(function () {
                            
                            const confirmButton = (event.created_by == window.App.user.id) || !window.App.isPharmacy;
                            let html = '<b>Tel. Paciente:</b> <a href="tel:' + event.patient.phone_number + '">'+ event.patient.phone_number +'</a><br>Fecha: ' + event.start.format('YYYY-MM-DD') + ' De: ' + horaStart + ' a: ' + horaEnd + ' <br>' + officeInfoDisplay;
                         
                            if(!window.App.isPharmacy){
                               
                                html +=' <a href="/agenda/' + event.id + '/print" class="btn btn-primary btn-print" target="_blank">Imprimir</a>';

                                html += event.confirmed ? '<a href="/agenda/' + event.id + '/unconfirm" class="btn btn-secondary btn-unconfirm" target="_blank" data-appointment="' + event.id + '">Desconfirmar</a>' : '<a href="/agenda/' + event.id + '/confirm" class="btn btn-secondary btn-confirm" target="_blank" data-appointment="' + event.id + '">Confirmar</a>';
                            }

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
                                showConfirmButton: confirmButton,
                                confirmButtonColor: '#dd4b39',
                                cancelButtonColor: '#67BC9A',
                                cancelButtonText: 'Ok',
                                confirmButtonText: 'Eliminar cita'
                            }).then((result) => {

                                if (result.value) {
                                    var resp = deleteAppointment(event.id, result.value);

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


                //if ($(jsEvent.target).parent('div').hasClass("fc-bgevent")) { //para prevenir que en eventos de fondo se agregue citas


                modalForm.modal({ backdrop: 'static', show: true });
                modalForm.find('#modal-new-event').attr('data-modaldate', date.format());
                modalForm.find('.modal-body').attr('data-modaldate', date.format());
                modalForm.find('.modal-body').attr('data-room', calendar.attr('data-room'));
                modalForm.find('.modal-body').attr('data-date', date.format('dddd, MMMM Do YYYY')).attr('data-hour', date.format('hh:mm a'));
                window.emitter.emit('openModalClinicNewAppointmentRoom');

                // } else {
                //     flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger')

                //     return false;
                // }






               



            },
            viewRender: function (view) {

                calendar.fullCalendar('removeEventSources');
                
                

                $.ajax({
                    type: 'GET',
                    url: '/rooms/' + calendar.data('room') + '/calendars/appointments?calendar=1&office=' + calendar.data('office') + '&date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {


                        var appointments = [];

                        $.each(resp, function (index, item) {

                            item.allDay = parseInt(item.allDay); // = false;

                            if ((item.patient_id != 0 && item.office_id != calendar.data('office')) || item.patient_id == 0) {
                                item.rendering = 'background';
                            }

                            item.constraint = 'availableForReservation';

                            if (item.confirmed) {

                                item.backgroundColor = '#dd4b39';
                            }
                            
                            if (item.created_by != window.App.user.id && window.App.isPharmacy){
                                item.title = 'Ocupado';
                            }

                            // if (item.user_id != calendar.data('room')) {

                            //     item.backgroundColor = "#FF8F35";
                            //     item.rendering = 'background';
                            //     item.opacity = 0.8;
                            //     item.title = item.room?.name || 'Consultorio';

                            //     if(item.room && item.room?.name != 'Consultorio'){

                            //         appointments.push(item);
                            //     }

                            // }else{
                            appointments.push(item);
                            //}

                          
                         
                        });

                        // initCalendar(appointments,schedules);
                        calendar.fullCalendar('addEventSource', appointments);

                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }
                });

            } //view render

        }); //fullcalendar

    } //init calendar

    function crud(method, url, data, revertFunc) {
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

                    if (data.redirect_appointment) {
                        window.location.href = '/appointments/' + resp.id;
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
    } // CRUD

    function saveAppointment(event) {

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
            office_info: (event.office_info) ? event.office_info : '',
            allDay: 0,
            room_id: event.room_id,
            optreatment_ids: event.optreatment_ids

        };

        // if (isOverlapping(appointment)) {
        //     appointment.allDay = 1;
        // }

        crud('POST', '/appointments', appointment);

    } //save appointment



    function updateAppointment(event, revertFunc) {

        var appointment = {
            //title : event.title,
            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            patient_id: event.patient_id,
            id: event.id,
            allDay: event.allDay ? 1 : 0
        };

        crud('PUT', '/appointments/' + appointment.id, appointment, revertFunc);

    } // update appointment

    function deleteAppointment(id, reason = '') {

        crud('DELETE', '/appointments/' + id, { idRemove: id, reason: reason });

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
        var medic_id = modalForm.find('.modal-body').find('.widget-user-2').attr('data-medic');
        var room_id = calendar.attr('data-room');
        var optreatment_ids = modalForm.find('.modal-body').find('.widget-user-2').attr('data-optreatments');
        optreatment_ids = optreatment_ids ? optreatment_ids.split(',') : [];
        var date = $.fullCalendar.moment(modalForm.find('#modal-new-event').attr('data-modaldate'));
        var end = (modalForm.find('#modal-new-event').attr('data-modaldate-end')) ? $.fullCalendar.moment(modalForm.find('#modal-new-event').attr('data-modaldate-end')) : '';

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


            calendar.fullCalendar('renderEvent', copiedEventObject);


            saveAppointment(copiedEventObject);
        }
        //Remove event from text input
        modalForm.find('#modal-new-event').val('');
        //$(".modal-search-patients").val("").trigger('change');
        //$(".modal-search-patients").text("").trigger('change');
        modalForm.find('#modal-new-event').attr('data-modaldate', '');
        modalForm.modal('hide');
    } //create form modal

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
    // }); //search patient

    $('.btn-finalizar-cita').click(function (e) {
        e.preventDefault();

        createEventFromModal();

    });

    $('body').on('click', '.btn-confirm', function (e) {
        e.preventDefault();

        var appointment_id = $(this).attr('data-appointment');

        $.ajax({
            type: 'PUT',
            url: '/agenda/' + appointment_id + '/confirm',
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
