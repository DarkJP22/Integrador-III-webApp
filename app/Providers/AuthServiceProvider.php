<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Patient' => 'App\Policies\PatientPolicy',
        'App\Appointment' => 'App\Policies\AppointmentPolicy',
        'App\ConfigFactura' => 'App\Policies\ConfigFacturaPolicy',
        'App\Product' => 'App\Policies\ProductPolicy',
        'App\Invoice' => 'App\Policies\InvoicePolicy',
        'App\Proforma' => 'App\Policies\ProformaPolicy',
        'App\ReviewApp' => 'App\Policies\ReviewAppPolicy',
        'App\RequestOffice' => 'App\Policies\RequestOfficePolicy',
        'App\Plan' => 'App\Policies\PlanPolicy',
        'App\User' => 'App\Policies\UserPolicy',
        'App\Schedule' => 'App\Policies\SchedulePolicy',
        'App\Discount' => 'App\Policies\DiscountPolicy',
        'App\Receptor' => 'App\Policies\ReceptorPolicy',
        'App\Cierre' => 'App\Policies\CierrePolicy',
        'App\AffiliationPlan' => 'App\Policies\AffiliationPlanPolicy',
        'App\Affiliation' => 'App\Policies\AffiliationPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();


         Gate::before(function ($user) {
            if ($user->hasRole('administrador')) {
                return true;
            }
             if ($user->disabled_by_payment) {
                 return false;
             }
        });

        Gate::define('view-reports', function (User $user) {
            return true;
        });

    }
}
