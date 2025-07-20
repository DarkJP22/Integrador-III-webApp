/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';

import {createApp} from 'vue';
import mitt from 'mitt';
import moment from 'moment';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';
import {provincias} from './ubicaciones.js';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import Swiper from 'swiper';
import {Navigation, Autoplay} from 'swiper/modules';
import 'swiper/css/bundle';

//import VueInputMask from 'vue-inputmask';

//Vue.use(VueInputMask);

window.moment = moment;
window.Swal = Swal;
window.flatpickr = flatpickr;

window.provincias = provincias;


import authorizations from './authorizations.js';

// Vue.prototype.authorize = function (...params) {
//     if (!window.App.signedIn) return false;

//     if (typeof params[0] === 'string') {
//         return authorizations[params[0]](params[1], params[2]);
//     }

//     return params[0](window.App.user);
// };

Number.prototype.format = function (n, x, s, c) {
    const re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

window.roundM = function (num, multiple = 1) {
    return Math.ceil(num / multiple) * multiple;
};

window.redondeo = function (numero, decimales = 2) {
    const flotante = parseFloat(numero);
    return Math.round(flotante * Math.pow(10, decimales)) / Math.pow(10, decimales);

};

//Vue.prototype.signedIn = window.App.signedIn;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Flash from './components/Flash.vue';
import FormError from './components/FormError.vue';
import AvatarForm from './components/AvatarForm.vue';
import Paginator from './components/Paginator.vue';
import Patients from './components/Patients.vue';
import PressureControl from './components/PressureControl.vue';
import SugarControl from './components/SugarControl.vue';
import Medicines from './components/Medicines.vue';
import Allergies from './components/Allergies.vue';
import SummaryAppointment from './components/SummaryAppointment.vue';
import ModalNewAppointment from './components/ModalNewAppointment.vue';
import ModalClinicNewAppointment from './components/ModalClinicNewAppointment.vue';
import ModalClinicNewAppointmentRoom from './components/ModalClinicNewAppointmentRoom.vue';
import Offices from './components/Offices.vue';
import NewOffice from './components/NewOffice.vue';
import Assistants from './components/Assistants.vue';
import NewAssistant from './components/NewAssistant.vue';
import NewPatient from './components/NewPatient.vue';
import History from './components/History.vue';
import TablePendingPayments from './components/TablePendingPayments.vue';
import TableSubscriptions from './components/TableSubscriptions.vue';
import Signs from './components/Signs.vue';
import DiseaseNotes from './components/DiseaseNotes.vue';
import PhysicalExams from './components/PhysicalExams.vue';
import LabExams from './components/LabExams.vue';
import LabResults from './components/LabResults.vue';
import Diagnostics from './components/Diagnostics.vue';
import Treatments from './components/Treatments.vue';
import Instructions from './components/Instructions.vue';
import ModalReservation from './components/ModalReservation.vue';
import ModalReminder from './components/ModalReminder.vue';
import ContactModal from './components/ContactModal.vue';
import UpdateOfficeLocation from './components/UpdateOfficeLocation.vue';
import PaymentDetails from './components/PaymentDetails.vue';
import UserNotifications from './components/UserNotifications.vue';
import InvoiceForm from './components/InvoiceForm.vue';
import ModalStatusHacienda from './components/ModalStatusHacienda.vue';
import ModalStatusMensajeHacienda from './components/ModalStatusMensajeHacienda.vue';
import SendToHacienda from './components/SendToHacienda.vue';
import SendMensajeToHacienda from './components/SendMensajeToHacienda.vue';
import UpdatePharmacyLocation from './components/UpdatePharmacyLocation.vue';
import NewPharmacy from './components/NewPharmacy.vue';
import TestConexionHacienda from './components/TestConexionHacienda.vue';
import PharmacyMedicines from './components/PharmacyMedicines.vue';
import MedicinesAvailables from './components/MedicinesAvailables.vue';
import SendMedicineReminder from './components/SendMedicineReminder.vue';
import ModalOperatorNewAppointment from './components/ModalOperatorNewAppointment.vue';
import ModalPharmacyNewAppointment from './components/ModalPharmacyNewAppointment.vue';
import AddPatient from './components/AddPatient.vue';
import AuthorizationPatientByCode from './components/AuthorizationPatientByCode.vue';
import Discounts from './components/Discounts.vue';
import MensajeReceptor from './components/MensajeReceptor.vue';
import ConfigFactura from './components/ConfigFactura.vue';
import ModalScheduleForm from './components/ModalScheduleForm.vue';
import LabExamsSettings from './components/LabExamsSettings.vue';
//import Loading from './components/Loading.vue';
import PaymentsModal from './components/PaymentsModal.vue';
import ButtonAgendaPrint from './components/ButtonAgendaPrint.vue';

import EmergencyContacts from './components/EmergencyContacts.vue';
import AuthorizationsMedics from './components/AuthorizationsMedics.vue';
import Media from './components/Media.vue';
import MensajeReceptorLote from './components/MensajeReceptorLote.vue';
import AffiliationForm from './components/AffiliationForm.vue';
import ShareLinkApp from './components/ShareLinkApp.vue';
import AddPatientAuthorization from './components/AddPatientAuthorization.vue';
import SelectMediaTags from './components/SelectMediaTags.vue';
import ApiPharmacyCredentials from './components/ApiPharmacyCredentials.vue';
import HistorialComprasPharmacy from './components/HistorialComprasPharmacy.vue';
import PharmacyCredentialsForm from './components/PharmacyCredentialsForm.vue';
import ModalCodeExpedient from './components/ModalCodeExpedient.vue';
import PatientDoseReminder from './components/PatientDoseReminder.vue';
import ModalNewCentroMedico from './components/ModalNewCentroMedico.vue';
import AgregarClinicaPrivada from './components/AgregarClinicaPrivada.vue';
import ModalPacienteGpsMedica from './components/ModalPacienteGpsMedica.vue';
import ProformaForm from './components/ProformaForm.vue';
import TaxView from './pages/Tax.vue';
import PatientDiscounts from './components/PatientDiscounts.vue';
import Evaluation from './components/Evaluation.vue';
import Anthropometry from './components/Anthropometry.vue';
import Treatment from './components/Treatment.vue';
import Documentation from './components/Documentation.vue';
import Recomendations from './components/Recomendations.vue';
import SummaryEsthetic from './components/SummaryEsthetic.vue';
import ModalNewAppointmentEstetica from './components/ModalNewAppointmentEstetica.vue';
import Reason from './components/Reason.vue';
import AgendaTreatments from './components/AgendaTreatments.vue';
import TreatmentsSummary from './components/TreatmentsSummary.vue';
import TreatmentView from './pages/TreatmentView.vue';
import RegisterAuthorizationCodeGenerator from './components/RegisterAuthorizationCodeGenerator.vue';
import SelectMedic from './components/SelectMedic.vue';
import AppointmentRequestForm from './components/AppointmentRequestForm.vue';
import LabAppointmentRequest from './components/LabAppointmentRequest.vue';
import LabAppointmentRequestUpdateDate from './components/LabAppointmentRequestUpdateDate.vue';
import LabAppointmentRequestUpdateVisitLocation from './components/LabAppointmentRequestUpdateVisitLocation.vue';
import LabAppointmentRequestUpdateExams from './components/LabAppointmentRequestUpdateExams.vue';
import LabAppointmentRequestShareLocation from './components/LabAppointmentRequestShareLocation.vue';
import ShareAppLinkWhatsapp from './components/ShareAppLinkWhatsapp.vue';
import ShareAppLink from './components/ShareAppLink.vue';
import ShortPatientForm from './components/ShortPatientForm.vue';
import PatientClinicHistory from './components/PatientClinicHistory.vue';
import NewProduct from './components/NewProduct.vue';
import LabVisits from './components/LabVisits.vue';
import SubscriptionInvoices from './components/SubscriptionInvoices.vue';
import PlanSelection from './components/PlanSelection.vue';
import CurrentSubscription from './components/CurrentSubscription.vue';
import BadgeNotifications from './components/BadgeNotifications.vue';
import OrderBadgeNotifications from './components/OrderBadgeNotifications.vue';
import LabAppointmentRequestForm from './components/LabAppointmentRequestForm.vue';
// eslint-disable-next-line no-unused-vars

const emitter = mitt();
window.emitter = emitter;
const app = createApp({});
app.config.globalProperties.emitter = emitter;
app.config.globalProperties.authorize = (...params) => {
    if (!window.App.signedIn) return false;

    if (typeof params[0] === 'string') {
        return authorizations[params[0]](params[1], params[2]);
    }

    return params[0](window.App.user);
};
app.component('Flash', Flash);
app.component('FormError', FormError);
app.component('AvatarForm', AvatarForm);
app.component('Paginator', Paginator);
app.component('Patients', Patients);
app.component('PressureControl', PressureControl);
app.component('SugarControl', SugarControl);
app.component('Medicines', Medicines);
app.component('Allergies', Allergies);
app.component('SummaryAppointment', SummaryAppointment);
app.component('ModalNewAppointment', ModalNewAppointment);
app.component('ModalClinicNewAppointment', ModalClinicNewAppointment);
app.component('ModalClinicNewAppointmentRoom', ModalClinicNewAppointmentRoom);
app.component('Offices', Offices);
app.component('NewOffice', NewOffice);
app.component('Assistants', Assistants);
app.component('NewAssistant', NewAssistant);
app.component('NewPatient', NewPatient);
app.component('History', History);
app.component('TablePendingPayments', TablePendingPayments);
app.component('TableSubscriptions', TableSubscriptions);
app.component('Signs', Signs);
app.component('DiseaseNotes', DiseaseNotes);
app.component('PhysicalExams', PhysicalExams);
app.component('LabExams', LabExams);
app.component('LabResults', LabResults);
app.component('Diagnostics', Diagnostics);
app.component('Treatments', Treatments);
app.component('Instructions', Instructions);
app.component('ModalReservation', ModalReservation);
app.component('ModalReminder', ModalReminder);
app.component('ContactModal', ContactModal);
app.component('UpdateOfficeLocation', UpdateOfficeLocation);
app.component('PaymentDetails', PaymentDetails);
app.component('UserNotifications', UserNotifications);
app.component('InvoiceForm', InvoiceForm);
app.component('ModalStatusHacienda', ModalStatusHacienda);
app.component('ModalStatusMensajeHacienda', ModalStatusMensajeHacienda);
app.component('SendToHacienda', SendToHacienda);
app.component('SendMensajeToHacienda', SendMensajeToHacienda);
app.component('UpdatePharmacyLocation', UpdatePharmacyLocation);
app.component('NewPharmacy', NewPharmacy);
app.component('TestConexionHacienda', TestConexionHacienda);
app.component('PharmacyMedicines', PharmacyMedicines);
app.component('MedicinesAvailables', MedicinesAvailables);
app.component('SendMedicineReminder', SendMedicineReminder);
app.component('ModalOperatorNewAppointment', ModalOperatorNewAppointment);
app.component('ModalPharmacyNewAppointment', ModalPharmacyNewAppointment);
app.component('AddPatient', AddPatient);
app.component('AuthorizationPatientByCode', AuthorizationPatientByCode);
app.component('Discounts', Discounts);
app.component('MensajeReceptor', MensajeReceptor);
app.component('ConfigFactura', ConfigFactura);
app.component('ModalScheduleForm', ModalScheduleForm);
app.component('PaymentsModal', PaymentsModal);
app.component('ButtonAgendaPrint', ButtonAgendaPrint);
app.component('EmergencyContacts', EmergencyContacts);
app.component('AuthorizationsMedics', AuthorizationsMedics);
app.component('Media', Media);
app.component('MensajeReceptorLote', MensajeReceptorLote);
app.component('AffiliationForm', AffiliationForm);
app.component('ShareLinkApp', ShareLinkApp);
app.component('AddPatientAuthorization', AddPatientAuthorization);
app.component('SelectMediaTags', SelectMediaTags);
app.component('ApiPharmacyCredentials', ApiPharmacyCredentials);
app.component('HistorialComprasPharmacy', HistorialComprasPharmacy);
app.component('PharmacyCredentialsForm', PharmacyCredentialsForm);
app.component('ModalCodeExpedient', ModalCodeExpedient);
app.component('PatientDoseReminder', PatientDoseReminder);
app.component('ModalNewCentroMedico', ModalNewCentroMedico);
app.component('AgregarClinicaPrivada', AgregarClinicaPrivada);
app.component('ModalPacienteGpsMedica', ModalPacienteGpsMedica);
app.component('ProformaForm', ProformaForm);
app.component('TaxView', TaxView);
app.component('PatientDiscounts', PatientDiscounts);
app.component('Evaluation', Evaluation);
app.component('Anthropometry', Anthropometry);
app.component('Treatment', Treatment);
app.component('Documentation', Documentation);
app.component('Recomendations', Recomendations);
app.component('SummaryEsthetic', SummaryEsthetic);
app.component('ModalNewAppointmentEstetica', ModalNewAppointmentEstetica);
app.component('Reason', Reason);
app.component('AgendaTreatments', AgendaTreatments);
app.component('TreatmentsSummary', TreatmentsSummary);
app.component('TreatmentView', TreatmentView);
app.component('RegisterAuthorizationCodeGenerator', RegisterAuthorizationCodeGenerator);
app.component('SelectMedic', SelectMedic);
app.component('AppointmentRequestForm', AppointmentRequestForm);
app.component('LabAppointmentRequest', LabAppointmentRequest);
app.component('LabAppointmentRequestUpdateDate', LabAppointmentRequestUpdateDate);
app.component('LabAppointmentRequestUpdateVisitLocation', LabAppointmentRequestUpdateVisitLocation);
app.component('LabAppointmentRequestUpdateExams', LabAppointmentRequestUpdateExams);
app.component('LabAppointmentRequestShareLocation', LabAppointmentRequestShareLocation);
app.component('ShareAppLinkWhatsapp', ShareAppLinkWhatsapp);
app.component('ShareAppLink', ShareAppLink);
app.component('ShortPatientForm', ShortPatientForm);
app.component('PatientClinicHistory', PatientClinicHistory);
app.component('NewProduct', NewProduct);
app.component('LabVisits', LabVisits);
app.component('SubscriptionInvoices', SubscriptionInvoices);
app.component('PlanSelection', PlanSelection);
app.component('CurrentSubscription', CurrentSubscription);
app.component('LabExamsSettings', LabExamsSettings);
app.component('BadgeNotifications', BadgeNotifications);
app.component('OrderBadgeNotifications', OrderBadgeNotifications);
app.component('LabAppointmentRequestForm', LabAppointmentRequestForm);

app.mount('#app');

window.flash = function (message, level = 'success') {
    window.emitter.emit('flash', {message, level});
};


flatpickr('.flatpickr', {
    wrap: true
});
flatpickr('.flatpickr.time', {
    wrap: true,
    enableTime: true,
    dateFormat: 'Y-m-d H:i',
});

tippy('.tippyTooltip', {
    theme: 'gps',
    content: $('.tippyTooltip').attr('title'),
});

tippy('.tippyTooltipAssistants', {
    theme: 'gps',
    content: $('.tippyTooltipAssistants').attr('title'),
});

tippy('.tippyTooltipFacturacion', {
    theme: 'gps',
    content: $('.tippyTooltipFacturacion').attr('title'),
});
tippy('.tippyTooltipProformas', {
    theme: 'gps',
    content: $('.tippyTooltipProformas').attr('title'),
});

Swiper.use([Navigation, Autoplay]);
new Swiper('.slider-notifications', {
    loop: true,
    centeredSlides: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: true,
    },
    // pagination: {
    //   el: '.swiper-pagination',
    //   clickable: true,
    // },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

});

$.noConflict();
$(function () {
    const sidebarMenu = $('.sidebar-menu');
    sidebarMenu ?? sidebarMenu.tree();
    // const sliderNotifications = $('.slider-notifications');
    // sliderNotifications ?? sliderNotifications.slick({
    //     infinite: false,
    //     autoplay: true,
    //     autoplaySpeed: 5000,
    //     prevArrow: '<span class="fa fa-angle-left"></span>',
    //     nextArrow: '<span class="fa fa-angle-right"></span>'
    // });

    $('body').on('click', '.popup-youtube', function (e) {
        e.preventDefault();

        $(this).magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            iframe: {
                patterns: {
                    youtube: {
                        index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).

                        id: 'v=', // String that splits URL in a two parts, second part should be %id%
                        // Or null - full URL will be returned
                        // Or a function that should return %id%, for example:
                        // id: function(url) { return 'parsed id'; }

                        src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0&showinfo=0' // URL that will be set as a source for iframe.
                    },


                },


            },
            fixedContentPos: false
        }).magnificPopup('open');

    });

    $('form[data-confirm]').on('submit', function () {
        if (!confirm($(this).attr('data-confirm'))) {
            return false;
        }
    });

    window.emitter.on('removeSliderNotifications', (index) => {

        $('.slider-notifications').slick('slickRemove', index, false);

    });
});

