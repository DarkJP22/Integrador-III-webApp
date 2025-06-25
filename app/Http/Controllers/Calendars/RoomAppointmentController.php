<?php

namespace App\Http\Controllers\Calendars;

use Illuminate\Http\Request;
use App\Appointment;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Repositories\AppointmentRepository;
use App\Room;

class RoomAppointmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->middleware('auth');
        $this->appointmentRepo = $appointmentRepo;

    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index(Room $room)
    {
       

        $search = request()->all();
        $search['date1'] = isset($search['date1']) ? Carbon::parse($search['date1']) : '';
        $search['date2'] = isset($search['date2']) ? Carbon::parse($search['date2']) : '';

        $appointments = Appointment::where('room_id', $room->id)
                                    ->where('office_id', $room->office_id)
                                    ->with('patient', 'user', 'office')
                                    ->get();//$this->appointmentRepo->findAllByDoctorWithoutPagination($medic, $search);

       
        
        return $appointments;
      

    }

   

}
