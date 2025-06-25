<?php

namespace App\Http\Controllers\Operator;

use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;

class AppointmentRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->hasRole('operador')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['status'] = request('status');
        $search['medic'] = request('medic');

        $appointmentRequests = AppointmentRequest::with('patient', 'office', 'user')
            ->search($search)
            ->when(filled($search['status']), function ($query) use ($search) {
                $query->whereIn('status', $search['status']);
            })
            ->when(filled($search['medic']), function ($query) use ($search) {
                $query->where('medic_id', $search['medic']);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate();

        $averages = AppointmentRequest::query()
            ->when(filled($search['medic']), function ($query) use ($search) {
                $query->where('medic_id', $search['medic']);
            })
            ->where('status', AppointmentRequestStatus::SCHEDULED)
            ->byAverageResponse()
            ->first();
        //->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, scheduled_at)) as average_response')->first();

        return view('operator.appointmentRequests.index', [
            'appointmentRequests' => $appointmentRequests,
            'search' => $search,
            'medics' => User::query()->whereHas('roles', function ($query) {
                $query->where('name', 'medico');
            })->get(),
            'statuses' => AppointmentRequestStatus::options(),
            'averages' => $averages
        ]);


    }
}
