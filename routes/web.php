<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// test
// Route::get('/appointments/request/form/{medic}/{user}','AppointmentRequestController@create')->name('appointmentRequestForm');
// Route::post('/appointments/request/{medic}/{user}','AppointmentRequestController@store')->name('appointmentRequestStore');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/mailable', function () {
    $user = App\User::find(3);

    return (new App\Notifications\WelcomeUser($user))->toMail($user);
});

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'HomeController@index');
Route::post('/support', 'HomeController@support');
Route::delete('/account/cancel', 'AccountController@destroy');
Route::post('/accounts/{account}/switch', 'AccountController@switch');
Route::post('/roles/{role}/switch', 'UserRoleController@switch');
Route::get('/identificaciones/tipos', 'HomeController@tiposIdentificaciones');

Route::resource('discounts', 'DiscountController');

Route::get('/invoices/patients', 'InvoicePatientsController@index');

/** Historial de descuento a pacientes con cuenta en GPS MEdica */
Route::get('/available-patients-for-discounts', 'PatientDiscountsController@index');
Route::get('/available-patients-for-discounts/discounts', 'PatientDiscountsController@discounts');
Route::post('/users/{user}/discounts', 'PatientDiscountsController@store');
/**** */

Route::get('/accumulateds/{accumulated}', 'AccumulatedsController@show');

Route::get('/patients', 'UserPatientsController@index');
Route::get('/patients/create', 'UserPatientsController@create');
Route::post('/patients', 'UserPatientsController@store');
Route::delete('/patients/{patient}', 'UserPatientsController@destroy');
Route::put('/patients/{patient}', 'UserPatientsController@update');
Route::delete('/patients/{patient}/authorizations/{authorization}', 'UserAuthorizationsController@destroy');
Route::get('/authorizations', 'UserAuthorizationsController@index');


Route::get('/patients/{patient}/expedient', 'ExpedientPatientController@show');
Route::get('/patients/{patient}/clinic-history', 'ExpedientPatientController@clinicHistory');
Route::post('/patients/{patient}/pressures', 'PressureController@store');
Route::delete('/pressures/{pressure}', 'PressureController@destroy');
Route::post('/patients/{patient}/sugars', 'SugarController@store');
Route::delete('/sugars/{sugar}', 'SugarController@destroy');
Route::post('/patients/{patient}/medicines', 'MedicineController@store');
Route::get('/patients/{patient}/medicines', 'MedicineController@index');
Route::delete('/medicines/{medicine}', 'MedicineController@destroy');
Route::put('/medicines/{medicine}/receta', 'MedicineController@receta');
Route::post('/patients/{patient}/allergies', 'AllergyController@store');
Route::delete('/allergies/{allergy}', 'AllergyController@destroy');
Route::post('/patients/{patient}/labresults', 'LabResultController@store');
Route::delete('/labresults/{labresult}', 'LabResultController@destroy');
Route::post('/patients/{patient}/labexams', 'LabExamController@store');
Route::get('/patients/{patient}/labexams', 'LabExamController@index');
Route::post('/patients/{patient}/generateauth', 'UserPatientsController@generateAuth');
Route::get('/patients/{patient}/responsables', 'UserPatientsController@responsables');
Route::delete('/appointments/{appointment}/labexams/{labexam}', 'LabExamController@destroy');
Route::post('/patients/{patient}/alerts', 'PatientAlertsController@store');
Route::delete('/labresults/{labresult}', 'LabResultController@destroy');
Route::get('/patients/{patient}/contacts', 'EmergencyContactController@index');
Route::post('/patients/{patient}/contacts', 'EmergencyContactController@store');
Route::put('/emergency-contacts/{emergencyContact}', 'EmergencyContactController@update');
Route::delete('/emergency-contacts/{emergencyContact}', 'EmergencyContactController@destroy');

Route::get('/patients/{patient}/apipharmacredentials', 'ApiPharmaCredentialController@index');
Route::post('/patients/{patient}/apipharmacredentials', 'ApiPharmaCredentialController@store');
Route::put('/apipharmacredentials/{credential}', 'ApiPharmaCredentialController@update');
Route::delete('/apipharmacredentials/{credential}', 'ApiPharmaCredentialController@destroy');

Route::get('/patients/{patient}/dosereminders', 'PatientDoseController@index');
Route::post('/patients/{patient}/dosereminders', 'PatientDoseController@store');
Route::put('/patients/{patient}/dosereminders/{dosereminder}', 'PatientDoseController@update');
//Route::put('/patients/{patient}/dosereminders', 'PatientDoseController@store');

Route::get('/profiles/{user}', 'ProfileController@show');
Route::put('/profiles/{user}', 'ProfileController@update');

Route::get('/medics', 'MedicController@index');
Route::get('/clinics', 'ClinicController@index');

Route::get('/medics/{medic}/appointments', 'MedicController@getAppointments');
Route::get('/medics/{medic}/schedules', 'MedicController@getSchedules');
Route::get('/medics/{medic}/polls', 'PollController@show');
Route::post('/medics/{medic}/polls', 'PollController@store');
Route::post('/polls/send', 'PollController@send');

Route::get('/appointments', 'AppointmentController@index');

Route::get('/medics/{medic}/offices/{office}/reservation', 'MedicController@reservation');
Route::get('/clinics/{office}/reservation', 'ClinicController@reservation');
Route::post('/reservations', 'ReservationController@store');
Route::put('/reservations/{appointment}', 'ReservationController@update');
Route::delete('/reservations/{appointment}', 'ReservationController@destroy');

//Medicos


Route::get('/medic/profiles', 'Medic\ProfileController@show');
Route::put('/medic/profiles/{user}', 'Medic\ProfileController@update');
Route::post('/medic/profiles/{user}/configfactura', 'Medic\ConfigFacturaController@store');
Route::post('/configfactura/{config}', 'ConfigFacturaController@update');
Route::delete('/configfactura/{config}', 'ConfigFacturaController@destroy');
Route::get('/agenda', 'AgendaController@index');
Route::get('/agenda/appointments/{appointment}', 'AgendaController@edit');
Route::put('/agenda/appointments/{appointment}', 'AgendaController@update');
Route::delete('/agenda/appointments/{appointment}', 'AgendaController@destroy');
Route::put('/agenda/appointments/{appointment}/finish', 'AgendaController@finish');
Route::put('/agenda/appointments/{appointment}/revalorizar', 'AgendaController@revalorizar');
Route::put('/agenda/appointments/{appointment}/bill', 'AgendaController@bill');
Route::put('/agenda/appointments/{appointment}/noshows', 'AgendaController@noshows');
Route::get('/agenda/appointments/{appointment}/print', 'AgendaController@print');
Route::get('/agenda/appointments/{appointment}/treatment/print', 'AgendaController@printTreatment');
Route::get('/agenda/appointments/{appointment}/pdf', 'AgendaController@pdf');

Route::get('/agenda/print', 'AgendaController@printAgenda');
Route::get('/agenda/{appointment}/print', 'AgendaController@printAppointment');
Route::put('/agenda/{appointment}/confirm', 'AgendaController@confirm');
Route::put('/agenda/{appointment}/unconfirm', 'AgendaController@unconfirm');

Route::get('/calendar', 'AgendaController@create');
Route::get('/agenda/create', 'AgendaController@create');
Route::get('/schedules/create', 'ScheduleController@create');
Route::post('/schedules', 'ScheduleController@store');
Route::post('/schedules/copy', 'ScheduleController@copy');
Route::put('/schedules/{schedule}', 'ScheduleController@update');
Route::delete('/schedules/{schedule}', 'ScheduleController@destroy');
Route::get('/calendars/offices', 'Calendars\OfficeController@index');
Route::get('/calendars/appointments', 'Calendars\AppointmentController@index');
Route::get('/calendars/schedules', 'Calendars\ScheduleController@index');
Route::get('/calendars/schedules/appointments', 'Calendars\ScheduleController@appointmentsSchedule');
Route::get('/calendars/medics/{medicId}/schedules/appointments', 'Calendars\ScheduleController@appointmentsSchedule');
Route::put('/calendars/schedules/appointments', 'Calendars\ScheduleController@migrationDateAppointments');
Route::put('/calendars/medics/{medicId}/schedules/appointments',
    'Calendars\ScheduleController@migrationDateAppointments');
Route::post('/appointments', 'AppointmentController@store');
Route::put('/appointments/{appointment}', 'AppointmentController@update');
Route::delete('/appointments/{appointment}', 'AppointmentController@destroy');
Route::post('/appointments/{appointment}/reminders', 'AppointmentReminderController@store');
Route::get('/appointments/reminders', 'AppointmentReminderController@show');
Route::get('/offices', 'OfficeController@index');
Route::post('/offices', 'OfficeController@store');
Route::post('/offices/requests', 'OfficeRequestController@store');
Route::put('/offices/{office}/notifications', 'OfficeNotificationController@update');
Route::get('/offices/{office}/rooms', 'OfficeRoomsController@index');
Route::get('/offices/{office}/medics', 'OfficeMedicController@index');
Route::get('/offices/{office}/rooms/{room}/checkavailability', 'OfficeRoomsController@checkAvailability');
Route::post('/offices/{office}', 'OfficeController@update');
Route::post('/offices/{office}/assign', 'OfficeController@assign');
Route::delete('/offices/{office}', 'OfficeController@destroy');
Route::get('/assistants', 'AssistantController@index');
Route::post('/assistants', 'AssistantController@store');
Route::delete('/assistants/{assistant}', 'AssistantController@destroy');
Route::put('/assistants/{user}', 'AssistantController@update');

Route::get('/medic/offices', 'Medic\OfficeController@index');
Route::get('/medic/offices/create', 'Medic\OfficeController@create');
Route::get('/medic/offices/{office}', 'Medic\OfficeController@edit');
Route::get('/medic/offices/{office}/configfactura', 'Medic\ConfigFacturaController@edit');
Route::post('/medic/offices/{office}/configfactura', 'Medic\ConfigFacturaController@store');
Route::put('/configfactura/{config}', 'ConfigFacturaController@update');
Route::delete('/configfactura/{config}', 'ConfigFacturaController@destroy');

Route::get('/medic/assistants', 'Medic\AssistantController@index');
Route::get('/medic/assistants/create', 'Medic\AssistantController@create');
Route::get('/medic/assistants/{assistant}', 'Medic\AssistantController@edit');


Route::get('/plans', 'PlanSubscriptionController@index');
Route::get('/medic/subscriptions/{plan}/buy', 'Medic\SubscriptionController@create');
Route::put('/medic/subscriptions/{plan}/buy', 'Medic\SubscriptionController@changeToFreeSubscription');
Route::get('/medic/subscriptions/{plan}/change', 'Medic\SubscriptionController@edit');
Route::post('/medic/subscriptions/{plan}/change', 'Medic\SubscriptionController@change');
Route::post('/medic/subscriptions/{plan}/change-voucher', 'Medic\SubscriptionController@changeVoucher');
Route::post('/medic/subscriptions/renew-voucher', 'Medic\SubscriptionController@renewVoucher');
Route::put('/medic/subscriptions/{plan}/changefree', 'Medic\SubscriptionController@changeToFreeSubscription');
Route::get('/medic/changeaccounttype', 'Medic\SubscriptionController@index')->name('medicChangeAccountType');
Route::get('/medic/payments/{id}/details', 'Medic\PaymentController@show');
Route::post('/medic/payments/{id}/pay', 'Medic\PaymentController@store');
Route::get('/medic/payments/{id}/create', 'Medic\PaymentController@create');
Route::get('/medic/payments/create', 'Medic\PaymentController@create');
Route::post('/medic/payments/receipt', 'Medic\PaymentController@purchaseResponse');

Route::post('/subscription/invoices/{subscriptionInvoice}/upload-voucher', 'SubscriptionInvoiceVouchersController@store');
Route::get('/subscription/invoices', 'SubscriptionInvoicesController@index');
Route::get('/subscription/invoices/{subscriptionInvoice}/pdf', 'SubscriptionInvoicesController@pdf');


Route::get('/users/{user}', 'UserController@show');
Route::post('/users/{user}/authorization/expedient', 'UserController@authorizationExpedient');
Route::get('/getexpedientcode', 'UserController@getExpedientCode');
Route::put('/users/{user}/settings', 'SettingController@update');

// general patient actions
Route::get('/general/patients', 'GeneralPatientsController@index');
Route::post('/general/patients/{patient}/account', 'GeneralPatientsController@createAccount');
Route::get('/general/patients/{ide}/verifyispatientgpsmedica', 'GeneralPatientsController@verifyIsPatientGpsMedica');
Route::get('/general/patients/create', 'PatientController@create');
Route::post('/general/patients/createaccount', 'GeneralPatientsController@createOrAssignAccount');
Route::post('/general/patients/share-app-link', 'GeneralPatientsController@shareAppLink');
Route::post('/general/patients', 'PatientController@store');
Route::get('/general/patients/{patient}', 'PatientController@edit');
Route::put('/general/patients/{patient}', 'PatientController@update');
Route::delete('/general/patients/{patient}', 'PatientController@destroy');
Route::post('/patients/marketing', 'PatientMarketingController@store');
Route::get('/general/patients/{patient}/historialcompras', 'GeneralPatientsController@historialCompras');
Route::post('/createorupdatepatient', 'PatientController@createorupdatepatient');


/** Beautician */
Route::get('/beautician/users/{user}/available-hours', 'Beautician\AvailableHoursController@index');
Route::get('/beautician/patients', 'Beautician\PatientController@index');
Route::post('/beautician/patients', 'Beautician\PatientController@store');
Route::get('/beautician/patients/{patient}', 'Beautician\PatientController@edit');
Route::get('/beautician/patients/{patient}/appointments', 'Beautician\PatientAgendaController@index');
Route::get('/beautician/patients/{patient}/agenda/treatment-appointments', 'Beautician\PatientAgendaController@edit');
Route::post('/beautician/patients/{patient}/agenda/treatment-appointments', 'Beautician\PatientAgendaController@store');
Route::put('/beautician/patients/{patient}/agenda/treatment-appointments/{appointment}',
    'Beautician\PatientAgendaController@update');
Route::delete('/beautician/patients/{patient}/agenda/treatment-appointments/{appointment}',
    'Beautician\PatientAgendaController@destroy');
Route::get('/beautician/agenda', 'Beautician\AgendaController@index');
Route::get('/beautician/agenda/create', 'Beautician\AgendaController@create');
Route::get('/beautician/schedules/create', 'Beautician\ScheduleController@create');
Route::get('/beautician/agenda/appointments/{appointment}', 'Beautician\AgendaController@edit');
Route::put('/beautician/agenda/appointments/{appointment}', 'Beautician\AgendaController@update');
Route::put('/beautician/agenda/appointments/{appointment}/noshows', 'Beautician\AgendaController@noshows');
Route::delete('/beautician/agenda/appointments/{appointment}', 'Beautician\AgendaController@destroy');
Route::put('/beautician/agenda/appointments/{appointment}/finish', 'Beautician\AgendaController@finish');
Route::get('/beautician/agenda/appointments/{appointment}/print', 'Beautician\AgendaController@print');
Route::get('/beautician/agenda/appointments/{appointment}/pdf', 'Beautician\AgendaController@pdf');
Route::post('/appointments/{appointment}/evaluations', 'Beautician\EvaluationController@store');
Route::put('/appointments/{appointment}/evaluations/{evaluation}', 'Beautician\EvaluationController@update');
Route::post('/appointments/{appointment}/evaluations-notes', 'Beautician\EvaluationNotesController@store');
Route::put('/appointments/{appointment}/evaluations-notes/{note}', 'Beautician\EvaluationNotesController@update');
Route::delete('/appointments/{appointment}/evaluations/{evaluation}', 'Beautician\EvaluationController@destroy');
Route::post('/appointments/{appointment}/anthropometries', 'Beautician\AnthropometryController@store');
Route::put('/appointments/{appointment}/anthropometries/{anthropometry}', 'Beautician\AnthropometryController@update');
Route::post('/appointments/{appointment}/estreatments', 'Beautician\EstreatmentController@store');
Route::post('/appointments/{appointment}/estreatments/proforma', 'Beautician\EstreatmentProformaController@store');
Route::put('/appointments/{appointment}/estreatments/{estreatment}', 'Beautician\EstreatmentController@update');
Route::delete('/appointments/{appointment}/estreatments/{estreatment}', 'Beautician\EstreatmentController@destroy');
Route::post('/appointments/{appointment}/estreatments-notes', 'Beautician\EstreatmentNotesController@store');
Route::put('/appointments/{appointment}/estreatments-notes/{note}', 'Beautician\EstreatmentNotesController@update');
Route::post('/appointments/{appointment}/documentations', 'Beautician\DocumentationController@store');
Route::delete('/documentations/{documentation}', 'Beautician\DocumentationController@destroy');
Route::post('/appointments/{appointment}/recomendations', 'Beautician\RecomendationController@store');
Route::put('/appointments/{appointment}/recomendations/{recomendation}', 'Beautician\RecomendationController@update');
Route::delete('/appointments/{appointment}/recomendations/{recomendation}',
    'Beautician\RecomendationController@destroy');
Route::post('/appointments/{appointment}/recomendations-notes', 'Beautician\RecomendationNotesController@store');
Route::put('/appointments/{appointment}/recomendations-notes/{note}', 'Beautician\RecomendationNotesController@update');

Route::post('/beautician/evaluations/options', 'Beautician\EvaluationController@storeOption');
Route::post('/beautician/estreatments/options', 'Beautician\EstreatmentController@storeOption');
Route::post('/beautician/recomendations/options', 'Beautician\RecomendationController@storeOption');

Route::put('/appointments/{appointment}/treatments', 'Beautician\AppointmentTreatmentController@update');
//Route::delete('/appointments/{appointment}/treatments/{treatment}', 'Beautician\AppointmentTreatmentController@store');
/***** */


Route::get('/medic/patients', 'PatientController@index');
Route::post('/medic/patients', 'Medic\PatientController@store');
Route::post('/patients/{patient}/add', 'PatientPermissionController@store');
Route::post('/patients/{patient}/authorization', 'PatientPermissionController@authorization');
Route::post('/patients/{patient}/addauth', 'PatientPermissionController@addAuthorization');

Route::put('/diseasenotes/{diseasenote}', 'DiseaseNoteController@update');
Route::put('/physicalexams/{physicalexam}', 'PhysicalExamController@update');
Route::post('/diagnostics', 'DiagnosticController@store');
Route::delete('/diagnostics/{diagnostic}', 'DiagnosticController@destroy');
Route::post('/treatments', 'TreatmentController@store');
Route::delete('/treatments/{treatment}', 'TreatmentController@destroy');
// Route::post('allergies', 'Medic\AllergyController');
Route::post('/pathologicals', 'PathologicalController@store');
Route::delete('/pathologicals/{pathological}', 'PathologicalController@destroy');
Route::post('/nopathologicals', 'NopathologicalController@store');
Route::delete('/nopathologicals/{nopathological}', 'NopathologicalController@destroy');
Route::post('/heredos', 'HeredoController@store');
Route::delete('/heredos/{heredo}', 'HeredoController@destroy');
Route::post('/ginecos', 'GinecoController@store');
Route::delete('/ginecos/{gineco}', 'GinecoController@destroy');
Route::put('/signs/{sign}', 'VitalSignController@update');
//Route::post('/notes', 'NoteController@store');

Route::get('/proformas', 'ProformaController@index');
Route::get('/medic/proformas', 'Medic\ProformaController@index');
Route::get('/proformas/create', 'ProformaController@create');
Route::post('/proformas', 'ProformaController@store');
Route::get('/proformas/{proforma}', 'ProformaController@show');
Route::put('/proformas/{proforma}', 'ProformaController@update');
Route::get('/proformas/{proforma}/print', 'ProformaController@print');
Route::get('/proformas/{proforma}/download/pdf', 'ProformaController@pdf');
Route::post('/proformas/{proforma}/email/pdf', 'ProformaController@sendpdf');


Route::get('/invoices', 'InvoiceController@index');
Route::get('/medic/invoices', 'Medic\InvoiceController@index');
Route::get('/medic/receptor/mensajes', 'Medic\ReceptorController@index');
Route::get('/invoices/create', 'InvoiceController@create');
Route::post('/invoices', 'InvoiceController@store');
Route::get('/invoices/{invoice}', 'InvoiceController@show');
Route::put('/invoices/{invoice}', 'InvoiceController@update');
Route::get('/invoices/{invoice}/print', 'InvoiceController@print');
Route::get('/invoices/{invoice}/download/xml', 'InvoiceController@xml');
Route::get('/invoices/{invoice}/download/pdf', 'InvoiceController@pdf');
Route::post('/invoices/{invoice}/email/pdf', 'InvoiceController@sendpdf');
Route::get('/invoices/{invoice}/notacredito', 'NotaCreditoController@create');
Route::post('/invoices/{invoice}/notacredito', 'NotaCreditoController@store');
Route::get('/invoices/{invoice}/notadebito', 'NotaDebitoController@create');
Route::post('/invoices/{invoice}/notadebito', 'NotaDebitoController@store');
Route::put('/invoices/{invoice}/sendhacienda', 'InvoiceController@sendhacienda');
Route::get('/invoices/{invoice}/payments', 'PaymentInvoiceController@index');
Route::post('/invoices/{invoice}/payments', 'PaymentInvoiceController@store');
Route::delete('/payments/{payment}', 'PaymentInvoiceController@destroy');
Route::get('/reports/invoices', 'Medic\ReportsController@invoices');
Route::get('/unbilled', 'Medic\ReportsController@unbilled');
Route::get('/incomes', 'Medic\ReportsController@incomes');
Route::get('/appointments-status', 'Medic\ReportsController@appointments');


Route::get('/receptor/mensajes/create', 'ReceptorController@create');
Route::get('/receptor/mensajes', 'ReceptorController@index');
Route::post('/receptor/mensajes', 'ReceptorController@store');
Route::post('/receptor/mensajes/uploadxml', 'ReceptorController@uploadXml');
Route::put('/receptor/mensajes/{receptor}/sendhacienda', 'ReceptorController@sendhacienda');
Route::delete('/receptor/mensajes/{receptor}', 'ReceptorController@destroy');
Route::get('/hacienda/mensaje/{receptor}/recepcion', 'HaciendaController@recepcionMensaje');
Route::get('/hacienda/mensaje/{receptor}/xml', 'HaciendaController@xmlMensaje'); // mensaje hacienda xml
Route::post('/receptor/mensajes/lote', 'ReceptorController@lote');

Route::get('/hacienda/{invoice}/recepcion', 'HaciendaController@recepcion');
Route::get('/hacienda/{invoice}/xml', 'HaciendaController@xml'); // mensaje hacienda xml
Route::post('/hacienda/response', 'HaciendaController@haciendaResponse')->name('haciendaresponse');
Route::post('/hacienda/mensaje/response',
    'HaciendaController@haciendaMensajeResponse')->name('haciendamensajeresponse');


Route::get('/products', 'ProductController@index');
Route::post('/products', 'ProductController@store');
Route::put('/products/{product}', 'ProductController@update');
Route::delete('/products/{product}', 'ProductController@destroy');
Route::resource('taxes', 'TaxController');


//afiliaciones
Route::get('/affiliationplans', 'AffiliationPlanController@index');
Route::get('/affiliationplans/create', 'AffiliationPlanController@create');
Route::post('/affiliationplans', 'AffiliationPlanController@store');
Route::get('/affiliationplans/{plan}', 'AffiliationPlanController@show');
Route::put('/affiliationplans/{plan}', 'AffiliationPlanController@update');
Route::delete('/affiliationplans/{plan}', 'AffiliationPlanController@destroy');

Route::get('/affiliations', 'AffiliationController@index');
Route::get('/affiliations/create', 'AffiliationController@create');
Route::post('/affiliations', 'AffiliationController@store');
Route::get('/affiliations/{affiliation}', 'AffiliationController@show');
Route::put('/affiliations/{affiliation}', 'AffiliationController@update');
Route::delete('/affiliations/{affiliation}', 'AffiliationController@destroy');
foreach (['active', 'inactive'] as $key) {
    Route::post('/affiliations/{affiliation}/'.$key, [
        'as' => 'affiliations.'.$key,
        'uses' => 'AffiliationController@'.$key,
    ]);
}

Route::get('/affiliations/{affiliation}/payments', 'AffiliationPaymentController@index');
Route::post('/affiliations/{affiliation}/payments', 'AffiliationPaymentController@store');
Route::put('/affiliations/{affiliation}/payments/{payment}', 'AffiliationPaymentController@update');
Route::delete('/affiliations/{affiliation}/payments/{payment}', 'AffiliationPaymentController@destroy');
Route::get('/affiliations/{affiliation}/transactions', 'AffiliationTransactionController@index');
//asistentes
Route::get('/assistant/agenda', 'Assistant\AgendaController@index');
Route::get('/assistant/agenda/rooms', 'Assistant\AgendaRoomController@index');
Route::get('/rooms/{room}/calendars/appointments', 'Calendars\RoomAppointmentController@index');
Route::get('/medics/{medic}/calendars/appointments', 'Calendars\AppointmentController@index');
Route::get('/medics/{medic}/calendars/schedules', 'Calendars\ScheduleController@index');
Route::get('/medics/{medic}/calendars/offices', 'Calendars\OfficeController@index');
Route::get('/assistant/medics/{medic}/schedules/create', 'Assistant\ScheduleController@create');
Route::post('/assistant/medics/{medic}/schedules/', 'Assistant\ScheduleController@store');
Route::post('/assistant/medics/{medic}/schedules/copy', 'Assistant\ScheduleController@copy');
Route::post('/assistant/schedules/copy', 'Assistant\ScheduleController@monthlyCopy');
Route::get('/assistant/schedules/create', 'Assistant\ScheduleController@monthly');
Route::get('/assistant/schedules/monthly', 'Assistant\ScheduleController@monthlySchedules');


Route::get('/medics/{medic}/patients/{patient}/verify', 'GeneralPatientsController@verifyIsPatient');
Route::post('/patients/{patient}/medics/{medic}/authorization', 'PatientPermissionController@authorization');
Route::post('/patients/{patient}/medics/{medic}/addauthorization', 'PatientPermissionController@addAuthorization');
Route::get('/assistant/medics/', 'Assistant\MedicController@index');
Route::post('/medics/{medic}/offices/{office}/verify', 'OfficeMedicController@update');
Route::delete('/offices/{office}/medics/{medic}', 'OfficeMedicController@destroy');
Route::get('/assistant/patients', 'Assistant\PatientController@index');
Route::post('/assistant/patients', 'Assistant\PatientController@store');

Route::get('/assistant/proformas', 'Assistant\ProformaController@index');
Route::get('/assistant/invoices', 'Assistant\InvoiceController@index');
Route::get('/assistant/cxc/{invoice}/print', 'Assistant\CxcController@print');
Route::get('/assistant/cxc', 'Assistant\CxcController@index');
Route::get('/assistant/balance', 'Assistant\ReportsController@balance');
Route::get('/assistant/cxc/payments', 'Assistant\CxcPaymentsController@index');

Route::get('/assistant/profiles', 'Assistant\ProfileController@show');
Route::put('/assistant/profiles/{user}', 'Assistant\ProfileController@update');

Route::get('/assistant/cierres', 'Assistant\CierreController@index');
Route::post('/assistant/cierres', 'Assistant\CierreController@store');
Route::delete('/assistant/cierres/{cierre}', 'Assistant\CierreController@destroy');

Route::get('/assistant/affiliationplans', 'Assistant\AffiliationPlanController@index');
Route::get('/assistant/affiliations', 'Assistant\AffiliationController@index');

//lab
Route::get('/lab/register', 'Auth\RegisterLabController@showRegistrationForm');
Route::post('/lab/register/admin', 'Auth\RegisterLabController@registerAdmin');
Route::get('/lab/complete-payment', 'Auth\RegisterLabController@showCompletePayment');
Route::post('/lab/complete-payment', 'Auth\RegisterLabController@completePayment');
// Route::post('/lab/register', 'Auth\RegisterLabController@register');
Route::get('/lab/office/register', 'Auth\RegisterLabController@showRegistrationOfficeForm');
Route::post('/lab/register/office', 'Auth\RegisterLabController@registerOffice');

Route::get('/lab/profiles', 'Lab\ProfileController@show');
Route::put('/lab/profiles/{user}', 'Lab\ProfileController@update');
Route::post('/lab/profiles/{user}/configfactura', 'Lab\ConfigFacturaController@store');
// Route::get('/lab/agenda', 'Lab\AgendaController@index');
// Route::get('/lab/agenda/rooms', 'Lab\AgendaRoomController@index');
Route::get('/lab/medics/', 'Lab\MedicController@index');
Route::get('/lab/medics/create', 'Lab\MedicController@create');
Route::post('/lab/medics/', 'Lab\MedicController@store');
Route::get('/lab/medics/{medic}', 'Lab\MedicController@edit');
Route::put('/lab/medics/{medic}', 'Lab\MedicController@update');
Route::put('/medics/{medic}/commission/', 'MedicCommissionController@update');
Route::get('/lab/patients', 'Lab\PatientController@index');
Route::post('/lab/patients', 'Lab\PatientController@store');
Route::get('/lab/receptor/mensajes', 'Lab\ReceptorController@index');
Route::get('/lab/proformas', 'Lab\ProformaController@index');
Route::get('/lab/invoices', 'Lab\InvoiceController@index');
Route::get('/lab/cxc/{invoice}/print', 'Lab\CxcController@print');
Route::get('/lab/cxc', 'Lab\CxcController@index');
Route::get('/lab/cxc/payments', 'Lab\CxcPaymentsController@index');
Route::get('/lab/sales', 'Lab\ReportsController@sales');
Route::get('/lab/commission/labs', 'Lab\ReportsCommissionLabsController@commissionLabs');
Route::get('/lab/commission/labs/pending', 'Lab\ReportsCommissionLabsController@commissionLabsPending');
Route::post('/lab/commissions/{commission}/upload-voucher', 'Lab\CommissionVouchersController@store');
Route::get('/lab/cierres', 'Lab\CierreController@index');
Route::post('/lab/cierres', 'Lab\CierreController@store');
Route::delete('/lab/cierres/{cierre}', 'Lab\CierreController@destroy');
Route::get('/lab/appointment-requests/register', 'Lab\AppointmentRequestsController@register');
Route::post('/lab/appointment-requests/register', 'Lab\AppointmentRequestsController@registerStore');
Route::get('/lab/appointment-requests/create', 'Lab\AppointmentRequestsController@create');
Route::get('/lab/appointment-requests', 'Lab\AppointmentRequestsController@index');
Route::post('/lab/appointment-requests', 'Lab\AppointmentRequestsController@store');
Route::post('/lab/appointment-requests/{appointmentRequest}/status',
    'Lab\AppointmentRequestsController@status')->name('appointmentRequest.status');
Route::put('/lab/appointment-requests/{appointmentRequest}/update-appointment-date',
    'Lab\AppointmentRequestsController@updateAppointmentDate')->name('appointmentRequest.updateAppointmentDate');
Route::put('/lab/appointment-requests/{appointmentRequest}/update-visit-location',
    'Lab\AppointmentRequestsController@updateVisitLocation')->name('appointmentRequest.updateVisitLocation');
Route::put('/lab/appointment-requests/{appointmentRequest}/update-exams',
    'Lab\AppointmentRequestsController@updateExams')->name('appointmentRequest.updateExams');
Route::get('/lab/exams-packages', 'Lab\ExamsPackagesController@index');
Route::get('/lab/exams-packages/create', 'Lab\ExamsPackagesController@create');
Route::post('/lab/exams-packages', 'Lab\ExamsPackagesController@store');
Route::get('/lab/exams-packages/{examPackage}', 'Lab\ExamsPackagesController@edit');
Route::put('/lab/exams-packages/{examPackage}', 'Lab\ExamsPackagesController@update');
Route::delete('/lab/exams-packages/{examPackage}', 'Lab\ExamsPackagesController@destroy');
Route::get('/lab/quotes', 'Lab\QuoteOrdersController@index');
Route::post('/lab/quotes', 'Lab\QuoteOrdersController@store');
Route::post('/lab/quotes/{quote}/upload-quote', 'Lab\QuoteOrdersController@upload');
Route::get('/lab/exams', 'Lab\ExamsController@index');
Route::get('/lab/exams/create', 'Lab\ExamsController@create');
Route::get('/lab/exams/{exam}', 'Lab\ExamsController@edit');
Route::get('/lab/visits', 'Lab\VisitsController@index');
Route::post('/lab/visits', 'Lab\VisitsController@store');
Route::put('/lab/visits/{labVisit}', 'Lab\VisitsController@update');
Route::delete('/lab/visits/{labVisit}', 'Lab\VisitsController@destroy');
Route::post('/lab/settings', 'Lab\SettingsController@store');
Route::get('/lab/settings', 'Lab\SettingsController@index');

//administradores clinicas
Route::put('/offices/{office}/medics/{medic}/permissionfe', 'OfficeMedicController@permissionFe');
Route::put('/offices/{office}/medics/{medic}/nopermissionfe', 'OfficeMedicController@noPermissionFe');
Route::get('/offices/{office}/patients/{patient}/verify', 'GeneralPatientsController@verifyIsPatientOfClinic');

Route::get('/clinic/profiles', 'Clinic\ProfileController@show');
Route::put('/clinic/profiles/{user}', 'Clinic\ProfileController@update');
Route::post('/clinic/profiles/{user}/configfactura', 'Clinic\ConfigFacturaController@store');
Route::get('/clinic/agenda', 'Clinic\AgendaController@index');
Route::get('/clinic/agenda/rooms', 'Clinic\AgendaRoomController@index');
Route::get('/clinic/medics/', 'Clinic\MedicController@index');
Route::get('/clinic/medics/create', 'Clinic\MedicController@create');
Route::post('/clinic/medics/', 'Clinic\MedicController@store');
Route::get('/clinic/medics/{medic}', 'Clinic\MedicController@edit');
Route::put('/clinic/medics/{medic}', 'Clinic\MedicController@update');
Route::put('/medics/{medic}/commission/', 'MedicCommissionController@update');
Route::get('/clinic/patients', 'Clinic\PatientController@index');
Route::post('/clinic/patients', 'Clinic\PatientController@store');
Route::get('/clinic/receptor/mensajes', 'Clinic\ReceptorController@index');

Route::get('/clinic/medics/{medic}/schedules/create', 'Clinic\ScheduleController@create');
Route::post('/clinic/medics/{medic}/schedules/', 'Clinic\ScheduleController@store');
Route::post('/clinic/medics/{medic}/schedules/copy', 'Clinic\ScheduleController@copy');
Route::post('/clinic/schedules/copy', 'Clinic\ScheduleController@monthlyCopy');
Route::get('/clinic/schedules/create', 'Clinic\ScheduleController@monthly');
Route::post('/clinic/schedules', 'Clinic\ScheduleController@store');
Route::get('/clinic/schedules/monthly', 'Clinic\ScheduleController@monthlySchedules');

Route::get('/clinic/proformas', 'Clinic\ProformaController@index');
Route::get('/clinic/invoices', 'Clinic\InvoiceController@index');
Route::get('/clinic/cxc/{invoice}/print', 'Clinic\CxcController@print');
Route::get('/clinic/cxc', 'Clinic\CxcController@index');
Route::get('/clinic/balance', 'Clinic\ReportsController@balance');
Route::get('/clinic/commission/appointments', 'Clinic\ReportsController@commissionAppointments');
Route::get('/clinic/commission/labs', 'Clinic\ReportsCommissionLabsController@commissionLabs');
Route::get('/clinic/commission/billed', 'Clinic\ReportsController@commissionBilled');
Route::get('/clinic/sales', 'Clinic\ReportsController@sales');
Route::get('/clinic/cxc/payments', 'Clinic\CxcPaymentsController@index');
Route::post('/clinic/commissions/{commission}/upload-voucher', 'Clinic\CommissionVouchersController@store');

Route::get('/clinic/cierres', 'Clinic\CierreController@index');
Route::post('/clinic/cierres', 'Clinic\CierreController@store');
Route::delete('/clinic/cierres/{cierre}', 'Clinic\CierreController@destroy');

Route::get('/clinic/affiliationplans', 'Clinic\AffiliationPlanController@index');
Route::get('/clinic/affiliations', 'Clinic\AffiliationController@index');
Route::get('/clinic/subscriptions/{plan}/buy', 'Clinic\SubscriptionController@create');
Route::get('/clinic/subscriptions/{plan}/change', 'Clinic\SubscriptionController@edit');
Route::post('/clinic/subscriptions/{plan}/change-voucher', 'Clinic\SubscriptionController@changeVoucher');
Route::put('/clinic/subscriptions/{plan}/changefree', 'Clinic\SubscriptionController@changeToFreeSubscription');
Route::get('/clinic/subscriptions/{incomeId}/renew', 'Clinic\SubscriptionController@renew');
Route::post('/clinic/subscriptions/renew-voucher', 'Clinic\SubscriptionController@renewVoucher');
Route::get('/clinic/changeaccounttype', 'Clinic\SubscriptionController@index')->name('clinicChangeAccountType');

Route::get('/clinic/esthetic/evaluations', 'Clinic\EstheticEvaluationController@index');
Route::get('/clinic/esthetic/evaluations/create', 'Clinic\EstheticEvaluationController@create');
Route::get('/clinic/esthetic/evaluations/{evaluation}', 'Clinic\EstheticEvaluationController@edit');
Route::post('/clinic/esthetic/evaluations', 'Clinic\EstheticEvaluationController@store');
Route::put('/clinic/esthetic/evaluations/{evaluation}', 'Clinic\EstheticEvaluationController@update');
Route::delete('/clinic/esthetic/evaluations/{evaluation}', 'Clinic\EstheticEvaluationController@destroy');

Route::get('/clinic/esthetic/treatments', 'Clinic\EstheticTreatmentController@index');
Route::get('/clinic/esthetic/treatments/create', 'Clinic\EstheticTreatmentController@create');
Route::get('/clinic/esthetic/treatments/{treatment}', 'Clinic\EstheticTreatmentController@edit');
Route::post('/clinic/esthetic/treatments', 'Clinic\EstheticTreatmentController@store');
Route::put('/clinic/esthetic/treatments/{treatment}', 'Clinic\EstheticTreatmentController@update');
Route::delete('/clinic/esthetic/treatments/{treatment}', 'Clinic\EstheticTreatmentController@destroy');

Route::get('/clinic/esthetic/recomendations', 'Clinic\EstheticRecomendationController@index');
Route::get('/clinic/esthetic/recomendations/create', 'Clinic\EstheticRecomendationController@create');
Route::get('/clinic/esthetic/recomendations/{recomendation}', 'Clinic\EstheticRecomendationController@edit');
Route::post('/clinic/esthetic/recomendations', 'Clinic\EstheticRecomendationController@store');
Route::put('/clinic/esthetic/recomendations/{recomendation}', 'Clinic\EstheticRecomendationController@update');
Route::delete('/clinic/esthetic/recomendations/{recomendation}', 'Clinic\EstheticRecomendationController@destroy');

Route::get('/clinic/rooms', 'OfficeRoomsController@index');
Route::get('/clinic/rooms/create', 'OfficeRoomsController@create');
Route::get('/clinic/rooms/{room}', 'OfficeRoomsController@edit');
Route::post('/clinic/rooms/', 'OfficeRoomsController@store');
Route::put('/clinic/rooms/{room}', 'OfficeRoomsController@update');
Route::delete('/clinic/rooms/{room}', 'OfficeRoomsController@destroy');
// Route::get('/clinic/esthetic/packages', 'Clinic\EstheticPackageController@index');
// Route::get('/clinic/esthetic/packages/create', 'Clinic\EstheticPackageController@create');
// Route::get('/clinic/esthetic/packages/{package}', 'Clinic\EstheticPackageController@edit');
// Route::post('/clinic/esthetic/packages', 'Clinic\EstheticPackageController@store');
// Route::put('/clinic/esthetic/packages/{package}', 'Clinic\EstheticPackageController@update');
// Route::delete('/clinic/esthetic/packages/{package}', 'Clinic\EstheticPackageController@destroy');

Route::get('/clinic/esthetic/evaluations', 'Clinic\EstheticEvaluationController@index');
Route::get('/clinic/esthetic/evaluations/create', 'Clinic\EstheticEvaluationController@create');
Route::get('/clinic/esthetic/evaluations/{evaluation}', 'Clinic\EstheticEvaluationController@edit');
Route::post('/clinic/esthetic/evaluations', 'Clinic\EstheticEvaluationController@store');
Route::put('/clinic/esthetic/evaluations/{evaluation}', 'Clinic\EstheticEvaluationController@update');
Route::delete('/clinic/esthetic/evaluations/{evaluation}', 'Clinic\EstheticEvaluationController@destroy');

Route::get('/clinic/esthetic/treatments', 'Clinic\EstheticTreatmentController@index');
Route::get('/clinic/esthetic/treatments/create', 'Clinic\EstheticTreatmentController@create');
Route::get('/clinic/esthetic/treatments/{treatment}', 'Clinic\EstheticTreatmentController@edit');
Route::post('/clinic/esthetic/treatments', 'Clinic\EstheticTreatmentController@store');
Route::put('/clinic/esthetic/treatments/{treatment}', 'Clinic\EstheticTreatmentController@update');
Route::delete('/clinic/esthetic/treatments/{treatment}', 'Clinic\EstheticTreatmentController@destroy');

Route::get('/clinic/esthetic/recomendations', 'Clinic\EstheticRecomendationController@index');
Route::get('/clinic/esthetic/recomendations/create', 'Clinic\EstheticRecomendationController@create');
Route::get('/clinic/esthetic/recomendations/{recomendation}', 'Clinic\EstheticRecomendationController@edit');
Route::post('/clinic/esthetic/recomendations', 'Clinic\EstheticRecomendationController@store');
Route::put('/clinic/esthetic/recomendations/{recomendation}', 'Clinic\EstheticRecomendationController@update');
Route::delete('/clinic/esthetic/recomendations/{recomendation}', 'Clinic\EstheticRecomendationController@destroy');

//farmacias
Route::get('/pharmacy/profiles', 'Pharmacy\ProfileController@show');
Route::put('/pharmacy/profiles/{user}', 'Pharmacy\ProfileController@update');
Route::get('/pharmacy/patients', 'Pharmacy\PatientController@index');
Route::post('/pharmacy/patients', 'Pharmacy\PatientController@store');
Route::get('/pharmacy/patients/{patient}', 'Pharmacy\PatientController@edit');
Route::post('/pharmacy/patients/{patient}/share/link', 'Pharmacy\PatientController@shareApp');
Route::put('/pharmacies/{pharmacy}/notifications', 'PharmacyNotificationController@update');
Route::post('/pharmacies/{pharmacy}', 'PharmacyController@update');
Route::post('/pharmacy/patients/{patient}/pressures', 'Pharmacy\PressureController@store');
Route::delete('/pharmacy/pressures/{pressure}', 'Pharmacy\PressureController@destroy');
Route::post('/pharmacy/patients/{patient}/sugars', 'Pharmacy\SugarController@store');
Route::delete('/pharmacy/sugars/{sugar}', 'Pharmacy\SugarController@destroy');
Route::post('/pharmacy/patients/{patient}/medicines', 'Pharmacy\MedicineController@store');
Route::get('/pharmacy/patients/{patient}/medicines', 'Pharmacy\MedicineController@index');
Route::post('/pharmacy/patients/{patient}/medicines/transfer', 'Pharmacy\MedicineController@transfer');
Route::delete('/pharmacy/medicines/{medicine}', 'Pharmacy\MedicineController@destroy');
Route::put('/pharmacy/medicines/{medicine}', 'Pharmacy\MedicineController@update');
Route::put('/pharmacy/medicines/{medicine}/receta', 'Pharmacy\MedicineController@receta');
Route::get('/pharmacy/medicines/reminders', 'Pharmacy\MedicineReminderController@index');
Route::post('/pharmacy/medicines/reminders/{reminder}/notifications', 'Pharmacy\MedicineReminderController@send');
Route::post('/pharmacy/medicines/reminders/{reminder}/contacted', 'Pharmacy\MedicineReminderController@contacted');
Route::post('/pharmacy/medicines/{medicine}/dosereminders', 'Pharmacy\MedicineDosesController@store');
Route::put('/pharmacy/medicines/{medicine}/dosereminders/{dose}', 'Pharmacy\MedicineDosesController@update');
Route::get('/pharmacy/marketing', 'Pharmacy\MarketingController@index');
Route::get('/pharmacy/subscriptions/{plan}/buy', 'Pharmacy\SubscriptionController@create');
Route::put('/pharmacy/subscriptions/{plan}/buy', 'Pharmacy\SubscriptionController@changeToFreeSubscription');
Route::get('/pharmacy/subscriptions/{plan}/change', 'Pharmacy\SubscriptionController@edit');
Route::post('/pharmacy/subscriptions/{plan}/change-voucher', 'Pharmacy\SubscriptionController@changeVoucher');
Route::put('/pharmacy/subscriptions/{plan}/changefree', 'Pharmacy\SubscriptionController@changeToFreeSubscription');
Route::get('/pharmacy/subscriptions/{incomeId}/renew', 'Pharmacy\SubscriptionController@renew');
Route::post('/pharmacy/subscriptions/renew-voucher', 'Pharmacy\SubscriptionController@renewVoucher');
//Route::get('/pharmacy/changeaccounttype', 'Pharmacy\SubscriptionController@index')->name('pharmacyChangeAccountType');

Route::get('/pharmacies/{pharmacy}/pharmacredentials', 'PharmaCredentialController@show');
Route::post('/pharmacies/{pharmacy}/pharmacredentials', 'PharmaCredentialController@store');
Route::put('/pharmacredentials/{credential}', 'PharmaCredentialController@update');
Route::delete('/pharmacredentials/{credential}', 'PharmaCredentialController@destroy');


Route::get('/pharmacy/agenda', 'Pharmacy\AgendaController@index');
Route::get('/pharmacy/medics/', 'Pharmacy\MedicController@index');
Route::get('/pharmacy/medics/{medic}/offices/{office}/agenda', 'Pharmacy\AgendaController@index');
Route::post('/pharmacy/patients/{patient}/medics/{medic}/add', 'Pharmacy\PatientPermissionController@store');

Route::get('/pharmacy/media', 'Pharmacy\MediaController@index');
Route::post('/pharmacy/media', 'Pharmacy\MediaController@store');
Route::put('/pharmacy/media/{media}', 'Pharmacy\MediaController@update');
Route::delete('/pharmacy/media/{media}', 'Pharmacy\MediaController@destroy');
Route::get('/pharmacy/media/videos', 'Pharmacy\MediaController@media');
Route::post('/patients/media', 'PatientMediaController@store');

//famacias ordenes
use App\Http\Controllers\Pharmacy\Orders_Pharmacy\OrdersController;

Route::prefix('pharmacy')->group(function () {
    Route::resource('orders', OrdersController::class)->names('pharmacy.orders');
});

//admins
Route::get('/admin/register-authorization-codes', 'Admin\UserController@authorizationCodes');
Route::post('/admin/register-authorization-codes', 'Admin\UserController@generateAuthorizationCode');
Route::post('/admin/loginas', 'Admin\LoginAsController@store');
Route::get('/admin/profiles', 'Admin\ProfileController@show');
Route::put('/admin/profiles/{user}', 'Admin\ProfileController@update');
Route::get('/admin/users', 'Admin\UserController@index');
Route::get('/admin/users/create', 'Admin\UserController@create');
Route::post('/admin/users', 'Admin\UserController@store');
Route::get('/admin/users/{user}', 'Admin\UserController@edit');
Route::put('/admin/users/{user}', 'Admin\UserController@update');
Route::delete('/admin/users/{user}', 'Admin\UserController@destroy');
Route::post('/admin/users/{user}/configfactura', 'Admin\ConfigFacturaController@store');
Route::get('/admin/users/{user}/titles', 'Admin\UserController@getTitles');

Route::post('/admin/users/{user}/subscriptions', 'Admin\SubscriptionController@store');
Route::put('/subscriptions/{subscription}', 'Admin\SubscriptionController@update');
Route::post('/subscriptions/{subscription}/billing', 'Admin\SubscriptionController@billing');
Route::delete('/subscriptions/{subscription}', 'Admin\SubscriptionController@destroy');

Route::put('/admin/configuration', 'Admin\ConfigurationController@update');
Route::post('/admin/configuration/xml', 'Admin\ConfigurationXmlController@store');
Route::put('/admin/configuration/accumulated', 'Admin\ConfigurationAccumulatedController@update');
Route::put('/admin/configuration/commission', 'Admin\ConfigurationCommissionController@update');

foreach (['active', 'inactive','commission', 'enableAgenda'] as $key) {
    Route::post('/admin/users/{user}/'.$key, [
        'as' => 'users.'.$key,
        'uses' => 'Admin\UserController@'.$key,
    ]);
}

Route::get('/admin/clinics/requests', 'Admin\OfficeRequestController@index');
Route::delete('/admin/clinics/requests/{id}', 'Admin\OfficeRequestController@destroy');
foreach (['active', 'inactive'] as $key) {
    Route::post('/admin/clinics/requests/{request}/'.$key, [
        'as' => 'requestOffices.'.$key,
        'uses' => 'Admin\OfficeRequestController@'.$key,
    ]);
}
Route::get('/admin/clinics/admins/requests', 'Admin\OfficeAdminRequestController@index');
foreach (['active', 'inactive'] as $key) {
    Route::post('/admin/clinics/admins/requests/{request}/'.$key, [
        'as' => 'requestAdminClinic.'.$key,
        'uses' => 'Admin\UserController@'.$key,
    ]);
}
Route::get('/admin/medics/requests', 'Admin\MedicRequestController@index');
foreach (['active', 'inactive'] as $key) {
    Route::post('/admin/medics/requests/{request}/'.$key, [
        'as' => 'requestMedic.'.$key,
        'uses' => 'Admin\UserController@'.$key,
    ]);
}
Route::put('/admin/users/{user}/changeaccountcentromedico', 'Admin\UserController@changeAccountCentroMedico');
Route::delete('/admin/users/{user}/cancel-account', 'Admin\UserController@cancelAccount');


Route::get('/admin/plans', 'Admin\PlanController@index');
Route::get('/admin/plans/create', 'Admin\PlanController@create');
Route::post('/admin/plans', 'Admin\PlanController@store');
Route::get('/admin/plans/{plan}', 'Admin\PlanController@edit');
Route::put('/admin/plans/{plan}', 'Admin\PlanController@update');
Route::delete('/admin/plans/{plan}', 'Admin\PlanController@destroy');

Route::get('/admin/app/reviews', 'Admin\ReviewAppController@index');
Route::delete('/admin/app/reviews/{id}', 'Admin\ReviewAppController@destroy');

Route::get('/mediatags', 'MediaTagController@index');
Route::get('/mediatags/tags', 'MediaTagController@tags');
Route::get('/mediatags/create', 'MediaTagController@create');
Route::post('/mediatags', 'MediaTagController@store');
Route::get('/mediatags/{tag}', 'MediaTagController@edit');
Route::put('/mediatags/{tag}', 'MediaTagController@update');
Route::delete('/mediatags/{tag}', 'MediaTagController@destroy');

Route::get('/admin/discounts', 'Admin\DiscountController@index');
Route::get('/admin/discounts/create', 'Admin\DiscountController@create');
Route::post('/admin/discounts', 'Admin\DiscountController@store');
Route::get('/admin/discounts/{discount}', 'Admin\DiscountController@edit');
Route::put('/admin/discounts/{discount}', 'Admin\DiscountController@update');
Route::delete('/admin/discounts/{discount}', 'Admin\DiscountController@destroy');

Route::resource('drugs', 'DrugController');
Route::post('drugs/import', 'DrugImportController@store');

//reports
Route::get('/admin/incomes', 'Admin\ReportsController@incomes');
Route::get('/admin/medics', 'Admin\ReportsController@medics');
Route::get('/admin/clinics', 'Admin\ReportsController@clinics');
Route::get('/admin/patients', 'Admin\ReportsController@patients');
Route::get('/admin/appointments', 'Admin\ReportsController@appointments');
Route::get('/admin/annual-performance', 'Admin\ReportsController@annualPerformance');

Route::post('/users/{user}/hacienda/conexion', 'HaciendaController@authToken');
Route::get('/users/{user}/hacienda/comprobantes/{clave}', 'HaciendaController@comprobante');
Route::get('/users/{user}/hacienda/recepcion/{clave}', 'HaciendaController@recepcionClave');
// Route::get('/users/{user}/hacienda/comprobantes', 'HaciendaController@comprobantes');

Route::get('/admin/subscription/invoices', 'Admin\SubscriptionInvoicesController@index');
Route::put('/admin/subscription/invoices/{subscriptionInvoice}', 'Admin\SubscriptionInvoicesController@update');

//operadores

Route::get('/operator/profiles', 'Operator\ProfileController@show');
Route::put('/operator/profiles/{user}', 'Operator\ProfileController@update');
Route::get('/operator/agenda', 'Operator\AgendaController@index');
Route::get('/operator/medics/', 'Operator\MedicController@index');
Route::get('/operator/appointment-requests/', 'Operator\AppointmentRequestController@index');
Route::get('/operator/patients', 'Operator\PatientController@index');
Route::post('/operator/patients', 'Operator\PatientController@store');
Route::get('/operator/medics/{medic}/offices/{office}/agenda', 'Operator\AgendaController@index');
Route::post('/operator/patients/{patient}/medics/{medic}/add', 'Operator\PatientPermissionController@store');

Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store');
Route::post('/api/patients/{patient}/avatar', 'Api\PatientAvatarController@store');
//Auth::routes();

Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy');
Route::delete('/profiles/{user}/{type}/notifications', 'UserNotificationsController@destroyByType');
Route::delete('/profiles/{user}/notifications', 'UserNotificationsController@destroyAll');

// patients
Route::get('/register', 'Auth\RegisterUserController@showRegistrationForm');
Route::post('/register', 'Auth\RegisterUserController@register');

// invitations
Route::get('/invitation/{patient}/register/{code}', 'Auth\RegisterUserInvitationController@showRegistrationForm');
Route::post('/invitation/{patient}/register/{code}', 'Auth\RegisterUserInvitationController@register');

//medics
Route::get('/medic/register', 'Auth\RegisterMedicController@showRegistrationForm');
Route::post('/medic/register/medic', 'Auth\RegisterMedicController@registerMedic');
//Route::post('/medic/register', 'Auth\RegisterMedicController@register');
Route::get('/medic/complete-payment', 'Auth\RegisterMedicController@showCompletePayment');
Route::post('/medic/complete-payment', 'Auth\RegisterMedicController@completePayment');

//clinics
Route::get('/clinic/register', 'Auth\RegisterClinicController@showRegistrationForm');
Route::post('/clinic/register/admin', 'Auth\RegisterClinicController@registerAdmin');
Route::get('/clinic/complete-payment', 'Auth\RegisterClinicController@showCompletePayment');
Route::post('/clinic/complete-payment', 'Auth\RegisterClinicController@completePayment');
// Route::post('/clinic/register', 'Auth\RegisterClinicController@register');
Route::get('/office/register', 'Auth\RegisterClinicController@showRegistrationOfficeForm');
Route::post('/clinic/register/office', 'Auth\RegisterClinicController@registerOffice');

//pharmacies
Route::get('/pharmacy/register', 'Auth\RegisterPharmacyController@showRegistrationForm');
Route::post('/pharmacy/register/admin', 'Auth\RegisterPharmacyController@registerAdmin');
Route::get('/pharmacy/registerform', 'Auth\RegisterPharmacyController@showRegistrationPharmacyForm');
Route::post('/pharmacy/register/pharmacy', 'Auth\RegisterPharmacyController@registerPharmacy');
Route::get('/pharmacy/complete-payment', 'Auth\RegisterPharmacyController@showCompletePayment');
Route::post('/pharmacy/complete-payment', 'Auth\RegisterPharmacyController@completePayment');

//login for patient
Route::get('/user/login', 'Auth\LoginUserController@login');
Route::post('/user/login', 'Auth\LoginUserController@postLogin');
Route::post('/user/logout', 'Auth\LoginUserController@logout');

Route::get('auth/{provider}', 'Auth\AuthController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\AuthController@handleProviderCallback');

Route::get('/user/password/reset', 'Auth\LoginUserController@resetPassword');
Route::post('/user/password/phone', 'Auth\LoginUserController@sendResetCodePhone');
Route::get('/user/password/reset/code', 'Auth\LoginUserController@resetCode');
Route::post('/user/password/reset', 'Auth\LoginUserController@newPassword');

//login for medics, clinics, pharmacies, admin
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');

// Password Reset Routes... for medics, clinics, pharmacies, admin
Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');



Route::get('/download-data/{user}', 'UserDownloadDataController@show')->name('user.download-data.show')->middleware('signed');
Route::post('/download-data/{user}', 'UserDownloadDataController@download')->name('user.download-data.download');

