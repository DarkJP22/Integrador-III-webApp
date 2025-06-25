<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Office;

class OfficeNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function update(Office $office)
    {
       
        $office->update([
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
