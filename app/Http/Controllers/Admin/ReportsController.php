<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentRequestStatus;
use App\Enums\SubscriptionInvoicePaidStatus;
use App\SubscriptionInvoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Repositories\MedicRepository;
use App\User;
use App\Subscription;
use App\Plan;
use App\Appointment;
use App\Office;
use App\Patient;

class ReportsController extends Controller
{
    function __construct(protected MedicRepository $medicRepo)
    {
        $this->middleware('auth');

    }

    public function incomes()
    {
        $this->authorize('create', User::class);

        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');

        $attended = [
            'medics' => [],
            'totalAttended' => 0,
            'totalPending' => 0,
        ];

        $statistics = [
            'medicsPlans' => [],
            'individualByAppointmentAttended' => $attended
        ];


        if (request('start')) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $medics = User::with([
                'incomes' => function ($query) use ($start, $end) {
                    $query->where([
                        ['incomes.date', '>=', $start->startOfDay()],
                        ['incomes.date', '<=', $end->endOfDay()]
                    ]);
                }
            ])->whereHas('roles', function ($q) {
                $q->where('name', 'medico');
            })->where('active', 1)->get();


            $medicsArray = [];
            $totalAttended = 0;
            $totalPending = 0;


            foreach ($medics as $medic) {

                // $incomesAttented = $medic->incomes()->where([
                //     ['incomes.date', '>=', $start],
                //     ['incomes.date', '<=', $end->endOfDay()]
                // ])->where('type', 'I');

                // $incomesPending = $medic->incomes()->where([
                //     ['incomes.date', '>=', $start],
                //     ['incomes.date', '<=', $end->endOfDay()]
                // ])->where('type', 'P');

                $totaIncomes = $medic->incomes;

                $incomesAttented = $totaIncomes->filter(function ($item, $key) {
                    return $item->type == 'I';
                });

                $incomesPending = $totaIncomes->filter(function ($item, $key) {
                    return $item->type == 'P';
                });


                $totalMedicAttended = $incomesAttented->sum('amount');
                $totalMedicPending = $incomesPending->sum('amount');


                $medicData = [
                    'id' => $medic->id,
                    'name' => $medic->name,
                    'attented' => $incomesAttented->count(),
                    'attented_amount' => $totalMedicAttended,
                    'pending' => $incomesPending->count(),
                    'pending_amount' => $totalMedicPending,

                ];

                $medicsArray[] = $medicData;

                $totalAttended += $totalMedicAttended;
                $totalPending += $totalMedicPending;

            }

            $attended = [
                'medics' => $medicsArray,
                'totalAttended' => $totalAttended,
                'totalPending' => $totalPending,
            ];

            $plans = Plan::all();

            foreach ($plans as $plan) {
                $subscriptions = Subscription::where('plan_id', $plan->id)->count();

                $planData = [
                    'title' => $plan->title,
                    'medics' => $subscriptions,
                    'cost' => $plan->cost,
                    'total' => $subscriptions * $plan->cost,
                ];

                $medicsPlans[] = $planData;
            }

            $statistics = [
                'medicsPlans' => $medicsPlans,
                'individualByAppointmentAttended' => $attended
            ];

        }

        return view('admin.reports.incomes', compact('statistics', 'search'));


    }

    public function medics()
    {
        $this->authorize('create', User::class);

        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');


        $medics = User::whereHas('roles', function ($q) {
            $q->where('name', 'medico');
        })->get();

        $statistics = [
            'medics' => [],
            'totalMedics' => 0,
            'appointments' => [],
            'totalAppointments' => 0

        ];

        if (request('start')) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);


            $medicsItems = User::whereHas('roles', function ($q) {
                $q->where('name', 'medico');
            });


            if (isset($search['medic']) && $search['medic'] != '') { //si es por clinica y por medico individual
                $medicsItems = $medicsItems->where('id', $search['medic']);


                $appointments = $medicsItems->first()->appointments()->where([
                    ['appointments.date', '>=', $start],
                    ['appointments.date', '<=', $end->endOfDay()]
                ]);

            } else {

                $appointments = Appointment::where([
                    ['appointments.date', '>=', $start],
                    ['appointments.date', '<=', $end->endOfDay()]
                ]);


            }
            $totalMedics = $medicsItems->count();
            $totalAppointments = $appointments->count();


            $appointments = $appointments->selectRaw('status, count(*) items')
                ->groupBy('status')
                ->orderBy('status', 'DESC')
                ->get()
                ->toArray();

            $medicsItems = $medicsItems->selectRaw('active, count(*) items')
                ->groupBy('active')
                ->orderBy('active', 'DESC')
                ->get()
                ->toArray();
            $statistics = [
                'medics' => $medicsItems,
                'totalMedics' => $totalMedics,
                'appointments' => $appointments,
                'totalAppointments' => $totalAppointments
            ];


        }


        return view('admin.reports.medics', compact('statistics', 'search', 'medics'));


    }

    public function appointments()
    {
        $search['year'] = request('year');
        $search['month'] = request('month');

        $medicsArray = [];
        $totalAppointmentsCount = 0;
        $totalAppointmentRequestsCount = 0;
        $totalAppointmentRequestsPromoCount = 0;
        $totalIncomes = 0;

        if (request('month')) {

            $medics = User::with([
                'appointments' => function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->whereYear('created_at', $search['year'])
                            ->whereMonth('created_at', $search['month']);
                    });
                },
                'appointmentRequests' => function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->whereYear('created_at', $search['year'])
                            ->whereMonth('created_at', $search['month']);
                    });
                },
                'subscription.plan',
            ])->whereHas('roles', function ($q) {
                $q->where('name', 'medico');
            })->whereHas('subscription.plan')
                ->get();

            $medics->each(function ($medic) use (
                $search,
                &$medicsArray,
                &$totalAppointmentsCount,
                &$totalAppointmentRequestsCount,
                &$totalAppointmentRequestsPromoCount,
                &$totalIncomes
            ) {

                $plan = $medic->subscription->plan;

                $appointments_count = $medic->appointments->count();
                $appointment_requests_in_range_count = $medic->appointmentRequests()
                    ->where('status', AppointmentRequestStatus::SCHEDULED)
                    ->where(function ($query) use ($search) {
                        $query->whereYear('created_at', $search['year'])
                            ->whereMonth('created_at', $search['month']);
                    })
                    ->whereRaw('ABS(TIMESTAMPDIFF(MINUTE, created_at, scheduled_at)) <= ?', $plan->commission_discount_range_in_minutes)
                    ->count();

                $appointment_requests_out_range_count = $medic->appointmentRequests()
                    ->where('status', AppointmentRequestStatus::SCHEDULED)
                    ->where(function ($query) use ($search) {
                        $query->whereYear('created_at', $search['year'])
                            ->whereMonth('created_at', $search['month']);
                    })
                    ->whereRaw('ABS(TIMESTAMPDIFF(MINUTE, created_at, scheduled_at)) > ?', $plan->commission_discount_range_in_minutes)
                    ->count();

                $appointment_requests_pending_count = $medic->appointmentRequests()
                    ->whereIn('status', [AppointmentRequestStatus::PENDING, AppointmentRequestStatus::RESERVED])
                    ->where(function ($query) use ($search) {
                        $query->whereYear('created_at', $search['year'])
                            ->whereMonth('created_at', $search['month']);
                    })
                    ->count();

                $cost_by_appointment = $medic->specialities->count() ? $plan->specialist_cost_commission_by_appointment : $plan->general_cost_commission_by_appointment;
                $commission_discount = ($plan->commission_discount / 100);
                //$commission_discount_val = ($cost_by_appointment * $commission_discount);
                //$cost_by_appointment_with_discount = $cost_by_appointment - $commission_discount_val;

                $pendingSubscriptionInvoices = $medic->subscriptionInvoices()->where('paid_status', '<>', SubscriptionInvoicePaidStatus::PAID)->count();
                $commission_amount = ($cost_by_appointment * $appointment_requests_in_range_count) + ($cost_by_appointment * $appointment_requests_out_range_count) + ($cost_by_appointment * $appointment_requests_pending_count) + ($cost_by_appointment * $appointments_count);
                $commission_discount_amount = ($cost_by_appointment * $appointment_requests_in_range_count) * $commission_discount;

                $medicData = [
                    'id' => $medic->id,
                    'name' => $medic->name,
                    'appointments_count' => $appointments_count,
                    'appointment_requests_count' => $appointment_requests_pending_count + $appointment_requests_out_range_count + $appointment_requests_in_range_count,
                    'appointment_requests_promo_count' => $appointment_requests_in_range_count,
                    'commission' => $cost_by_appointment,
                    'commission_amount' => $commission_amount,
                    'commission_discount_porc' => $plan->commission_discount,
                    'commission_discount_amount' => $commission_discount_amount,
                    'commission_total' => $commission_amount - $commission_discount_amount,
                    'status' => $pendingSubscriptionInvoices ? 'Pendiente' : 'Al Dia',
                    360
                ];

                $medicsArray[] = $medicData;

                $totalAppointmentsCount += $medicData['appointments_count'];
                $totalAppointmentRequestsCount += $medicData['appointment_requests_count'];
                $totalAppointmentRequestsPromoCount += $medicData['appointment_requests_promo_count'];
                $totalIncomes += $medicData['commission_total'];

            });
        }
        $data = [
            'medics' => $medicsArray,
            'totalAppointmentsCount' => $totalAppointmentsCount,
            'totalAppointmentRequestsCount' => $totalAppointmentRequestsCount,
            'totalAppointmentRequestsPromoCount' => $totalAppointmentRequestsPromoCount,
            'totalIncomes' => $totalIncomes,
        ];

        return view('admin.reports.appointments', [
            'search' => $search,
            'months' => getMonths(),
            'years' => getYearsFromDates('2024-01-01', now()->toDateString()),
            'data' => $data
        ]);
    }

    public function annualPerformance()
    {
        $search['year'] = request('year');
        $months = getMonths();

        $data = [];

        if (request('year')) {
            $currentMonth = now()->month;
            foreach ($months as $key => $month) {
                $totalPatientsCount = $currentMonth >= (int) $key ? Patient::where(function ($query) use ($search, $key) {
                    $query->whereYear('created_at', $search['year'])
                        ->whereMonth('created_at', $key);
                })->count() : 0;
                $totalMedicsCount = $currentMonth >= (int) $key ? User::where(function ($query) use ($search, $key) {
                    $query->where('created_at', '<=', now()->setMonth((int)$key)->endOfMonth());
                })->whereHas('roles', function ($q) {
                    $q->where('name', 'medico');
                })->count() : 0;
                $totalNewMedicsCount = User::where(function ($query) use ($search, $key) {
                    $query->whereYear('created_at', $search['year'])
                        ->whereMonth('created_at', $key);
                })->whereHas('roles', function ($q) {
                    $q->where('name', 'medico');
                })->count();

                $paidMedics = $currentMonth >= (int) $key ? User::whereHas('subscriptionInvoices', function ($query) use ($key) {
                    $query->where('paid_status', SubscriptionInvoicePaidStatus::PAID)
                        ->where('invoice_date', '<=', now()->setMonth((int)$key)->endOfMonth());
                })->whereHas('roles', function ($q) {
                    $q->where('name', 'medico');
                })->count() : 0;

                $pendingMedics = $currentMonth >= (int) $key ? User::whereHas('subscriptionInvoices', function ($query) use ($key) {
                    $query->where('paid_status', '<>', SubscriptionInvoicePaidStatus::PAID)
                        ->where('invoice_date', '<=', now()->setMonth((int)$key)->endOfMonth());
                })->whereHas('roles', function ($q) {
                    $q->where('name', 'medico');
                })->count() : 0;

                $inactiveMedics = $currentMonth >= (int) $key ? User::whereHas('subscriptionInvoices', function ($query) {
                    $query->where('paid_status', '<>', SubscriptionInvoicePaidStatus::PAID)
                        ->where('due_date', '<=', now()->subMonths(3));
                })->whereHas('roles', function ($q) {
                    $q->where('name', 'medico');
                })->where('active', 0)
                    ->count() : 0;

                $incomes = $currentMonth >= (int) $key ? SubscriptionInvoice::where('paid_status', SubscriptionInvoicePaidStatus::PAID)
                    ->where('invoice_date', '<=', now()->setMonth((int)$key)->endOfMonth())
                    ->sum('total') : 0;

                $pendingIncomes = $currentMonth >= (int) $key ? SubscriptionInvoice::where('paid_status', '<>', SubscriptionInvoicePaidStatus::PAID)
                    ->where('invoice_date', '<=', now()->setMonth((int)$key)->endOfMonth())
                    ->sum('total') : 0;

                $data[] = [
                    'month_id' => $key,
                    'month' => $month,
                    'totalPatientsCount' => $totalPatientsCount,
                    'totalMedicsCount' => $totalMedicsCount,
                    'totalNewMedicsCount' => $totalNewMedicsCount,
                    'incomes' => $incomes,
                    'pendingIncomes' => $pendingIncomes,
                    'paidMedicsCount' => $paidMedics,
                    'paidMedics' => round(($paidMedics * 100) / ($totalMedicsCount ?: 1)),
                    'pendingMedicsCount' => $pendingMedics,
                    'pendingMedics' => round(($pendingMedics * 100) / ($totalMedicsCount ?: 1)),
                    'inactiveMedicsCount' => $inactiveMedics,
                    'inactiveMedics' => round(($inactiveMedics * 100 / ($totalMedicsCount ?: 1))),
                ];


            }


        }
        return view('admin.reports.annual', [
            'search' => $search,
            'years' => getYearsFromDates('2024-01-01', now()->toDateString()),
            'data' => $data
        ]);
    }

    public function clinics()
    {
        $this->authorize('create', User::class);

        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['clinic'] = request('clinic');


        $clinics = Office::all();

        $statistics = [
            'totalClinics' => $clinics->count(),
            'appointments' => [],
            'totalAppointments' => 0

        ];

        if (request('start') && request('clinic')) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);


            if (isset($search['clinic']) && $search['clinic'] != '') { //sí es por clínica y por médico individual

                $appointments = Appointment::where([
                    ['appointments.date', '>=', $start],
                    ['appointments.date', '<=', $end->endOfDay()]
                ])->where('office_id', $search['clinic']);

            } else {

                $appointments = Appointment::where([
                    ['appointments.date', '>=', $start],
                    ['appointments.date', '<=', $end->endOfDay()]
                ]);


            }


            $totalAppointments = $appointments->count();


            $appointments = $appointments->selectRaw('status, count(*) items')
                ->groupBy('status')
                ->orderBy('status', 'DESC')
                ->get()
                ->toArray();


            $statistics = [
                'totalClinics' => $clinics->count(),
                'appointments' => $appointments,
                'totalAppointments' => $totalAppointments
            ];


        }


        return view('admin.reports.clinics', compact('statistics', 'search', 'clinics'));


    }

    public function patients()
    {
        $this->authorize('create', User::class);

        $search['start'] = request('start');
        $search['end'] = request('end');


        $statistics = [
            'patients' => [],
            'totalPatients' => 0

        ];

        if (request('start')) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);


            $patients = Patient::where([
                ['patients.created_at', '>=', $start],
                ['patients.created_at', '<=', $end->endOfDay()]
            ]);


            $totalPatients = $patients->count();


            $patients = $patients->selectRaw('province, count(*) items')
                ->groupBy('province')
                ->orderBy('province', 'DESC')
                ->get()
                ->toArray();


            $statistics = [

                'patients' => $patients,
                'totalPatients' => $totalPatients
            ];


        }


        return view('admin.reports.patients', compact('statistics', 'search'));


    }


}
