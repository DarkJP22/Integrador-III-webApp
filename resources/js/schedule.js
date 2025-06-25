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
    var setupSchedule = $('#setupSchedule');
    var slotDurationSchedule = setupSchedule.find('#selectSlotDurationModal').val();
    var calendar = $('#calendar');
    // var miniCalendar = $('#miniCalendar');
    // var modalForm = $('#myModal');
    // var currColor = '#3c8dbc';
    // var boxCreateAppointment = $('.box-create-appointment');
    // var searchPatients = $('.search-patients');
    // var searchModalPatients = $('.modal-search-patients');
    var searchOffices = $('.search-offices');
    var datetimepicker1 = $('#datetimepicker1');
    var datetimepicker2 = $('#datetimepicker2');
    var datetimepicker3 = $('#datetimepicker3');
    var datetimepickerini1 = $('#datetimepickerini1');
    var datetimepickerini2 = $('#datetimepickerini2');
    var datetimepickerfin1 = $('#datetimepickerfin1');
    var datetimepickerfin2 = $('#datetimepickerfin2');
    let from = '';
    let to = '';

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

   
    var stepping = moment.duration(slotDurationSchedule).asMinutes(); 

    datetimepicker1.datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'es',
        defaultDate: new Date()

    });

    datetimepicker2.datetimepicker({
        format: 'HH:mm',
        stepping: stepping,
        //useCurrent: false

    });

    datetimepicker3.datetimepicker({
        format: 'HH:mm',
        stepping: stepping,
        useCurrent: false//Important! See issue #1075
    });

    // setupSchedule.modal({ backdrop: 'static', show: true });
   

    $('#selectSlotDuration').on('change', function (e) {
        e.preventDefault();

        onChangeSlotDurarion($(this).val());

    });
    $('#selectSlotDurationModal').on('change', function (e) {
        e.preventDefault();

        onChangeSlotDurarion($(this).val());
        resetTimePicker($(this).val());
    });

    function resetTimePicker(/*slotDuration*/) {
        datetimepicker2.data('DateTimePicker').clear();
        datetimepicker3.data('DateTimePicker').clear();
        datetimepicker2.data('DateTimePicker').destroy();
        datetimepicker3.data('DateTimePicker').destroy();


        // var stepping = (slotDuration.split(':')[1] == "00" ? slotDuration.split(':')[0] : slotDuration.split(':')[1]);

           
        datetimepicker2.datetimepicker({
            format: 'LT',
            stepping: stepping,
            //useCurrent: false
        });
        datetimepicker3.datetimepicker({
            format: 'LT',
            stepping: stepping,
            useCurrent: false

        });


    } //reset datepicker

    function onChangeSlotDurarion(slotDuration) {


        eventDurationNumber = moment.duration(slotDuration).asMinutes();//(slotDuration.split(':')[1] == "00" ? slotDuration.split(':')[0] : slotDuration.split(':')[1]);
        eventDurationMinHours = 'minutes';//(slotDuration.split(':')[1] == "00" ? 'hours' : 'minutes');

        calendar.attr('data-slotduration', slotDuration);
        calendar.fullCalendar('option', 'slotDuration', slotDuration);

        $.ajax({
            type: 'PUT',
            url: '/users/'+ window.App.user.id +'/settings',
            dataType:'json',
            data: { slotDuration: slotDuration },
            success: function () {

                console.log('slotDuration actualizado');
                window.location.href = '/schedules/create';

            },
            error: function () {
                console.log('error updating slotDuration');

            }
        });
    } // onchange slotduration


    datetimepickerini1.datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'es',
        defaultDate: new Date()

    });
    datetimepickerini2.datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'es',
        defaultDate: new Date()

    });
    datetimepickerfin1.datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'es',
        defaultDate: new Date()

    });
    datetimepickerfin2.datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'es',
        defaultDate: new Date()

    });

    function isOverlapping(event) {

        var array = calendar.fullCalendar('clientEvents');

        for (const i in array) {
            if (event.idRemove != array[i]._id && !array[i].rendering) {
                if (event.end > array[i].start._i && event.start < array[i].end._i) {
                    return true;
                }
            }
        }
        return false;
    }



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


    fetch_offices();

    function fetch_offices() {

        $.ajax({
            type: 'GET',
            url: '/calendars/offices',
            dataType: 'json',
            data: {},
            success: function (resp) {

                var offices = [];
                //var colors = ['#00c0ef', '#00a65a', '#f39c12', '#dd4b39', '#A9D300'];
                //var currColor = colors[Math.floor((Math.random()*colors.length))];//"#00a65a";
                
                $.each(resp, function (index, item) {
                    console.log(index);
                    var currColor = '#67BC9A';//colors[index];

                    if (!currColor) currColor = '#00c0ef';

                    offices.push(item);

                    var event = $('<div />');
                    event.css({ 'background-color': currColor, 'border-color': currColor, 'color': '#fff' }).addClass('external-event');
                    event.attr('data-patient', 0);

                    event.html('');
                    event.html(item.name);

                    $('#external-offices').prepend(event);
                  
                    const borderColor = event.css('border-color') ? event.css('border-color') : event.css('background-color');
                    var eventObject = {

                        title: $.trim(item.name), // use the element's text as the event title
                        office_id: item.id, //patient_id :0
                        office_info: JSON.stringify(item),
                        backgroundColor: event.css('background-color'),
                        borderColor: borderColor // fix firefox


                    };

                    // store the Event Object in the DOM element so we can get to it later
                    event.data('event', eventObject);

                    // make the event draggable using jQuery UI
                    event.draggable({
                        zIndex: 1070,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0  //  original position after the drag
                    });



                });

            },
            error: function (resp) {
                console.log('Error - ' + resp);

            }
        });


    } // fetch offices

   
    //var loadedEvents = false;

    initCalendar([]);
  
    function initCalendar(appointments) {

        minTime = calendar.attr('data-minTime') ? calendar.attr('data-minTime') : '06:00:00';
        maxTime = calendar.attr('data-maxTime') ? calendar.attr('data-maxTime') : '18:00:00';
        slotDuration = $('#selectSlotDuration').val() ? $('#selectSlotDuration').val() : calendar.attr('data-slotDuration');
        eventDurationNumber = moment.duration(slotDuration).asMinutes(); //(slotDuration.split(':')[1] == "00" ? slotDuration.split(':')[0] : slotDuration.split(':')[1]);
        eventDurationMinHours = 'minutes'; //(slotDuration.split(':')[1] == "00" ? 'hours' : 'minutes');
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
            //Random default events
            events: appointments,
            forceEventDuration: true,
            slotDuration: slotDuration,
            defaultTimedEventDuration: slotDuration,
            editable: true,
            droppable: true, // this allows things to be dropped onto the calendar !!!
            eventOverlap: false,
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
            
            drop: function (date/*, allDay*/) { // this function is called when something is dropped
                
                var currentDate = new Date();

                if (date < currentDate) {
                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

                    return;
                   
                }
                // retrieve the dropped element's stored Event Object
                var originalEventObject = $(this).data('event');

                // we need to copy it, so that multiple events don't have a reference to the same object
                var copiedEventObject = $.extend({}, originalEventObject);

                // assign it the date that was reported
                copiedEventObject.start = date;

                
                copiedEventObject.allDay = false;//allDay;
                copiedEventObject.backgroundColor = $(this).css('background-color');
                copiedEventObject.borderColor = $(this).css('border-color');
                copiedEventObject.overlap = false;


            },
            eventReceive: function (event) {
                
                var currentDate = new Date();
                if (event.start < currentDate) {

                    calendar.fullCalendar('removeEvents', event._id);

                    return false;
                }

              
                saveSchedule(event);
               
            },
            eventResize: function (event, _delta, revertFunc/*, jsEvent*/) {

               
                updateSchedule(event, revertFunc);
               


            },
            eventDrop: function (event, delta, revertFunc) {
                var currentDate = new Date();
                

                if (event.start < currentDate) {

                    flash('Hora no permitida!. No puedes selecionar horas pasadas o fuera del horario de atención', 'danger');

                    revertFunc();

                    return false;
                }

                updateSchedule(event, revertFunc);

                const date = moment(event.date);
                const day = date.format('DD');
                const month = date.format('MM');
                const year = date.format('YYYY');
                const startTime = moment(event.start).format('HH:mm:ss');
                const endTime = moment(event.end).format('HH:mm:ss');
    
                const start = moment(year+'-'+month+'-'+day+'T'+startTime).stripZone().format();
                const end = moment(year+'-'+month+'-'+day+'T'+endTime).stripZone().format();

                axios.get('/calendars/schedules/appointments?date1=' + start + '&date2=' + end)
                    .then(({ data }) => {

                        //console.log(data);
                        if(data.length){
                            Swal.fire({
                                title: 'Migracion de Citas',
                                html: 'Parece que esta programación de horario ya tenía citas agendadas.. ¿Deseas migrar tambien las citas a la nueva fecha?',
                                showCancelButton: true,
                                confirmButtonColor: '#67BC9A',
                                cancelButtonColor: '#dd4b39',
                                cancelButtonText: 'No',
                                confirmButtonText: 'Si'
                            }).then( (result) => {

                                if(result.value){
                                
                                    axios.put('/calendars/schedules/appointments?date1=' + start + '&date2=' + end+'&dateTo='+ moment(event.start).stripZone().format())
                                        .then(() => {

                                            //loadSchedules()

                                            Swal.fire(
                                                'Citas',
                                                'Citas Migradas correctamente.',
                                                'success'
                                            );
                                        });
                                }
                                

                            });
                        }

                    });
                
               
               
                


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


        
                var officeInfoDisplay = '';
                var titleAlert = event.title;
                var textAlert = 'Fecha: ' + event.start.format('YYYY-MM-DD') + ' De: ' + horaStart + ' a: ' + horaEnd + officeInfoDisplay;

                if (event.office) {
                    var officeInfo = event.office;//JSON.parse(event.office_info);

                    officeInfoDisplay = '<br>Dirección: ' + officeInfo.address + ', ' + officeInfo.province + ' <br>';

                    titleAlert = 'Este horario está reservado para atención en ' + officeInfo.type + ' ' + officeInfo.name;

                    // textAlert = 'Favor llamar a este número: ' + officeInfo.phone + ' <br> Fecha: ' + event.start.format("YYYY-MM-DD") + ' De: ' + horaStart + ' a: ' + horaEnd + officeInfoDisplay
                }


                element.find('.appointment-details').click(function () {
                       
                    Swal.fire({
                        title: titleAlert,
                        html: textAlert,
                        showCancelButton: true,
                        confirmButtonColor: '#67BC9A',
                        cancelButtonColor: '#dd4b39',
                        cancelButtonText: 'Ok',
                        confirmButtonText: 'Eliminar!'
                    }).then( (result) => {

                        if(result.value){
                              
                            deleteSchedule(event.id, event);

                            Swal.fire(
                                'Evento eliminado!',
                                'Tu evento ha sido eliminado del calendario.',
                                'success'
                            );

                        }
                            

                    });




                });




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


                    // return false;
                }


               


                datetimepicker1.data('DateTimePicker').date(date);
                datetimepicker2.data('DateTimePicker').date(date.format('HH:mm '));
                setupSchedule.modal({ backdrop: 'static', show: true });

               



            },
            viewRender: function (view) {
                console.log(view.start.format() + ' - ' + view.end.format());

                from = view.start.format();
                to = view.end.format();

                loadSchedules(view.start.format(), view.end.format());
                

                


            } //view render

        });

    } // init calendar

    function loadSchedules(){
        
        calendar.fullCalendar('removeEventSources');

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/calendars/schedules?date1=' + from + '&date2=' + to,
            data: {},
            success: function (resp) {


                var schedules = [];

                $.each(resp, function (index, item) {

                    item.allDay = parseInt(item.allDay); // = false;

                    /*if(item.patient_id == 0) item.rendering = 'background';*/

                    schedules.push(item);
                });


                calendar.fullCalendar('addEventSource', schedules);
                
            },
            error: function (resp) {
                console.log('Error - ' + resp);

            }
        }); //ajax schedules

    }
    
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

                    //calendar.fullCalendar('removeEvents', data.idRemove)

                    if (resp) {


                        resp.allDay = parseInt(resp.allDay);

                        if (resp.allDay) {
                            
                            deleteSchedule(resp.id);
                            

                        } else {

                            //loadSchedules();
                            calendar.fullCalendar('renderEvent', resp, true);
                            calendar.fullCalendar('refetchEvents');

                        }
                        if (data.redirect_appointment)
                            window.location.href = '/appointments/' + resp.id;


                    } else {
                        flash('No se pudo crear el horario consulta!!', 'danger');

                        return;
                    }

                }
                if (method == 'DELETE') {
                    if (resp) {

                        flash('No se puede eliminar consulta ya que se encuentra iniciada!!', 'danger');
                        

                        return resp;
                    }

                    calendar.fullCalendar('removeEvents', data.idRemove);

                }

                if (method == 'PUT') {
                    if (resp == '') {
                        flash('No se puede cambiar de dia la consulta ya que se encuentra iniciada!!', 'danger');
                    

                        revertFunc();

                        return;
                    }

                    // calendar.fullCalendar('removeEvents', data.id)

                    // resp.allDay = parseInt(resp.allDay);


                    // calendar.fullCalendar('renderEvent', resp, true);

                    //loadSchedules()
                    calendar.fullCalendar('refetchEvents');


                }

            },
            error: function () {
                $('.loader').hide();
                console.log('error saving appointment');

            }
        });
    }// CRUD

    function saveSchedule(event, idRemove) {
        
        var schedule = {
            title: event.title,
            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            backgroundColor: event.backgroundColor, //Success (green)
            borderColor: event.borderColor,
            office_id: (event.office_id) ? event.office_id : 0,
            idRemove: idRemove,
            office_info: (event.office_info) ? event.office_info : '',
            allDay: 0

        };

        if (isOverlapping(schedule)) {
            schedule.allDay = 1;
        }

        crud('POST', '/schedules', schedule);

    }

    function updateSchedule(event, revertFunc) {

        var schedule = {
            //title : event.title,
            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            office_id: event.office_id,
            id: event.id,
            office_info: event.office_info,
            allDay: event.allDay ? 1 : 0
        };

        crud('PUT', '/schedules/' + schedule.id, schedule, revertFunc);

    }

    function deleteSchedule(id) {

        crud('DELETE', '/schedules/' + id, { idRemove: id });

    }


    setupSchedule.find('.add-cita').on('click', function (e) {
        e.preventDefault();

        var title = setupSchedule.find('#search-offices').text();
        var office_id = setupSchedule.find('#search-offices').val();
        var date = setupSchedule.find('input[name="date"]').val();
        var ini = setupSchedule.find('input[name="start"]').val();
        var fin = setupSchedule.find('input[name="end"]').val();

        //var dataSelect = (title) ? setupSchedule.find('#search-offices').select2('data') : '';

        //var office_info = (dataSelect) ? ((dataSelect[0].office_info) ? dataSelect[0].office_info : '') : '';

        var start = date + 'T' + ini + ':00';
        var end = date + 'T' + ((fin) ? fin : ini) + ':00';

        if (!title) {

            setupSchedule.find('#search-offices').select2('focus');
            setupSchedule.find('#search-offices').select2('open');

            flash('Escribe un consultorio o clínica. Por favor revisar!!!', 'danger');
           

            return false;
        }

        if (!date || !ini) {
            flash('Fecha invalida. Por favor revisar!!!', 'danger');
           

            return false;
        }

        if (moment(start).isAfter(end)) {
            flash('Fecha invalida. La hora de inicio no puede ser mayor que la hora final!!!', 'danger');
           
            return false;
        }

        if (moment(start).isSame(end)) {
            end = moment(start).add(eventDurationNumber, eventDurationMinHours).stripZone().format();
        }

        //var colors = ['#2A630F', '#558D00', '#77B000', '#8CCC00', '#A9D300'];
        var currColor = '#67BC9A';//colors[Math.floor((Math.random() * colors.length))];//colors[Math.floor((Math.random() * colors.length))];

        var schedule = {
            title: title,
            date: date,
            start: start,
            end: end,
            backgroundColor: currColor, //Success ('#00a65a')
            borderColor: currColor,
            office_id: office_id,
            allDay: 0

        };

        if (isOverlapping(schedule)) {
            flash('No se puede agregar el evento por que hay colision de horarios. Por favor revisar!!!', 'danger');

          
            return false;
        }

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/schedules',
            data: schedule,
            success: function (resp) {

                resp.allDay = parseInt(resp.allDay);

                calendar.fullCalendar('renderEvent', resp, true);

                flash('Horario Agregado Correctamente!!');
               
                setupSchedule.find('#search-offices').val([]);
                setupSchedule.find('#search-offices').text('');
                datetimepicker1.data('DateTimePicker').clear();
                datetimepicker2.data('DateTimePicker').clear();
                datetimepicker3.data('DateTimePicker').clear();



            },
            error: function () {
                console.log('error saving schedule');

            }
        });


    });

    searchOffices.select2({
        placeholder: 'Buscar Consultorios o Clínicas privadas',

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


                searchOffices.empty();
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



   




});
