<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Repositories\ScheduleRepository;

class MedicScheduleController extends Controller
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

    public function index($medicId)
    {
        $search = request()->all();
        $search['date1'] = isset($search['date1']) ? Carbon::parse($search['date1']) : '';
        $search['date2'] = isset($search['date2']) ? Carbon::parse($search['date2']) : '';


        $schedules = $this->scheduleRepo->findAllByDoctorWithoutPagination($medicId, $search);

        return $schedules;
        
    }
   
    
   
    
}
