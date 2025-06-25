<?php

namespace App\Http\Controllers\Api;

use App\Repositories\MedicRepository;
use App\User;
use App\Http\Controllers\Controller;

class MedicController extends Controller
{
    public function __construct(public MedicRepository $medicRepo)
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {

        $items = [];


        if (request()->all()) {
            // if (trim(request('q')) != '' || request('province') != '' || request('canton') != '' || request('district') != '' || request('lat') != '' || request('lon') != '' || request('speciality') != '' || request('date') || request('accumulated_affiliation')) {
            $search['q'] = trim(request('q', ''));
            $search['speciality'] = request('speciality');
            $search['province'] = request('province');
            $search['canton'] = request('canton');
            $search['district'] = request('district');
            $search['lat'] = request('lat');
            $search['lon'] = request('lon');
            $search['radius'] = request('radius');
            $search['general'] = request('general');
            $search['date'] = request('date');
            $search['active'] = 1;
            $search['commission_by_appointment'] = 1; // verifica que el médico tiene plan detectable (Visible para citas)
            $search['accumulated_affiliation'] = request('accumulated_affiliation');
            $search['type_of_health_professional'] = request('type_of_health_professional');
            $search['order'] = 'name';
            $search['dir'] = 'ASC';


            $items = $this->medicRepo->findAll($search);
            // }
        }


        return $items;
    }


    public function show(User $medic): User
    {
        return $medic->load([
            'offices' => function ($query) {
                //\Log::info('lat-'. request('lat'). 'lon:'.request('lon'));
                if (request('lat') && request('lon')) {
                    return $query->nearLatLng(request('lat'), request('lon'), 300);
                }
                return $query;

            }, 'specialities', 'settings',
            'schedules' => function ($q) {

                return $q->whereDate('date', request('date'));


            },
            'appointments' => function ($q) {

                return $q->whereDate('date', request('date'));


            }
        ]);
    }
}
