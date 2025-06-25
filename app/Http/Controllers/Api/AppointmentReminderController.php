<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Reminder;
use App\User;
use App\Repositories\AppointmentRepository;
use App\Notifications\NewAppointment;
use App\Http\Controllers\Controller;

class AppointmentReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        

       
    }

    /**
     * Lista de todas las citas de un doctor sin paginar
     */
    public function store($appointmentId)
    {

        $data = request()->all();

        $data['appointment_id'] = $appointmentId;

        $reminder = Reminder::create($data);

        return $reminder;

    }


   
}
