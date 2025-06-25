<?php

namespace App\Http\Controllers\Medic;

use App\Actions\GetAppointmentAndRequestsForCommission;
use App\Appointment;
use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Enums\AppointmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class ReportsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');

    }

    public function invoices()
    {
        Gate::authorize('view-reports');

        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }

        $search['start'] = request('start') ? request('start') : Carbon::now()->startOfDay()->toDateTimeString();
        $search['end'] = request('end');
        $search['office'] = request('office');

        //dd($search);


        $invoices = auth()->user()->invoices()->with('notascreditodebito', 'user')->search($search)
            ->where(function ($query) {
                $query->where('TipoDocumento', '01')
                    ->orWhere('TipoDocumento', '04');
            })
            // ->where(function ($query) {
            //     $query->whereNull('status_fe')
            //         ->orWhere('status_fe','<>', 'rechazado');
            // })
            ->where('status', 1);


        if (!request('office')) {

            $clinicsIds = auth()->user()->clinicsWithPermissionFe()->pluck('id');

            $invoices = $invoices->whereIn('office_id', $clinicsIds);
        }

        $invoicesCollection = clone $invoices;
        $invoicesCollection = $invoicesCollection->get();

        $totalVentasCRC = $invoicesCollection->sum(function ($invoice) {
            return $invoice->CodigoMoneda == 'CRC' ? $invoice->TotalWithNota : 0;
        });
        $totalVentasUSD = $invoicesCollection->sum(function ($invoice) {
            return $invoice->CodigoMoneda == 'USD' ? $invoice->TotalWithNota : 0;
        });


        if (request('print')) {
            $invoices = $invoices->get();
            return view('medic.reports.invoicesPrint', compact('invoices', 'totalVentasCRC', 'totalVentasUSD'));
        }

        $invoices = $invoices->paginate(10);


        return view('medic.reports.invoices', compact('invoices', 'totalVentasCRC', 'totalVentasUSD', 'search'));

    }

    public function unbilled()
    {
        Gate::authorize('view-reports');

        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['office'] = request('office');


        $unbilled = auth()->user()->appointments()->with('patient')
            ->where('status', 1)
            ->where('finished', 1)
            ->where('billed', 0)
            ->search($search)->latest()->paginate(10); //auth()->user()->invoices()->search($search)->latest()->paginate(10);


        return view('medic.reports.unbilled', compact('unbilled', 'search'));

    }

    public function appointments()
    {
        Gate::authorize('view-reports');

        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }

        $search['start'] = request('start');
        $search['end'] = request('end');
        // $data = [
        //     'data' => [],
        //     'total' => 0
        // ];
        $data = null;
        $dataAppointments = null;
        $dataRequests = null;
        $totalAppointments = 0;
        $totalRequests = 0;
        //if (request('start')) {

            $appointments = auth()->user()->appointments()
                ->search($search);
            $appointmentRequests = auth()->user()->appointmentRequests()
                ->search($search);

            $creators = implode(',', [auth()->user()->id, ...auth()->user()->assistants->pluck('id')]);

            $dataAppointments = $appointments->selectRaw("count(case when status = '0' and created_by in (?) then 1 end) as reservedByMedic", [$creators])
                ->selectRaw("count(case when status = '0' and created_by not in (?) then 1 end) as reservedByPatient", [$creators])
                ->selectRaw("count(case when status = '1' then 1 end) as attended")
                ->selectRaw("count(case when status = '2' then 1 end) as noassist")
                ->first();

            $dataRequests = $appointmentRequests->selectRaw("count(case when status = '0' and user_id not in (?) then 1 end) as reserved", [$creators])
                ->selectRaw("count(case when status = '1' then 1 end) as scheduled")
                ->selectRaw("count(case when status = '2' then 1 end) as pending")
                ->selectRaw("count(case when status = '3' then 1 end) as cancelled")
                ->first();

            $totalAppointments = $dataAppointments->reservedByMedic + $dataAppointments->reservedByPatient + $dataAppointments->attended + $dataAppointments->noassist;
            $totalRequests = $dataRequests->reserved + $dataRequests->scheduled + $dataRequests->pending + $dataRequests->cancelled;
            // $data['data'] = $appointments->selectRaw('status, count(*) items')
            //     ->groupBy('status')
            //     ->orderBy('status', 'DESC')
            //     ->get()
            //     ->toArray();

            // $data['total'] = 0;

            // foreach ($data['data'] as $item) {
            //     $data['total'] += $item['items'];
            // }

       // }
        $data = [
            'appointments' => $dataAppointments,
            'totalAppointments' => $totalAppointments,
            'appointmentRequests' => $dataRequests,
            'totalRequests' => $totalRequests,
        ];

        if (request()->wantsJson()) {

            return response($data, 200);

        }

        $data = (object)$data;

        return view('medic.reports.appointmentsStatus', compact('data', 'search'));

    }

    public function incomes(GetAppointmentAndRequestsForCommission $getAppointmentAndRequestsForCommission)
    {
        Gate::authorize('view-reports');

        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }

        $medic = auth()->user();
        $data = [];
        $search['start'] = request('start');
        $search['end'] = request('end');

        if (request('start')) {

            $plan = $medic->subscription->plan;
            $appointmentAndRequestData = $getAppointmentAndRequestsForCommission->handle($medic, $medic->subscription->plan, $search['start'], $search['end']);

            $data = [
                [
                    'name' => 'Citas en línea efectivas',
                    'quantity' => $appointmentAndRequestData->scheduledAppointmentsQuantity,
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'amount' => $appointmentAndRequestData->scheduledAppointmentsQuantity * $appointmentAndRequestData->cost_by_appointment,
                ],
                [
                    'name' => 'Solicitudes de citas < '.$plan->commission_discount_range_in_minutes.' min',
                    'quantity' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity,
                    'price' => $appointmentAndRequestData->cost_by_appointment_with_discount,
                    'amount' => $appointmentAndRequestData->scheduledAppointmentRequestsInRangeQuantity * $appointmentAndRequestData->cost_by_appointment_with_discount,
                ],
                [
                    'name' => 'Solicitudes de citas > '.$plan->commission_discount_range_in_minutes.' min',
                    'quantity' => $appointmentAndRequestData->scheduledAppointmentRequestsOutRangeQuantity,
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'amount' => $appointmentAndRequestData->scheduledAppointmentRequestsOutRangeQuantity * $appointmentAndRequestData->cost_by_appointment,
                ],
                [
                    'name' => 'Solicitudes de citas no agendadas',
                    'quantity' => $appointmentAndRequestData->pendingAppointmentRequestsQuantity,
                    'price' => $appointmentAndRequestData->cost_by_appointment,
                    'amount' => $appointmentAndRequestData->pendingAppointmentRequestsQuantity * $appointmentAndRequestData->cost_by_appointment,
                ]
            ];

        }


        return view('medic.reports.incomes', compact('data', 'search'));

    }
}
