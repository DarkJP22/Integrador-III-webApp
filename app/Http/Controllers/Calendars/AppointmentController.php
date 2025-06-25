<?php

namespace App\Http\Controllers\Calendars;

use Illuminate\Http\Request;
use App\Appointment;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Repositories\AppointmentRepository;

class AppointmentController extends Controller
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
    public function index($medicId = null)
    {
        $medic = $medicId ? $medicId : auth()->id();

        $search = request()->all();
        $search['date1'] = isset($search['date1']) ? Carbon::parse($search['date1']) : '';
        $search['date2'] = isset($search['date2']) ? Carbon::parse($search['date2']) : '';

        $appointments = $this->appointmentRepo->findAllByDoctorWithoutPagination($medic, $search);

        if( !auth()->user()->hasRole('clinica') && !auth()->user()->hasRole('asistente')){
            return $appointments;
        }

        
        return $appointments->merge( $this->getAppointmentFromOthersMedics($medic, $search) );
      

    }

    public function getAppointmentFromOthersMedics($currentMedic, $search)
    {
        $date1 = $search['date1'];
        $date2 = (isset($search['date2']) && $search['date2'] != "") ? $search['date2'] : $search['date1'];
        $date2 = $date2;

        $appoinmentsOtherMedics = Appointment::with('room')->where('office_id', $search['office'])
                                    ->where('user_id', '<>', $currentMedic)
                                    ->where([
                                        ['appointments.date', '>=', $date1],
                                        ['appointments.date', '<=', $date2->endOfDay()]
                                    ])->get();

        return $appoinmentsOtherMedics;
    }

}
