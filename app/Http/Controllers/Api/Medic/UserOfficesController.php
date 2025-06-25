<?php

namespace App\Http\Controllers\Api\Medic;

use Illuminate\Http\Request;
use App\Repositories\OfficeRepository;
use Validator;
use App\Office;
use App\Http\Controllers\Controller;

class UserOfficesController extends Controller
{
    
    public function __construct(protected OfficeRepository $officeRepo) {
        $this->middleware('auth');
    }

    public function index()
    {
        $search = request()->all();
        $search['active'] = 1;
        $search['dir'] = 'ASC';
        $search['date'] = today();
       
        $offices = $this->officeRepo->findAllByDoctor(auth()->user(), $search);
       

        return $offices;
    }

   


   
    
}
