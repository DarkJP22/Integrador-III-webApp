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
    var miniCalendar = $('#miniCalendar');
    var modalForm = $('#myModal');
    var currColor = '#374850';
    var boxCreateAppointment = $('.box-create-appointment');
    var searchPatients = $('.search-patients');
    // var searchModalPatients = $('.modal-search-patients');
    // var searchOffices = $('.search-offices');


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
        $('.box-offices').hide();
    }

    // function dayNumber(date) {

    //     return $.fullCalendar.moment(date).day();
    // }



    // function isOverlapping(event) {

    //     var array = calendar.fullCalendar('clientEvents');

    //     for (i in array) {
    //         if (event.idRemove != array[i]._id && !array[i].rendering) {
    //             if (event.end > array[i].start._i && event.start < array[i].end._i) {
    //                 return true;
    //             }
    //         }
    //     }
    //     return false;
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

    }


    //var loadedEvents = false;

    initCalendar([]);

    initMiniCalendar([]);

    function initCalendar(appointments) {
        
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
            timeFormat: 'h(:mm)a',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            
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
            
            // drop: function (date, allDay) { // this function is called when something is dropped

            //     var currentDate = new Date();

            //     if (date < currentDate) {

            //         flash('Hora no permitida!.No puedes selecionar horas pasadas o fuera del horario de atención', 'danger')
                   
            //         return false;
            //     }
            //     // retrieve the dropped element's stored Event Object
            //     var originalEventObject = $(this).data('event');

            //     // we need to copy it, so that multiple events don't have a reference to the same object
            //     var copiedEventObject = $.extend({}, originalEventObject);

            //     // assign it the date that was reported
            //     copiedEventObject.start = date;


            //     copiedEventObject.allDay = false;//allDay;
            //     copiedEventObject.backgroundColor = $(this).css("background-color");
            //     copiedEventObject.borderColor = $(this).css("border-color");
            //     copiedEventObject.overlap = false;


            // },
            eventReceive: function (event) {
              
                var currentDate = new Date();
                if (event.start < currentDate) {

                    calendar.fullCalendar('removeEvents', event._id);

                    return false;
                }
               
               
                saveAppointment(event);

            },
            eventResize: function (event, _delta, revertFunc) {

              
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
               
                if (element.hasClass('fc-nonbusiness')) {

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');
                    
                    return false;
                }


                //element.append( "<span class='closeon fa fa-trash'></span>" );
                var office_id = (event.office) ? event.office.id : '';
                var office_name = (event.office) ? event.office.name : '';
                var horaStart = event.start.format('HH:mm');
                var horaEnd = (event.end) ? event.end.format('HH:mm') : '';

                element.append('<span class="appointment-details" data-office="' + office_id + '" data-officename="' + office_name + '"></span>');


                if (event.rendering == 'background') {
                    element.append('<span class="title-bg-event" data-title="' + event.title + '">' + event.title + '</span>');


                }


                element.append('<div data-createdby="' + event.created_by + '"></div>');

                if (event.patient_id && event.patient) {

                    var officeInfoDisplay = '';
                    

                    if (event.office) {
                        
                        var officeInfo = event.office;//JSON.parse(event.office_info);

                        officeInfoDisplay = 'en ' + officeInfo.type + ' ' + officeInfo.name; //+ ' <br>Dirección: ' + officeInfo.address + ', ' + officeInfo.province //+ ', Tel: <a href="tel:' + officeInfo.phone + '">' + officeInfo.phone + '</a><br>'


                    }
                

                    element.find('.appointment-details').click(function () {
                        
                        Swal.fire({
                            title: 'Cita con el Paciente ' + event.patient.first_name,
                            html: '<b>Tel. Paciente:</b> <a href="tel:' + event.patient.phone_number + '">'+ event.patient.phone_number +'</a><br>Fecha: ' + event.start.format('YYYY-MM-DD') + ' De: ' + horaStart + ' a: ' + horaEnd + ' <br>' + officeInfoDisplay,
                            input: 'text',
                            inputPlaceholder:'Motivo eliminar cita',
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Escribe el motivo de la eliminación!';
                                }
                            },
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#67BC9A',
                            cancelButtonText: 'Ok',
                            confirmButtonText: 'Eliminar cita'
                        }).then( (result) => {
                            
                            if (result.value) {
                                const resp = deleteAppointment(event.id, result.value);

                                if (resp) {

                                    Swal.fire(
                                        'Cita cancelada!',
                                        'Tu cita ha sido eliminada del calendario.',
                                        'success',
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


                if ($(jsEvent.target).parent('div').hasClass('fc-bgevent')) { //solo mostrar el modal de crear cita si esta seleecionando una fecha con consultorio


                    modalForm.modal({ backdrop: 'static', show: true });
                    modalForm.find('#modal-new-event').attr('data-modaldate', date.format());
                    modalForm.find('.modal-body').attr('data-modaldate', date.format());
                    modalForm.find('.modal-body').attr('data-date', date.format('dddd, MMMM Do YYYY')).attr('data-hour', date.format('hh:mm a'));
                    modalForm.find('.modal-body').attr('data-office', $(jsEvent.target).data('office'));
                    modalForm.find('.modal-body').attr('data-officename', $(jsEvent.target).data('officename'));

                    window.emitter.emit('openModalNewAppointmentEstetica', $(jsEvent.target).data('office'));

                }else{
                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

                    return false;
                }



                    





            },
            viewRender: function (view) {
                console.log(view.start.format() + ' - ' + view.end.format());
              
                calendar.fullCalendar('removeEventSources');



               
                $.ajax({
                    type: 'GET',
                    dataType:'json',
                    url: '/calendars/appointments?calendar=1&date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {

                        var appointments = [];

                        $.each(resp, function (index, item) {

                            item.allDay = parseInt(item.allDay); // = false;

                            if (item.patient_id == 0) item.rendering = 'background';

                            item.constraint = 'availableForReservation';
                            item.title = (item.patient) ? item.patient.first_name : 'Paciente Desconocido';

                            appointments.push(item);
                        });

                        calendar.fullCalendar('addEventSource', appointments);
                        //calendar.fullCalendar( 'updateEvents', appointments )

                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }

                }); //ajax appoitnments

                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/calendars/schedules?date1=' + view.start.format() + '&date2=' + view.end.format(),
                    data: {},
                    success: function (resp) {

                        var schedulesForAppointmentPage = [];

                        //var bh = [];
                        $.each(resp, function (index, item) {
                               
                            item.allDay = parseInt(item.allDay); // = false;
                            item.id = 'availableForReservation';
                            item.rendering = 'background';
                            item.schedule = 1;
                            schedulesForAppointmentPage.push(item);

                            

                        });

                    
                         
                        calendar.fullCalendar('addEventSource', schedulesForAppointmentPage);


                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }
                }); //ajax schedules



            } //view render

        });

    } // init calendar

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

                var horaStart = event.start.format('HH:mm');
                var horaEnd = (event.end) ? event.end.format('HH:mm') : '';

                //element.append( "<span class='closeon fa fa-trash'></span>" );
                var office_id = (event.office) ? event.office.id : '';
                var office_name = (event.office) ? event.office.name : '';


                var textTooltip = office_name + ' De: ' + horaStart + ' a: ' + horaEnd;



                element.append('<span class="appointment-details tooltip" data-office="' + office_id + '" data-officename="' + office_name + '" data-toggle="tooltip" title="' + textTooltip + '"></span>');


                if (event.rendering == 'background') {
                    element.append('<span class="title-bg-event" data-title="' + event.title + '">' + event.title + '</span>');


                }


                //element.append('<div data-createdby="'+ event.created_by +'"></div>');



                element.find('.appointment-details').click(function () {

                    calendar.fullCalendar('gotoDate', event.date);

                });





            },

            viewRender: function (view) {
                console.log(view.start.format() + ' - ' + view.end.format());

                miniCalendar.fullCalendar('removeEventSources');


                $.ajax({
                    type: 'GET',
                    dataType: 'json',
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
                        
                    },
                    error: function (resp) {
                        console.log('Error - ' + resp);

                    }
                }); //ajax schedules



            } //view render

        });

    } // init mini calendar

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

                    if (data.redirect_appointment){
                      
                        window.location.href = data.redirect_url ? (data.redirect_url + resp.id) : ('/agenda/appointments/' + resp.id) ;
                    }

                 
                }
                if (method == 'DELETE') {
                 
                    calendar.fullCalendar('removeEvents', data.idRemove);

                }

                calendar.fullCalendar('refetchEvents');

            },
            error: function (resp) {
                $('.loader').hide();

                if(revertFunc){
                    revertFunc();
                }
                calendar.fullCalendar('refetchEvents');
                flash(resp.responseJSON.message, 'danger');
              

            }
        });
    }// CRUD

   
    function saveAppointment(event, redirect_appointment, redirect_url) {

        var appointment = {
            title: event.title,
            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            backgroundColor: event.backgroundColor, //Success (green)
            borderColor: event.borderColor,
            office_id: event.office_id,
            patient_id: (event.patient_id) ? event.patient_id : 0,
            office_info: (event.office_info) ? event.office_info : '',
            allDay: 0,
            redirect_appointment: redirect_appointment,
            redirect_url: redirect_url,
            room_id: event.room_id,

        };

        // if (isOverlapping(appointment)) {
        //     appointment.allDay = 1;
        // }

        crud('POST', '/appointments', appointment);

    }



    function updateAppointment(event, revertFunc) {

        var appointment = {

            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            patient_id: event.patient_id,
            id: event.id,
            allDay: event.allDay ? 1 : 0
        };

        crud('PUT', '/appointments/' + appointment.id, appointment, revertFunc);

    }

    function deleteAppointment(id, reason = '') {

        crud('DELETE', '/appointments/' + id, { idRemove: id, reason: reason });

    }

    function createEvent() {
        var val = $('#new-event').val();
        var valSelect = boxCreateAppointment.find('.widget-user-2').attr('data-patient');
        var office_id = boxCreateAppointment.find('.widget-user-2').attr('data-office');
        var valName = boxCreateAppointment.find('.widget-user-2').attr('data-title');

        if (valSelect.length == 0 || !office_id) {
            return;
        }


        //Create events
        var event = $('<div />');
        event.css({ 'background-color': currColor, 'border-color': currColor, 'color': '#fff' }).addClass('external-event');
        event.attr('data-patient', valSelect);
        event.attr('data-office', office_id);

        event.html('');
        event.html(val + ' - ' + valName);
        $('#external-events').prepend(event);

        //Add draggable funtionality
        ini_events(event);

        //Remove event from text input
        $('#new-event').val('');
        searchPatients.val('').trigger('change');
        searchPatients.text('').trigger('change');
    } // create event

    searchPatients.select2({
        placeholder: 'Buscar paciente',
        ajax: {
            url: '/patients',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term

                };
            },
            processResults: function (data) {

                // console.log(data.data);
                searchPatients.empty();
                var items = [];

                $.each(data.data, function (_index, value) {
                    const item = {
                        id: value.id,
                        text: value.first_name
                    };
                    items.push(item);
                });


                return {
                    results: items,

                };
            }



        }
    });
    //searcn patient

    modalForm.on('shown.bs.modal', function () {

        var date = $(this).find('.modal-body').attr('data-date');
        var hour = $(this).find('.modal-body').attr('data-hour');
        var officename = $(this).find('.modal-body').attr('data-officename');


        var modal = $(this);
        modal.find('.modal-title').html('Crear cita para el  <span class="label bg-primary">' + date + '</span> a las <span class="label bg-primary">' + hour + '</span> en <span class="label bg-primary">' + officename + '</span>');

    });

    function createEventFromModal(redirect_appointment, redirect_url) {
        
        var val = modalForm.find('#modal-new-event').val();
        var valSelect = modalForm.find('.modal-body').find('.widget-user-2').attr('data-patient');//val();
        var valName = modalForm.find('.modal-body').find('.widget-user-2').attr('data-title');
        var office_id = modalForm.find('.modal-body').find('.widget-user-2').attr('data-office');
        var room_id = modalForm.find('.modal-body').find('.widget-user-2').attr('data-room');
        var date = $.fullCalendar.moment(modalForm.find('#modal-new-event').attr('data-modaldate'));
        var end = (modalForm.find('#modal-new-event').attr('data-modaldate-end')) ? $.fullCalendar.moment(modalForm.find('#modal-new-event').attr('data-modaldate-end')) : '';
        if (!office_id) {
            office_id = modalForm.find('.modal-body').attr('data-office');
        }
        if (valSelect.length == 0 || !office_id) {
            return;
        }
       

        //Create events

        var eventObject = {
            title: $.trim(val + ' - ' + valName), // use the element's text as the event title
            patient_id: valSelect,
            office_id: office_id,
            room_id: room_id

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
        

            saveAppointment(copiedEventObject, redirect_appointment, redirect_url);
        }
        //Remove event from text input
        modalForm.find('#modal-new-event').val('');
        modalForm.find('#modal-new-event').attr('data-modaldate', '');
        modalForm.modal('hide');

       

    } //create form modal


    // searchModalPatients.select2({
    //     placeholder: "Buscar paciente",
    //     ajax: {
    //         url: "/patients",
    //         dataType: 'json',
    //         delay: 250,
    //         data: function (params) {
    //             return {
    //                 q: params.term // search term

    //             };
    //         },
    //         processResults: function (data) {
    //             searchModalPatients.empty();
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
    // }); //search modal patient


   


    $('#new-event').keypress(function (e) {
        if (e.which == 13) {
            createEvent();
        }
    });

    $('#add-new-event').click(function (e) {
        e.preventDefault();

        createEvent();

    });

    $('.btn-finalizar-cita').click(function (e) {
        e.preventDefault();

        createEventFromModal();

    });

    $('body').on('click', '.btn-iniciar-cita',function (e) {
        e.preventDefault();
        var redirect_appointment = 1;
        var redirect_url = $(this).attr('data-url');
        createEventFromModal(redirect_appointment, redirect_url);




    });







});
