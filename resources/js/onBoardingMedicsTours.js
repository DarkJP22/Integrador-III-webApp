$(function () {
    const inactiveTour = new window.Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            classes: '',
            scrollTo: true
        },
    });
    const CreateAppointmentTour = new window.Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            classes: 'shadow-md bg-purple-dark',
            scrollTo: false
        }
    });

    inactiveTour.addSteps([{
        id: 'inactive-step',
        title: 'Inactivo',
        text: 'Su cuenta está inactiva mientras el administrador verifica sus datos. Siga los siguientes pasos',
        attachTo: {
            element: '.inactive-medic-notification',
            on: 'bottom'
        },
        buttons: [
            {
                text: 'Siguiente',
                action: inactiveTour.next
            }
        ]
    },
    {
        id: 'consultorio-step',
        title: 'Consultorios',
        text: 'Crea tu consultorio para poder programar tu agenda',
        attachTo: {
            element: '.offices-menu',
            on: 'right'
        },
        buttons: [
            {
                text: 'Anterior',
                action: inactiveTour.back
            },
            {
                text: 'Siguiente',
                action: inactiveTour.next
            }
        ]
    },
    {
        id: 'schedule-step',
        title: 'Programación',
        text: 'Programa tu horario de atención para poder recibir citas',
        attachTo: {
            element: '.schedules-menu',
            on: 'right'
        },
        buttons: [
            {
                text: 'Anterior',
                action: inactiveTour.back
            },
            {
                text: 'Listo',
                action: inactiveTour.next
            }
        ]
    }
    ]);
    inactiveTour.on('complete', function () {
        localStorage.setItem('tour_inactive_viewed', 1);
    });
    if (!localStorage.getItem('tour_inactive_viewed') && $('.inactive-medic-notification').length) {
        inactiveTour.start();
    }

    CreateAppointmentTour.addStep({
        id: 'create-appointment-step',
        title: 'Crear Cita',
        text: 'Selecciona una hora en el calendario',
        attachTo: {
            element: '#calendar',
            on: 'top'
        },
        cancelIcon: {
            enabled: true
        },
        buttons: [
            {
                text: 'Listo',
                action: CreateAppointmentTour.next
            }
        ]
    });
    CreateAppointmentTour.on('complete', function () {
        localStorage.setItem('tour_crear_cita_viewed', 1);
    });
    CreateAppointmentTour.on('cancel', function () {
        localStorage.setItem('tour_crear_cita_viewed', 1);
    });
    if (!localStorage.getItem('tour_crear_cita_viewed') && $('#calendar').length) {
        if (inactiveTour.isActive() === true) {
            inactiveTour.on('complete', function () {
                CreateAppointmentTour.start();
            });

        } else {

            CreateAppointmentTour.start();
        }
    }
});