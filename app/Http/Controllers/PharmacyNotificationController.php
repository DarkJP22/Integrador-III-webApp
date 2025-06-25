<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pharmacy;

class PharmacyNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function update(Pharmacy $pharmacy)
    {
       
        $pharmacy->update([
            'notification_date' => null,
            'notification' => 0,
            'lat' => request('lat'),
            'lon'=> request('lon')
        ]);

        if (request()->wantsJson()) {

            return response([], 204);

        }

    }
}
