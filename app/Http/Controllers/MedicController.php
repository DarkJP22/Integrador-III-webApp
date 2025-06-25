<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Speciality;
use App\Repositories\MedicRepository;
use Carbon\Carbon;
use App\Repositories\ScheduleRepository;
use App\Repositories\AppointmentRepository;
use App\User;
use App\Office;

class MedicController extends Controller
{
    public function __construct(MedicRepository $medicRepo, ScheduleRepository $scheduleRepo, AppointmentRepository $appointmentRepo) {
        
        $this->medicRepo = $medicRepo;
        $this->scheduleRepo = $scheduleRepo;
        $this->appointmentRepo = $appointmentRepo;
        $this->middleware('auth')->except('index');

        View::share('specialities', Speciality::all());
    }
    public function index()
    {
        //$medics = [];
        $search['typeOfSearch'] = request('typeOfSearch');

        $items = [];
       

        if (request()->all()) {
            if (trim(request('q')) != '' || request('province') != '' || request('canton') != '' || request('district') != '' || request('lat') != '' || request('lon') != '' || request('speciality') != '') {
                $search['q'] = trim(request('q'));
                $search['speciality'] = request('speciality');
                $search['province'] = request('province');
                $search['canton'] = request('canton');
                $search['district'] = request('district');
                $search['lat'] = request('lat');
                $search['lon'] = request('lon');
                $search['general'] = request('general');
                $search['date'] = request('date');
                $search['active'] = 1;
                


                $items = $this->medicRepo->findAll($search);
              

            }
        }

      

        return view('medics.index', compact('items', 'search'));
    }

    public function reservation(User $medic, Office $office)
    {
        if (!auth()->user()->active) return redirect('/');


        if (!$medic->hasrole('medico')) return redirect('/');
        if (!$medic->verifyOffice($office->id)) return redirect('/');

        return view('medics.reservation', compact('medic', 'office'));
    }

    /**
     * Lista de todas las citas de un doctor sin paginar
     */
    public function getAppointments($id)
    {
        $search = request()->all();
        $search['date1'] = isset($search['date1']) ? Carbon::parse($search['date1']) : '';
        $search['date2'] = isset($search['date2']) ? Carbon::parse($search['date2']) : '';

        $appointments = $this->appointmentRepo->findAllByDoctorWithoutPagination($id, $search);

        return $appointments;

    }

    /**
     * Lista de todas las citas de un doctor sin paginar
     */
    public function getSchedules($id)
    {
        $search = request()->all();
        $search['date1'] = isset($search['date1']) ? Carbon::parse($search['date1']) : '';
        $search['date2'] = isset($search['date2']) ? Carbon::parse($search['date2']) : '';

        $schedules = $this->scheduleRepo->findAllByDoctorWithoutPagination($id, $search);

        return $schedules;

    }

}
