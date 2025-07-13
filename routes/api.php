<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/version-app', function (Request $request) {
    $version = json_decode(file_get_contents(public_path() . "/mobile/version.json"), true);
    return $version;
});
Route::post('/appointment/request/uri', 'Api\AppointmentRequestController@generateUrl')->middleware('auth:api');
Route::post('/appointments/request/{medic}/{user}','Api\AppointmentRequestController@store')->middleware('auth:api');

Route::get('/bootstrap', 'Api\BootstrapController');
Route::get('/configuration', 'Api\ConfigurationController@show')->middleware('auth:api');
Route::post('/broadcasting/auth', 'Api\BroadcastController@authenticate')->middleware('auth:api');
Route::get('/user', 'Api\AuthController@user')->middleware('auth:api');
Route::post('/token', 'Api\AuthController@token');
Route::get('/register/users/{ide}', 'Api\AuthController@getUserByIde');
// Route::post('/user/social/register', 'Api\AuthController@registerSocial');
Route::post('/user/register', 'Api\AuthController@register');
Route::post('/user/{user}/register', 'Api\AuthController@updateRegister');
Route::get('/user/notifications', 'Api\UserNotificationsController@index')->middleware('auth:api');
Route::get('/types-of-health-professional', 'Api\UserNotificationsController@index')->middleware('auth:api');
Route::delete('/users/{user}/notifications/{notification}', 'Api\UserNotificationsController@destroy')->middleware('auth:api');
Route::put('/account', 'Api\UserController@update')->middleware('auth:api');
Route::put('/confignotifications', 'Api\UserController@confignotifications')->middleware('auth:api');
Route::delete('/account/cancel', 'Api\UserController@destroy')->middleware('auth:api');
Route::put('/account/updatepush', 'Api\UserController@updatePushToken')->middleware('auth:api');
Route::post('/account/avatars', 'Api\UserAvatarController@store')->middleware('auth:api');
Route::post('/patient/register', 'Api\UserPatientsController@store')->middleware('auth:api');
Route::get('/users/{user}/patients', 'Api\UserPatientsController@index')->middleware('auth:api');
Route::get('/users/{user}/medic-authorizations', 'Api\UserMedicAuthorizationsController@index')->middleware('auth:api');
Route::delete('/users/{user}/medic-authorizations/{authorization}', 'Api\UserMedicAuthorizationsController@destroy')->middleware('auth:api');
Route::post('/users/{user}/authorization/expedient', 'Api\UserController@authorizationExpedient')->middleware('auth:api');
Route::get('/patients/{patient}', 'Api\UserPatientsController@show')->middleware('auth:api');
Route::get('/patients-ide/{ide}', 'Api\UserPatientsController@getPatientByIde')->middleware('auth:api');
Route::post('/patients/{patient}/request-authorization', 'Api\UserPatientsController@requestAuthorization')->middleware('auth:api');
Route::post('/patients/{patient}/authorization', 'Api\PatientPermissionController@authorization')->middleware('auth:api');
Route::put('/patients/{patient}', 'Api\UserPatientsController@update')->middleware('auth:api');
Route::delete('/patients/{patient}', 'Api\UserPatientsController@destroy')->middleware('auth:api');
Route::get('/patients/{patient}/pressures', 'Api\PressureController@index')->middleware('auth:api');
Route::post('/patients/{patient}/pressures', 'Api\PressureController@store')->middleware('auth:api');
Route::delete('/pressures/{pressure}', 'Api\PressureController@destroy')->middleware('auth:api');
Route::get('/patients/{patient}/sugars', 'Api\SugarController@index')->middleware('auth:api');
Route::post('/patients/{patient}/sugars', 'Api\SugarController@store')->middleware('auth:api');
Route::delete('/sugars/{sugar}', 'Api\SugarController@destroy')->middleware('auth:api');
Route::get('/patients/{patient}/medicines', 'Api\MedicineController@index')->middleware('auth:api');
Route::post('/patients/{patient}/medicines', 'Api\MedicineController@store')->middleware('auth:api');
Route::put('/patients/{patient}/dosereminders/{dosereminder}', 'Api\PatientDoseController@update')->middleware('auth:api');
Route::delete('/medicines/{medicine}', 'Api\MedicineController@destroy')->middleware('auth:api');
Route::get('/patients/{patient}/allergies', 'Api\AllergyController@index')->middleware('auth:api');
Route::post('/patients/{patient}/allergies', 'Api\AllergyController@store')->middleware('auth:api');
Route::delete('/allergies/{allergy}', 'Api\AllergyController@destroy')->middleware('auth:api');
Route::get('/patients/{patient}/lab-results', 'Api\PatientLabresultsController@index')->middleware('auth:api');
Route::post('/patients/{patient}/lab-results', 'Api\PatientLabresultsController@store')->middleware('auth:api');
Route::delete('/patients/{patient}/lab-results/{labresult}', 'Api\PatientLabresultsController@destroy')->middleware('auth:api');
Route::get('/patients/{patient}/history', 'Api\PatientHistoryController@index')->middleware('auth:api');
Route::get('/patients/{patient}/documentations', 'Api\PatientDocumentationController@index')->middleware('auth:api');
Route::get('/patients/{patient}/anthropometry', 'Api\PatientAnthropometryController@index')->middleware('auth:api');
Route::get('/medics', 'Api\MedicController@index');//->middleware('auth:api');
Route::get('/medics/specialities', 'Api\MedicSpecialityController@index');//->middleware('auth:api');
Route::get('/medics/{medic}', 'Api\MedicController@show');//->middleware('auth:api');
Route::get('/medics/{medic}/schedules', 'Api\MedicScheduleController@index')->middleware('auth:api');
Route::get('/medics/{medic}/appointments', 'Api\MedicAppointmentController@index')->middleware('auth:api');
Route::post('/reservations', 'Api\ReservationController@store')->middleware('auth:api');
Route::delete('/appointments/{appointment}', 'Api\ReservationController@destroy')->middleware('auth:api');
Route::get('/appointments', 'Api\AppointmentController@index')->middleware('auth:api');
Route::get('/appointments/{appointment}', 'Api\AppointmentController@show')->middleware('auth:api');
Route::get('/appointments/{appointment}/pdf', 'Api\AppointmentController@pdf')->middleware('auth:api');
Route::post('/appointments/{appointment}/reminder', 'Api\AppointmentReminderController@store')->middleware('auth:api');
// Route::post('/appointments/{appointment}/upload-sinpe', 'Api\AppointmentSinpeController@store')->middleware('auth:api');
Route::get('/clinics', 'Api\ClinicController@index')->middleware('auth:api');
Route::get('/clinics/{clinic}', 'Api\ClinicController@show')->middleware('auth:api');
Route::post('/reviews', 'Api\ReviewController@store')->middleware('auth:api');
Route::post('/user/password/phone', 'Api\AuthController@sendResetCodePhone');
Route::post('/user/password/email', 'Api\AuthController@sendResetCodeEmail');
Route::post('/user/password/ide', 'Api\AuthController@sendResetCodeIde');
Route::post('/user/password/reset', 'Api\AuthController@newPassword');
Route::get('/conditions', 'Api\ConditionsController@index')->middleware('auth:api');
Route::post('/patients/{patientIde}/dosereminders', 'Api\PatientDoseController@store')->middleware('auth:api');
Route::get('/accumulateds/{accumulated}', 'Api\AccumulatedsController@show')->middleware('auth:api');
Route::get('/lab/exams', 'Api\LabExamsController@index');//->middleware('auth:api');
Route::get('/lab/exams-packages', 'Api\LabExamsPackagesController@index');//->middleware('auth:api');
Route::post('/lab/quotes', 'Api\LabQuoteOrdersController@store');
Route::get('/lab/visits', 'Api\LabVisitsController@index');
Route::post('/lab/appointment-requests/register', 'Api\LabVisitsController@registerVisit')->middleware('auth:api');
Route::get('/laboratories', 'Api\LaboratoriesController@index');
Route::get('/laboratories/{office}', 'Api\LaboratoriesController@show');
Route::put('/medicines/reminders/{reminder}', 'Api\MedicineReminderController@update')->middleware('auth:api');

//medics
Route::get('/medics/{medic}/appointments/requests', 'Api\Medic\MedicAppointmentRequestController@index')->middleware('auth:api');
Route::get('/medic/appointments/requests','Api\Medic\AppointmentRequestController@index')->middleware('auth:api');
Route::get('/medic/appointments/requests/{appointmentRequest}','Api\Medic\AppointmentRequestController@show')->middleware('auth:api');
Route::put('/medic/appointments/requests/{appointmentRequest}','Api\Medic\AppointmentRequestController@update')->middleware('auth:api');
Route::post('/medic/register/authorization', 'Api\Medic\AuthController@authorization');
Route::post('/medic/register', 'Api\Medic\AuthController@register');
Route::get('/medic/{user}/patients', 'Api\Medic\UserPatientsController@index')->middleware('auth:api');
Route::put('/medic/patients/{patient}', 'Api\Medic\UserPatientsController@update')->middleware('auth:api');
Route::post('/medic/patients', 'Api\Medic\UserPatientsController@store')->middleware('auth:api');
Route::delete('/medic/patients/{patient}', 'Api\Medic\UserPatientsController@destroy')->middleware('auth:api');
Route::get('/medic/offices', 'Api\Medic\UserOfficesController@index')->middleware('auth:api');
Route::get('/medic/offices/list', 'Api\Medic\OfficeController@index')->middleware('auth:api');
Route::post('/medic/offices', 'Api\Medic\OfficeController@store')->middleware('auth:api');
Route::get('/medic/offices/{office}', 'Api\Medic\OfficeController@show')->middleware('auth:api');
Route::put('/medic/offices/{office}', 'Api\Medic\OfficeController@update')->middleware('auth:api');
Route::delete('/medic/offices/{office}', 'Api\Medic\OfficeController@destroy')->middleware('auth:api');
Route::post('/medic/offices/request', 'Api\Medic\OfficeRequestController@store')->middleware('auth:api');
Route::post('/medic/offices/{office}/assign', 'Api\Medic\OfficeController@assign')->middleware('auth:api');
Route::post('/medic/offices/{office}/activate-appointment-requests-today', 'Api\Medic\OfficeController@activateAppointmentRequestsToday')->middleware('auth:api');
Route::get('/medic/appointments', 'Api\Medic\AppointmentController@index')->middleware('auth:api');
Route::get('/medic/appointments/{appointment}', 'Api\Medic\AppointmentController@show')->middleware('auth:api');
Route::put('/medic/appointments/{appointment}','Api\Medic\AppointmentController@update')->middleware('auth:api');
Route::get('/medic/{medic}/appointments', 'Api\MedicAppointmentController@index')->middleware('auth:api');
Route::post('/medic/schedules', 'Api\Medic\ScheduleController@store')->middleware('auth:api');
Route::post('/medic/schedules/all', 'Api\Medic\ScheduleController@storeAll')->middleware('auth:api');
Route::get('/medic/schedules/{schedule}', 'Api\Medic\ScheduleController@show')->middleware('auth:api');
Route::put('/medic/schedules/{schedule}', 'Api\Medic\ScheduleController@update')->middleware('auth:api');
Route::delete('/medic/schedules/{schedule}', 'Api\Medic\ScheduleController@destroy')->middleware('auth:api');
Route::get('/medic/patients/{patient}/appointments', 'Api\Medic\PatientAppointmentsController@index')->middleware('auth:api');
Route::get('/medic/patients/{patient}/lab-results', 'Api\Medic\PatientLabresultsController@index')->middleware('auth:api');
Route::post('/medic/patients/{patient}/lab-results', 'Api\Medic\PatientLabresultsController@store')->middleware('auth:api');
Route::delete('/medic/patients/{patient}/lab-results/{labresult}', 'Api\Medic\PatientLabresultsController@destroy')->middleware('auth:api');
Route::get('/medic/commission-transactions', 'Api\Medic\CommissionTransactionsController@index')->middleware('auth:api');
Route::get('/medic/commissions', 'Api\Medic\CommissionsController@index')->middleware('auth:api');
Route::get('/medic/commissions/{commission}/transactions', 'Api\Medic\CommissionsController@transactions')->middleware('auth:api');

Route::get('/patients/{patient}/affiliations', 'Api\PatientAffiliationController@index')->middleware('auth:api');
Route::get('/affiliations/{affiliation}/transactions', 'Api\PatientAffiliationController@transactions')->middleware('auth:api');
Route::get('/patients/{patientIde}/invoices', 'Api\PatientInvoiceController@index')->middleware('auth:api');

Route::get('/invoices/{invoice}', 'Api\InvoiceController@show')->middleware('auth:api');
Route::get('/patients/{patient}/historialcompras', 'Api\PatientCompraController@index')->middleware('auth:api');
Route::post('/patients/createaccount', 'Api\UserPatientsController@createOrAssignAccount')->middleware('auth:api');
Route::get('/patients/{patientIde}/get-account', 'Api\UserPatientsController@getAccount')->middleware('auth:api');
Route::post('/patients/{patientIde}/create-discount', 'Api\UserPatientsController@createDiscount')->middleware('auth:api');
Route::get('/users/{user}/discount-history', 'Api\UserDiscountHistoryController@index')->middleware('auth:api');
Route::post('/patients/{patientIde}/generate-accumulated', 'Api\AccumulatedsController@store')->middleware('auth:api');
Route::get('/medic/subscription/invoices', 'Api\Medic\SubscriptionInvoicesController@index')->middleware('auth:api');
Route::post('/medic/subscription/invoices/{subscriptionInvoice}/upload-voucher', 'Api\Medic\SubscriptionInvoiceVouchersController@store')->middleware('auth:api');
Route::get('/medic/subscription/invoices/{subscriptionInvoice}/pdf', 'Api\Medic\SubscriptionInvoicesController@pdf')->middleware('auth:api');
Route::put('/medic/social-profile', 'Api\Medic\SocialProfileController@update')->middleware('auth:api');
Route::get('/medic/social-profile/images', 'Api\Medic\SocialProfileImageController@index')->middleware('auth:api');
Route::post('/medic/social-profile/images', 'Api\Medic\SocialProfileImageController@store')->middleware('auth:api');
Route::post('/medic/social-profile/images/{socialImage}/stories', 'Api\Medic\SocialImageStoryController@store')->middleware('auth:api');
Route::get('/social-stories', 'Api\SocialStoryController@index')->middleware('auth:api');
Route::get('/social-posts', 'Api\SocialPostController@index')->middleware('auth:api');
Route::delete('/medic/{user}/social-posts/{socialImage}', 'Api\MedicSocialPostController@destroy')->middleware('auth:api');
Route::get('/medic/{user}/social-posts', 'Api\MedicSocialPostController@index')->middleware('auth:api');


//Prueba git

// Pharmacies
Route::get('/pharmacies', 'Api\PharmacyController@index');//->middleware('auth:api');

