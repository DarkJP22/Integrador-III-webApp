<?php

namespace App\Http\Controllers\Calendars;

use Illuminate\Http\Request;
use App\Appointment;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Repositories\AppointmentRepository;
use App\Repositories\ScheduleRepository;

class ScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->middleware('auth');
        $this->scheduleRepo = $scheduleRepo;

    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index($medicId = null)
    {
        $medic = $medicId ? $medicId : auth()->id();

        $search = request()->all();
        $search['date1'] = isset($search['date1']) ? Carbon::parse($search['date1']) : '';
        $search['date2'] = isset($search['date2']) ? Carbon::parse($search['date2']) : '';
        
        $schedules = $this->scheduleRepo->findAllByDoctorWithoutPagination($medic, $search);

        return $schedules;

    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function appointmentsSchedule($medicId = null)
    {
        $medic = $medicId ? $medicId : auth()->id();

        $search = request()->all();
        $search['date1'] = isset($search['date1']) ? Carbon::parse($search['date1']) : '';
        $search['date2'] = isset($search['date2']) ? Carbon::parse($search['date2']) : '';

        $appointments = Appointment::where('user_id', $medic);
        $items = [];
        if (isset($search['date1']) && $search['date1'] != "") {
           
           // dd($search['date2']);

            $date1 = $search['date1'];
            $date2 = (isset($search['date2']) && $search['date2'] != "") ? $search['date2'] : $search['date1'];
            $date2 = $date2;
            //dd($search);

            $appointments = $appointments->where([
                ['appointments.date', '>=', $date1->startOfDay()],
                ['appointments.date', '<=', $date2->endOfDay()]
            ]);
            

            $items = $appointments->get()->filter(function ($item, $key) use($search) {
                return Carbon::parse($item->start) >= $search['date1'] && Carbon::parse($item->start) <= $search['date2'];
            });
            
            //dd($items);
        }

      

        return $items;

    }

     /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function migrationDateAppointments($medicId = null)
    {
        $medic = $medicId ? $medicId : auth()->id();

        $search = request()->all();
        $search['date1'] = isset($search['date1']) ? Carbon::parse($search['date1']) : '';
        $search['date2'] = isset($search['date2']) ? Carbon::parse($search['date2']) : '';
        $search['dateTo'] = isset($search['dateTo']) ? Carbon::parse($search['dateTo']) : '';

        $appointments = Appointment::where('user_id', $medic);
        $items = [];

        if (isset($search['date1']) && $search['date1'] != "") {
           
           // dd($search['date2']);

            $date1 = $search['date1'];
            $date2 = (isset($search['date2']) && $search['date2'] != "") ? $search['date2'] : $search['date1'];
            $date2 = $date2;
            //dd($search);

            $appointments = $appointments->where([
                ['appointments.date', '>=', $date1->startOfDay()],
                ['appointments.date', '<=', $date2->endOfDay()]
            ]);
            

            $items = $appointments->get()->filter(function ($item, $key) use($search) {
                return Carbon::parse($item->start) >= $search['date1'] && Carbon::parse($item->start) <= $search['date2'];
            });
            
            //dd($items);
        }

        foreach($items as $appointment){

            $appointment->date = $search['dateTo']->startOfDay();

            $start = Carbon::parse($appointment->start);
            $end = Carbon::parse($appointment->end);

            $start->day = $search['dateTo']->day;
            $start->month = $search['dateTo']->month;
            $start->year = $search['dateTo']->year;
            
            $end->day = $search['dateTo']->day;
            $end->month = $search['dateTo']->month;
            $end->year = $search['dateTo']->year;

            $appointment->start = $start->format('Y-m-d') . 'T' . $start->toTimeString();
            $appointment->end = $end->format('Y-m-d'). 'T' . $end->toTimeString();
            $appointment->save();
        }
      

        return $items;

    }

}
