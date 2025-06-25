<?php

namespace App\Http\Controllers\Api\Medic;

use App\AppointmentRequest;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MedicAppointmentRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, User $medic)
    {

        $date1 = Carbon::parse(request('date1'))->startOfDay();
        $date2 = Carbon::parse(request('date2'))->endOfDay();

        $appointmentRequests = AppointmentRequest::with('user', 'medic')
            ->where('medic_id', $medic->id)
            ->where('office_id', request('office'))
            ->where([
                ['scheduled_date', '>=', $date1],
                ['scheduled_date', '<=', $date2->endOfDay()]
            ])
            ->get();


        return $appointmentRequests;
    }


}
