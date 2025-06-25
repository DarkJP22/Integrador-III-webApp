<?php

namespace App\Http\Controllers\Calendars;

use Illuminate\Http\Request;
use App\Repositories\OfficeRepository;
use Validator;
use App\Office;
use App\User;
use App\Http\Controllers\Controller;

class OfficeController extends Controller
{
    
    public function __construct(OfficeRepository $officeRepo) {
        $this->middleware('auth');
        
        $this->officeRepo = $officeRepo;
    }


    public function index(User $medic = null)
    {
        $medic = $medic ? $medic : auth()->user();

        $search = request()->all();
        $search['active'] = 1;
        $search['dir'] = 'ASC';
        $offices = $this->officeRepo->findAllByDoctorWithoutPagination($medic, $search);

        return $offices;
    }

   
   
    
}
