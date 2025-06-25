<?php

namespace App\Providers;

use App\Enums\OfficeType;
use App\Enums\SubscriptionInvoicePaidStatus;
use App\Setting;
use Illuminate\Support\ServiceProvider;
use App\User;
use App\Discount;
use Illuminate\Support\Facades\Validator;
use App\Activity;
use App\AffiliationPlan;
use App\MediaTag;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            $user = request()->user();

            $view->with('user_settings', $user?->getAllSettings());
        });


        Paginator::useBootstrap();

        // Email array validator
        Validator::extend('email_array', function ($attribute, $value, $parameters, $validator) {
            $value = str_replace(' ', '', $value);
            $array = explode(',', $value);
            foreach ($array as $email) //loop over values
            {
                $email_to_validate['alert_email'][] = $email;
            }
            $rules = array('alert_email.*' => 'email');
            $messages = array(
                'alert_email.*' => trans('validation.email_array')
            );
            $validator = Validator::make($email_to_validate, $rules, $messages);
            if ($validator->passes()) {
                return true;
            } else {
                return false;
            }
        });

        view()->composer('layouts.medics.app', function ($view) {

            $newHaciendaNotifications = [];

            $notifications = [];//$appointments->get();

            if (auth()->user()->subscriptionInvoices()
                ->where('paid_status', '!=', SubscriptionInvoicePaidStatus::PAID)
                //->where('due_date', '<', today())
                ->count()) {
                $notifications[] = 'subscription';
            }

            if (auth() && !auth()->user()->active) {
                $notifications[] = 'active';
            }

            $offices = auth()->user()->offices()
                ->where('type', OfficeType::MEDIC_OFFICE)
                ->whereNull('lat')->get();

            if ($offices->count()) {
                $notifications[] = 'location';
            }


            $view->with('notifications', $notifications)
                ->with('offices', $offices);
        });

        view()->composer('layouts.laboratories.app', function ($view) {

            $notifications = [];


            $office = auth()?->user()->offices->first();

            if ($office && !$office->lat) {
                $notifications[] = 'location';
            }

            if (auth() && !auth()->user()->active) {
                $notifications[] = 'active';
            }

            if (auth()->user()->subscriptionInvoices()
                ->where('paid_status', '!=', SubscriptionInvoicePaidStatus::PAID)
                //->where('due_date', '<', today())
                ->count()) {
                $notifications[] = 'subscription';
            }

            $view->with('notifications', $notifications)->with('office', $office);
        });

        view()->composer('layouts.clinics.app', function ($view) {

            $notifications = [];

            $office = auth()?->user()->offices->first();

            if ($office && !$office->lat) {
                $notifications[] = 'location';
            }

            if (auth() && !auth()->user()->active) {
                $notifications[] = 'active';
            }

            if (auth()->user()->subscriptionInvoices()
                ->where('paid_status', '!=', SubscriptionInvoicePaidStatus::PAID)
                //->where('due_date', '<', today())
                ->count()) {
                $notifications[] = 'subscription';
            }

            $view->with('notifications', $notifications)->with('office', $office);

        });

        view()->composer('layouts.pharmacies.app', function ($view) {

            $notifications = [];

            if (auth() && !auth()->user()->active) {
                $notifications[] = 'active';
            }

            if (auth()->user()->subscriptionInvoices()
                ->where('paid_status', '!=', SubscriptionInvoicePaidStatus::PAID)
                //->where('due_date', '<', today())
                ->count()) {
                $notifications[] = 'subscription';
            }

            $offices = auth()->user()->pharmacies()
                ->whereNull('lat')->get();

            if ($offices->count()) {
                $notifications[] = 'location';
            }

            $view->with('notifications', $notifications)->with('offices', $offices);

        });

        view()->composer('layouts.admins.app', function ($view) {


            $notifications = [];


            $view->with('notifications', $notifications);
        });

        view()->composer('layouts.operators.app', function ($view) {
            $notifications = [];

            $view->with('notifications', $notifications);
        });


        view()->composer(['agenda.index', 'medic.patients.index', 'medic.patients.edit'], function ($view) {
            $monthlyCharge = auth()->user()->monthlyCharge();

            $view->with(compact('monthlyCharge'));
        });

        view()->composer([
            'invoices._form', 'proformas._form', 'medic.patients.edit', 'clinic.patients.edit', 'lab.patients.edit'
        ], function ($view) {

            $tipoDocumentos = [
                '01' => 'Factura',
                '02' => 'Nota débito',
                '03' => 'Nota crédito',
                '04' => 'Tiquete electrónico',
                /* '05' => 'Nota de despacho',
                '06' => 'Contrato',
                '07' => 'Procedimiento',
                '08' => 'Comprobante emitido en contingencia',
                '99' => 'Otro'*/
            ];
            $tipoDocumentosNotas = [
                '01' => 'Factura',
                '02' => 'Nota débito',
                '03' => 'Nota crédito',
                '04' => 'Tiquete electrónico',
                '05' => 'Nota de despacho',
                '06' => 'Contrato',
                '07' => 'Procedimiento',
                '08' => 'Comprobante emitido en contingencia',
                '09' => 'Devolución mercadería',
                '10' => 'Sustituye factura rechazada por el Ministerio de Hacienda',
                '11' => 'Sustituye factura rechazada por el Receptor del comprobante  
11',
                '12' => 'Sustituye Factura de exportación',
                '13' => '*Facturación mes vencido',
                '99' => 'Otro'
            ];
            $medioPagos = [
                '01' => 'Efectivo',
                '02' => 'Tarjeta',
                '03' => 'Cheque',
                '04' => 'Transferencia – depósito bancario'
            ];
            $condicionVentas = [
                '01' => 'Contado',
                '02' => 'Crédito',
            ];
            $codigoReferencias = [
                '01' => 'Anula Documento de Referencia',
                '02' => 'Corrige texto documento de referencia',
                '03' => 'Corrige monto',
                '04' => 'Referencia a otro documento',
                '05' => 'Sustituye comprobante provisional por contingencia',
                '99' => 'Otro',

            ];

            $tipoIdentificaciones = [
                '01' => 'Cédula Física',
                '02' => 'Cédula Jurídica',
                '03' => 'DIMEX',
                '04' => 'NITE',
                '00' => 'Extranjero'

            ];

            $tipoDocumentosExo = [
                '01' => 'Compras autorizadas',
                '02' => 'Ventas exentas a diplomáticos',
                '03' => 'Autorizado por Ley especial',
                '04' => 'Exenciones Dirección General de Hacienda',
                '05' => 'Transitorio V',
                '06' => 'Transitorio IX',
                '07' => 'Transitorio XVII',
                '99' => 'Otro'
            ];


            $currencies = \App\Currency::all();


            if (auth()->user()->isAssistant()) {
                $office = auth()->user()->clinicsAssistants->first();


            }
            if (auth()->user()->isClinic() || auth()->user()->isLab()) {

                $office = auth()->user()->offices->first();

            }

            if (auth()->user()->isMedic()) {

                $medics[] = auth()->user();

            } else {

                $medicos = $office->users()->whereHas('roles', function ($q) {
                    $q->where('name', 'medico')
                        ->orWhere('name', 'esteticista');
                })->get();

                $adminsClinic = $office->users()->whereHas('roles', function ($q) {
                    $q->Where('name', 'clinica');
                })->get();

                $admins = $adminsClinic->map(function ($item, $key) {
                    $item->name = $item->name." (Administrador)";
                    return $item;
                });

                $medics = $medicos->merge($admins);

            }


            if (auth()->user()->isAssistant()) {
                $clinics = auth()->user()->clinicsAssistants()->with('configFactura.activities')->get();

                $offices = $clinics->filter(function ($item, $key) {
                    return !$item->centroMedicoPendingCharge();
                });


            } else {

                $clinics = auth()->user()->offices()->with('configFactura.activities')->get();

                if (auth()->user()->isMedic()) {
                    $idsClinicasPrivadaSinPermisoFE = auth()->user()->offices()
                        ->where('type', OfficeType::CLINIC)
                        ->where('permission_fe', 0)
                        ->get()
                        ->pluck('id');

                    $offices = $clinics->whereNotIn('id', $idsClinicasPrivadaSinPermisoFE);

                } else {

                    $offices = $clinics->filter(function ($item, $key) {
                        return !$item->centroMedicoPendingCharge();
                    });

                }


            }

            $activities = [];

            $obligadoTributario = auth()->user()->getObligadoTributario();

            if ($obligadoTributario) {
                $activities = Activity::whereIn('codigo', $obligadoTributario->activities->pluck('codigo'))->get();

            }

            $endpointsPatients = [
                'medic' => '/medic/patients',
                'clinic' => '/clinic/patients',
                'assistant' => '/assistant/patients',
                'pharmacy' => '/pharmacy/patients',
                'lab' => '/lab/patients',
            ];
            $endpoint = $endpointsPatients[auth()->user()->userRole()];

            $view->with(compact('tipoDocumentos', 'medioPagos', 'condicionVentas', 'tipoDocumentosNotas',
                'codigoReferencias', 'tipoIdentificaciones', 'currencies', 'medics', 'tipoDocumentosExo', 'offices',
                'activities', 'endpoint'));
        });

        view()->composer(['receptors._form'], function ($view) {

            $MensajesReceptor = [
                '1' => 'Aceptado',
                '2' => 'Aceptado Parcialmente',
                '3' => 'Rechazado',

            ];
            $CondicionImpuesto = [
                '01' => 'Genera crédito IVA',
                '02' => 'Genera Crédito parcial del IVA',
                '03' => 'Bienes de Capital',
                '04' => 'Gasto corriente no genera crédito',
                '05' => 'Proporcionalidad',


            ];


            $view->with(compact('MensajesReceptor', 'CondicionImpuesto'));

        });

        view()->composer([
            'medic._facturaElectronica', 'clinic._facturaElectronica', 'lab._facturaElectronica',
            'admin.users._facturaElectronica', 'medic.offices.editConfigFactura'
        ], function ($view) {
            $tipoIdentificaciones = [
                '01' => 'Cédula Física',
                '02' => 'Cédula Jurídica',
                '03' => 'DIMEX',
                '04' => 'NITE'

            ];
            $activities = Activity::all();

            $view->with(compact('tipoIdentificaciones', 'activities'));
        });

        view()->composer(['patients._form'], function ($view) {
            $userIds = [];

            if (auth()->user()->isAssistant()) {
                $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->user()->id)->first();

                if ($assistantUser) {
                    $user = User::find($assistantUser->user_id);

                    $userIds[] = $user->id;
                }


            } elseif (auth()->user()->isMedic()) {

                $user = auth()->user();
                $userIds[] = $user->id;


            } else {

                $user = auth()->user();
                $userIds[] = $user->id;
            }

            $discounts = Discount::whereIn('user_id', $userIds)->get();

            $view->with(compact('discounts'));
        });

        view()->composer(['affiliations._form'], function ($view) {


            $tipoPlan = AffiliationPlan::all();

            $medioPagos = [
                '01' => 'Efectivo',
                '02' => 'Tarjeta',
                '03' => 'Cheque',
                '04' => 'Transferencia – depósito bancario'
            ];

            $view->with(compact('tipoPlan', 'medioPagos'));
        });

        view()->composer(['patients._form'], function ($view) {


            $tags = MediaTag::pluck('name')->all();

            $view->with(compact('tags'));
        });

        view()->composer(['invoices.index', 'proformas.index', 'medic.reports.invoices'], function ($view) {


            $offices = auth()->user()->clinicsWithPermissionFe();


            $view->with(compact('offices'));

        });
        view()->composer([
            'clinic.reports.sales', 'clinic.reports.commissionBilled', 'clinic.cierres.index', 'clinic.reports.balance',
            'assistant.reports.balance', 'assistant.cierres.index', 'clinic.cxc.index', 'lab.cxc.index',
            'assistant.cxc.index', 'clinic.invoices.index', 'lab.invoices.index', 'assistant.invoices.index',
            'clinic.payments.index', 'assistant.payments.index', 'lab.reports.sales', 'lab.payments.index',
            'lab.cierres.index'
        ], function ($view) {

            $obligadoTributario = auth()->user()->getObligadoTributario();

            $activities = collect([]);

            if ($obligadoTributario) {
                $activities = Activity::whereIn('codigo', $obligadoTributario->activities->pluck('codigo'))->get();

            }


            $view->with(compact('activities'));

        });

        view()->composer('admin.taxes._form', function ($view) {

            $codesTaxes = [
                ['code' => '01', 'name' => 'Impuesto al Valor Agregado'],
                ['code' => '02', 'name' => 'Impuesto Selectivo de Consumo'],
                ['code' => '03', 'name' => 'Impuesto Único a los combustibles'],
                ['code' => '04', 'name' => 'Impuesto específico de Bebidas Alcohólicas'],
                [
                    'code' => '05',
                    'name' => 'Impuesto Específico sobre las bebidas envasadas sin contenido alcohólico y jabones de tocador'
                ],
                ['code' => '06', 'name' => 'Impuesto a los Productos de Tabaco'],
                ['code' => '07', 'name' => 'IVA (cálculo especial)'],
                ['code' => '08', 'name' => 'IVA Régimen de Bienes Usados (Factor)'],
                ['code' => '12', 'name' => 'Impuesto Específico al Cemento'],
                ['code' => '99', 'name' => 'Otros'],

            ];
            $codesTarifaIVA = [
                ['code' => '01', 'name' => 'Tarifa 0% (Exento)', 'value' => 0],
                ['code' => '02', 'name' => 'Tarifa reducida 1%', 'value' => 1],
                ['code' => '03', 'name' => 'Tarifa reducida 2%', 'value' => 2],
                ['code' => '04', 'name' => 'Tarifa reducida 4%', 'value' => 4],
                ['code' => '05', 'name' => 'Transitorio 0%', 'value' => 0],
                ['code' => '06', 'name' => 'Transitorio 4%', 'value' => 4],
                ['code' => '07', 'name' => 'Transitorio 8%', 'value' => 8],
                ['code' => '08', 'name' => 'Tarifa general 13%', 'value' => 13],


            ];


            $view->with(compact('codesTaxes', 'codesTarifaIVA'));
        });


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
