<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Repositories\ClinicRepository;
use Carbon\Carbon;
use App\User;
use App\Office;
use App\Http\Controllers\Controller;

class ClinicController extends Controller
{
    public function __construct(ClinicRepository $clinicRepo) {
        
        $this->clinicRepo = $clinicRepo;
        $this->middleware('auth')->except('index');

    }

    public function index()
    {
        
        $items = [];


        if (request()->all()) {
            if (trim(request('q')) != '' || request('province') != '' || request('canton') != '' || request('district') != '' || request('lat') != '' || request('lon') != '') {

                $search['q'] = trim(request('q'));
                $search['province'] = request('province');
                $search['canton'] = request('canton');
                $search['district'] = request('district');
                $search['lat'] = request('lat');
                $search['lon'] = request('lon');


                $items = $this->clinicRepo->findAll($search);



            }
        }

      

        return $items;
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     * @internal param int $id
     */
    public function show(Office $clinic)
    {
        
        if (!$clinic) {
       
            return response(['message' => 'Clínica no existe'], 404);
          

        }

        $clinic['medics'] = $clinic->doctors();

        return $clinic;
    }


}
