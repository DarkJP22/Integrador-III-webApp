<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Appointment;
use App\Reminder;

class AppointmentReminderController extends Controller
{

    public function store(Appointment $appointment)
    {

        $reminder = Reminder::create([
            'appointment_id' => $appointment->id,
            'reminder_time' => request('reminder_time')
        ]);

        return $reminder;

    }

    public function show()
    {

        $exitCode = \Artisan::call('gps:appointmentReminder');

        return $exitCode;

    }
}
