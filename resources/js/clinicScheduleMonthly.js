$(function () {

    const calendar = $('#calendar');
    let from = '';
    let to = '';
    const modalScheduleForm = $('#modalScheduleForm');
    const datetimepicker1 = $('#datetimepicker1');
    const datetimepicker2 = $('#datetimepicker2');
    const datetimepicker3 = $('#datetimepicker3');
    const slotDuration = '00:30';
    const eventDurationNumber = moment.duration(slotDuration).asMinutes();
    const eventDurationMinHours = 'minutes';

    datetimepicker1.datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'es',
        defaultDate: new Date()

    });

    datetimepicker2.datetimepicker({
        format: 'HH:mm',
        stepping: 30,
        //useCurrent: false
    });

    datetimepicker3.datetimepicker({
        format: 'HH:mm',
        stepping: 30,
        useCurrent: false//Important! See issue #1075
    });

    calendar.fullCalendar({
        locale: 'es',
        events: [],
        displayEventTime:true,
        displayEventEnd:true,
        editable: true,
        selectable: true,
        select: function(startDate, endDate/*, jsEvent, view*/) {
            // alert('selected ' + startDate.format() + ' to ' + endDate.format() );
            modalScheduleForm.modal({ backdrop: 'static', show: true });
            window.emitter.emit('createSchedule', { startDate: startDate.format('YYYY-MM-DD'), endDate: endDate.format('YYYY-MM-DD'), office: calendar.data('office') });  
        },
        eventDrop: function (event, delta, revertFunc) {
           
            updateSchedule(event, revertFunc);
            
            const date = moment(event.date);
            const day = date.format('DD');
            const month = date.format('MM');
            const year = date.format('YYYY');
            const startTime = moment(event.start).format('HH:mm:ss');
            const endTime = moment(event.end).format('HH:mm:ss');

            const start = moment(year+'-'+month+'-'+day+'T'+startTime).stripZone().format();
            const end = moment(year+'-'+month+'-'+day+'T'+endTime).stripZone().format();

            axios.get('/calendars/medics/'+ event.user_id +'/schedules/appointments?date1=' + start + '&date2=' + end)
                .then(({ data }) => {

                    //console.log(data);
                    if(data.length){
                        Swal.fire({
                            title: 'Migracion de Citas',
                            html: 'Parece que esta programación de horario ya tenía citas agendadas.. ¿Deseas migrar tambien las citas a la nueva fecha?',
                            showCancelButton: true,
                            confirmButtonColor: '#67BC9A',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'No',
                            confirmButtonText: 'Si'
                        }).then( (result) => {

                            if(result.value){
                            
                                axios.put('/calendars/medics/'+ event.user_id +'/schedules/appointments?date1=' + start + '&date2=' + end+'&dateTo='+ moment(event.start).stripZone().format())
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
        // dayClick: function (date, jsEvent, view) {
        
        //     // modalScheduleForm.modal({ backdrop: 'static', show: true });
        //     // window.emitter.emit('createSchedule', { date: date.format('YYYY-MM-DD'), office: calendar.data('office') })    

        // },
        viewRender: function (view) {
            //console.log(view.start.format() + ' - ' + view.end.format())
            
            from = view.start.format();
            to = view.end.format();

            loadSchedules();
            
         
        }, //view render
        eventRender: function (event, element) {

            element.append('<span class="schedule-details absolute pin z-10 cursor-pointer" data-id="' + event.id + '"></span>');


            element.find('.schedule-details').click(function () {
             
                // window.emitter.emit('editSchedule', { schedule: event });
                var horaStart = event.start.format('HH:mm');
                var horaEnd = (event.end) ? event.end.format('HH:mm') : '';

                Swal.fire({
                    title: 'Horario reservado para Médico ' + event.user.name,
                    html: 'Fecha: ' + event.start.format('YYYY-MM-DD') + ' De: ' + horaStart + ' a: ' + horaEnd,
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#67BC9A',
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
    });

    function loadSchedules() {

        calendar.fullCalendar('removeEventSources');

        axios.get('/clinic/schedules/monthly?office='+ calendar.data('office') +'from=' + from + '&to=' + to)
            .then(({ data }) => {

                const schedules = [];

                $.each(data, function (index, item) {


                    item = makeEvent(item);

                    schedules.push(item);
                });

                calendar.fullCalendar('addEventSource', schedules);

            });
        
    }
   
    function updateSchedule(event/*, revertFunc*/){

        var schedule = {
            date: event.start.format('YYYY-MM-DD'),
            start: event.start.stripZone().format(),
            end: (event.end) ? event.end.stripZone().format() : event.start.add(eventDurationNumber, eventDurationMinHours).stripZone().format(),
            office_id: event.office_id,
            id: event.id,
            office_info: event.office_info,
            allDay: event.allDay ? 1 : 0
        };

        axios.put('/schedules/'+ schedule.id, schedule)
            .then(() => {

                window.emitter.emit('updatedSchedule', event);    

            });
    }

    function deleteSchedule(scheduleId, event){
        axios.delete('/schedules/'+ scheduleId)
            .then(() => {

                window.emitter.emit('deletedSchedule', event);    

            });
    }

    function makeEvent(item) {
 
        item.title = item.user.name;

        return item;
    }
    
    window.emitter.on('createdSchedule', (data) => {
       
        data.forEach(schedule => {
            const item = makeEvent(schedule);
        
            calendar.fullCalendar('renderEvent', item, true);
        });
       
        //calendar.fullCalendar('refetchEvents');
    });

    window.emitter.on('updatedSchedule', () => {

        //const item = makeEvent(data);
        loadSchedules();
        //calendar.fullCalendar('removeEvents', item.id);
        //calendar.fullCalendar('renderEvent', item, true);
        // calendar.fullCalendar('refetchEvents');
    });

    window.emitter.on('deletedSchedule', (data) => {
        
        calendar.fullCalendar('removeEvents', data.id);
      
 
    });



});
