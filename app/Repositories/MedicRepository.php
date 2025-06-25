<?php

namespace App\Repositories;


use App\Office;
use App\User;
use Carbon\Carbon;


class MedicRepository extends DbRepository
{

    protected $limit;
    /**
     * Construct
     * @param  User  $model
     */
    function __construct(User $model)
    {
        $this->model = $model;
        $this->limit = 10;
    }


    /**
     * Find all the users for the admin panel
     * @param  null  $search
     * @return mixed
     * @internal param $username
     */
    public function findAll($search = null)
    {
        $order = 'created_at';
        $dir = 'DESC';

        if (!$search) {
            return $this->model->whereHas('roles', function ($q) {
                $q->where('name', 'medico');
            })->paginate($this->limit);
        }


        if (isset($search['q']) && trim($search['q'])) {
            $query = $this->model->whereHas('roles', function ($q) {
                $q->where('name', 'medico');
            })->Search($search['q']);
        } else {
            $query = $this->model->whereHas('roles', function ($q) {
                $q->where('name', 'medico');
            });
        }


        if (isset($search['active']) && $search['active'] != "") {
            $query = $query->Active($search['active']);
        }

        if (isset($search['commission_by_appointment']) && $search['commission_by_appointment'] != "") {
            $query = $query->whereHas('subscription.plan', function ($q) use ($search) {
                $q->where('commission_by_appointment', $search['commission_by_appointment']);
            });

        }

        if (isset($search['accumulated_affiliation']) && $search['accumulated_affiliation'] != "") {

            $query = $query->where('users.accumulated_affiliation', $search['accumulated_affiliation']);
        }
        if (isset($search['type_of_health_professional']) && $search['type_of_health_professional'] != "") {

            $query = $query->where('users.type_of_health_professional', $search['type_of_health_professional']);
        }

        if (isset($search['lat']) && $search['lat'] != "" && isset($search['lon']) && $search['lon'] != "") {
            $query = $query->withAndWhereHas('offices', function ($q) use ($search) {

                return $q->selectRaw('( 6371 * acos( cos( radians( ? ) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( lat ) ) ) ) AS distance',
                    [$search['lat'], $search['lon'], $search['lat']])
                    ->having('distance', '<', $search['radius'] ?? 10)
                    ->orderBy('distance');
            });
        }
        if (isset($search['province']) && $search['province'] != "") {

            $query = $query->whereHas('offices', function ($q) use ($search) {
                $q->where('province', $search['province']);
            });
        }
        if (isset($search['canton']) && $search['canton'] != "") {
            $query = $query->whereHas('offices', function ($q) use ($search) {
                $q->where('province', $search['province'])
                    ->where('canton', $search['canton']);
            });
        }
        if (isset($search['district']) && $search['district'] != "") {
            $query = $query->whereHas('offices', function ($q) use ($search) {
                $q->where('province', $search['province'])
                    ->where('canton', $search['canton'])
                    ->where('district', $search['district']);
            });
        }

        if (isset($search['date']) && $search['date'] != "") {

            $query = $query->with([
                'schedules' => function ($q) use ($search) {
                    $q->whereDate('date', $search['date']);
                },
                'appointments' => function ($q) use ($search) {
                    $q->whereDate('date', $search['date']);
                }
            ]);
        } else {

            $query = $query->with([
                'schedules' => function ($q) {
                    $q->whereDate('date', '>=', Carbon::now()->startOfDay()->toDateTimeString())
                        ->take(30)->orderBy('date', 'ASC');
                }
            ])->withCount([
                'schedules' => function ($q) {
                    $q->whereDate('date', '>=', Carbon::now()->startOfDay()->toDateTimeString())
                        ->take(30);
                }
            ])->orderBy('schedules_count', 'DESC');
        }

        if (isset($search['speciality']) && $search['speciality'] != "") {
            $query = $query->whereHas('specialities', function ($q) use ($search) {
                $q->where('specialities.id', $search['speciality']);
            });
        }

        if (isset($search['general']) && $search['general'] != "") {

            $query = $query->doesntHave('specialities');
        }


        if (isset($search['order']) && $search['order'] != "") {
            $order = $search['order'];
        }
        if (isset($search['dir']) && $search['dir'] != "") {
            $dir = $search['dir'];
        }


        $query = $query->with([
            'offices' => function ($q) {
                $q->where('verified', 1);
            },
            'settings'
        ]);

        return $query->with('specialities')->orderBy('users.'.$order, $dir)->paginate($this->limit);
    }
    // public function findAll($search = null)
    // {
    //     $order = 'name';
    //     $dir = 'ASC';

    //     if (!$search) return $this->model->whereHas('roles', function ($q) {
    //         $q->where('name', 'medico');
    //     })->paginate($this->limit);


    //     if (isset($search['lat']) && $search['lat'] != "" && isset($search['lon']) && $search['lon'] != "") {

    //         //$offices = Office::NearLatLng($search['lat'], $search['lon'], 60, 'K');

    //         if (isset($search['q']) && trim($search['q'])) {
    //             $query = $this->model->whereHas('roles', function ($q) {
    //                 $q->where('name', 'medico');
    //                    // ->orWhere('name', 'esteticista');
    //             })->Search($search['q']);
    //         } else {
    //             $query = $this->model->whereHas('roles', function ($q) {
    //                 $q->where('name', 'medico');
    //                    // ->orWhere('name', 'esteticista');
    //             });
    //         }

    //         if (isset($search['active']) && $search['active'] != "") {

    //             $query = $query->where('users.active', $search['active']);
    //         }

    //         if (isset($search['accumulated_affiliation']) && $search['accumulated_affiliation'] != "") {

    //             $query = $query->where('users.accumulated_affiliation', $search['accumulated_affiliation']);
    //         }

    //         // $query = $query->selectRaw('DISTINCT users.id, users.name, users.avatar_path, users.accumulated_affiliation, ( 6371 * acos( cos( radians( ? ) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( lat ) ) ) ) AS distance, offices.name AS office_name', [$search['lat'], $search['lon'], $search['lat']])
    //         //     ->join('office_user', 'users.id', '=', 'office_user.user_id')
    //         //     ->join('offices', 'office_user.office_id', '=', 'offices.id')
    //         //     ->having('distance', '<', $search['radius'] ?? 60); // radius
    //         $query = $query->withAndWhereHas('offices', function ($q) use ($search) {

    //             return $q->selectRaw('( 6371 * acos( cos( radians( ? ) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( lat ) ) ) ) AS distance',[$search['lat'], $search['lon'], $search['lat']])
    //             ->having('distance', '<', $search['radius'] ?? 10)
    //             ->orderBy('distance');
    //         });

    //         if (isset($search['province']) && $search['province'] != "") {
    //             $query = $query->where('offices.province', $search['province']);
    //         }
    //         if (isset($search['canton']) && $search['canton'] != "") {
    //             $query = $query->where('offices.canton', $search['canton']);
    //         }
    //         if (isset($search['district']) && $search['district'] != "") {
    //             $query = $query->where('offices.district', $search['district']);
    //         }

    //         if (isset($search['date']) && $search['date'] != "") {

    //             $query = $query->withAndWhereHas('schedules', function ($q) use ($search) {

    //                 return $q->whereDate('date', $search['date']);
    //             })->withCount(['schedules' => function ($q) use ($search) {
    //                 $q->whereDate('date', $search['date']);
    //             }])->orderBy('schedules_count', 'DESC');
    //         } else {

    //             $query = $query->with(['schedules' => function ($q) {
    //                 $q->whereDate('date', '>=', Carbon::now()->startOfDay()->toDateTimeString())
    //                     ->take(30)->orderBy('date', 'ASC');
    //             }])->withCount(['schedules' => function ($q) {
    //                 $q->whereDate('date', '>=', Carbon::now()->startOfDay()->toDateTimeString())
    //                     ->take(30);
    //             }])->orderBy('schedules_count', 'DESC');
    //         }


    //         if (isset($search['speciality']) && $search['speciality'] != "") {
    //             $query = $query->whereHas('specialities', function ($q) use ($search) {
    //                 $q->where('specialities.id', $search['speciality']);
    //             });
    //         }

    //         if (isset($search['general']) && $search['general'] != "") {
    //             $query = $query->doesntHave('specialities');
    //         }

    //         $query = $query->with(['offices' => function ($q) {
    //             $q->where('verified', 1);
    //         }])->with('specialities');


    //         return $query->with('specialities')->orderBy('users.' . $order, $dir)->paginate($this->limit);

    //         // $paginator = paginate($query->get()->all(), $this->limit);

    //         // return $paginator;
    //     } else {


    //         if (isset($search['q']) && trim($search['q'])) {
    //             $users = $this->model->whereHas('roles', function ($q) {
    //                 $q->where('name', 'medico');
    //             })->Search($search['q']);
    //         } else {
    //             $users = $this->model->whereHas('roles', function ($q) {
    //                 $q->where('name', 'medico');
    //             });
    //         }


    //         if (isset($search['active']) && $search['active'] != "") {
    //             $users = $users->Active($search['active']);
    //         }

    //         if (isset($search['accumulated_affiliation']) && $search['accumulated_affiliation'] != "") {

    //             $query = $users->where('users.accumulated_affiliation', $search['accumulated_affiliation']);
    //         }

    //         if (isset($search['province']) && $search['province'] != "") {

    //             $users = $users->whereHas('offices', function ($q) use ($search) {
    //                 $q->where('province', $search['province']);
    //             });
    //         }
    //         if (isset($search['canton']) && $search['canton'] != "") {
    //             $users = $users->whereHas('offices', function ($q) use ($search) {
    //                 $q->where('province', $search['province'])
    //                     ->where('canton', $search['canton']);
    //             });
    //         }
    //         if (isset($search['district']) && $search['district'] != "") {
    //             $users = $users->whereHas('offices', function ($q) use ($search) {
    //                 $q->where('province', $search['province'])
    //                     ->where('canton', $search['canton'])
    //                     ->where('district', $search['district']);
    //             });
    //         }

    //         if (isset($search['date']) && $search['date'] != "") {

    //             $users = $users->withAndWhereHas('schedules', function ($q) use ($search) {

    //                 return $q->whereDate('date', $search['date']);
    //             })->withCount(['schedules' => function ($q) use ($search) {
    //                 $q->whereDate('date', $search['date']);
    //             }])->orderBy('schedules_count', 'DESC');
    //         } else {

    //             $users = $users->with(['schedules' => function ($q) {
    //                 $q->whereDate('date', '>=', Carbon::now()->startOfDay()->toDateTimeString())
    //                     ->take(30)->orderBy('date', 'ASC');
    //             }])->withCount(['schedules' => function ($q) {
    //                 $q->whereDate('date', '>=', Carbon::now()->startOfDay()->toDateTimeString())
    //                     ->take(30);
    //             }])->orderBy('schedules_count', 'DESC');
    //         }

    //         if (isset($search['speciality']) && $search['speciality'] != "") {
    //             $users = $users->whereHas('specialities', function ($q) use ($search) {
    //                 $q->where('specialities.id', $search['speciality']);
    //             });
    //         }

    //         if (isset($search['general']) && $search['general'] != "") {

    //             $users = $users->doesntHave('specialities');
    //         }


    //         if (isset($search['order']) && $search['order'] != "") {
    //             $order = $search['order'];
    //         }
    //         if (isset($search['dir']) && $search['dir'] != "") {
    //             $dir = $search['dir'];
    //         }


    //         $users = $users->with(['offices' => function ($q) {
    //             $q->where('verified', 1);
    //         }]);

    //         return $users->with('specialities')->orderBy('users.' . $order, $dir)->paginate($this->limit);
    //     }
    // }

    /**
     * Find all the users for the admin panel
     * @param  null  $search
     * @return mixed
     * @internal param $username
     */
    public function findAllByOffice($office_id, $search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        $office = Office::findOrfail($office_id);


        if (isset($search['q']) && trim($search['q'])) {
            $medics = $office->users()->whereHas('roles', function ($q) {
                $q->where('name', 'medico')
                    ->orWhere('name', 'esteticista');
            })->Search($search['q']);
        } else {
            $medics = $office->users()->whereHas('roles', function ($q) {
                $q->where('name', 'medico')
                    ->orWhere('name', 'esteticista');
            });
        }

        if (isset($search['hasAppointments']) && $search['hasAppointments'] != '' && isset($search['date']) && $search['date']) {
            $medics = $medics->with([
                'appointments' => function ($query) use ($search) {
                    $query->whereDate('date', $search['date']);
                }
            ]);
        }
        if (isset($search['hasSchedules']) && $search['hasSchedules'] != '' && isset($search['date']) && $search['date']) {
            $medics = $medics->with([
                'schedules' => function ($query) use ($search) {
                    $query->whereDate('date', $search['date']);
                }
            ]);
        }


        return $medics->with('offices', 'specialities')->orderBy('users.'.$order, $dir)->paginate($this->limit);
    }

    /**
     * Find all the users for the admin panel
     * @param  null  $search
     * @return mixed
     * @internal param $username
     */
    public function findAllByOffices($office_ids, $search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        //$office = Office::findOrfail($office_id);

        $medic_ids = \DB::table('office_user')->whereIn('office_id', $office_ids)->pluck('user_id');


        if (isset($search['q']) && trim($search['q'])) {

            $medics = User::whereIn('users.id', $medic_ids)->whereHas('roles', function ($q) {
                $q->where('name', 'medico')
                    ->orWhere('name', 'esteticista');
            })->Search($search['q']);
        } else {

            $medics = User::whereIn('users.id', $medic_ids)->whereHas('roles', function ($q) {
                $q->where('name', 'medico')
                    ->orWhere('name', 'esteticista');
            });
        }


        return $medics->with('offices', 'specialities')->orderBy('users.'.$order, $dir)->paginate($this->limit);
    }

    /**
     * Find all the users for the admin panel
     * @param  null  $search
     * @return mixed
     * @internal param $username
     */
    public function findAllByOfficeWithoutPaginate($office_id, $search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        $office = Office::findOrfail($office_id);


        if (isset($search['q']) && trim($search['q'])) {
            $medics = $office->users()->whereHas('roles', function ($q) {
                $q->where('name', 'medico')
                    ->orWhere('name', 'esteticista');
            })->Search($search['q']);
        } else {
            $medics = $office->users()->whereHas('roles', function ($q) {
                $q->where('name', 'medico')
                    ->orWhere('name', 'esteticista');
            });
        }
        if (isset($search['hasAppointments']) && $search['hasAppointments'] != '' && isset($search['date']) && $search['date']) {
            $medics = $medics->whereHas('appointments', function ($query) use ($search) {
                $query->whereDate('date', $search['date']);
            });
        }


        return $medics->with('offices', 'specialities')->orderBy('users.'.$order, $dir)->get();
    }

    /**
     * Find all the users for the admin panel
     * @param  null  $search
     * @return mixed
     * @internal param $username
     */
    public function findAllWithoutPaginate($search = null)
    {
        $order = 'created_at';
        $dir = 'desc';


        if (isset($search['clinic']) && $search['clinic'] != "") {
            $medics = Office::find($search['clinic'])->users();
        } else {

            $medics = $this->model;
        }


        if (isset($search['q']) && trim($search['q'])) {
            $medics = $medics->whereHas('roles', function ($q) {
                $q->where('name', 'medico')
                    ->orWhere('name', 'esteticista');
            })->Search($search['q']);
        } else {
            $medics = $medics->whereHas('roles', function ($q) {
                $q->where('name', 'medico')
                    ->orWhere('name', 'esteticista');
            });
        }


        return $medics->orderBy('users.'.$order, $dir)->get();
    }

    /**
     * Get all the appointments for the admin panel
     * @param $search
     * @return mixed
     */
    public function reportsStatistics($search)
    {

        $order = 'created_at';
        $dir = 'desc';


        $medics = $this->model->whereHas('roles', function ($query) use ($search) {
            $query->where('name', 'medico')
                ->orWhere('name', 'esteticista');
        });

        $appointments = \DB::table('appointments');


        if (isset($search['date1']) && $search['date1'] != "") {


            $date1 = new Carbon($search['date1']);
            $date2 = (isset($search['date2']) && $search['date2'] != "") ? $search['date2'] : $search['date1'];
            $date2 = new Carbon($date2);


            $appointments = $appointments->where([
                ['appointments.date', '>=', $date1],
                ['appointments.date', '<=', $date2->endOfDay()]
            ]);
        }

        $appointments = $appointments->selectRaw('status, count(*) items')
            ->groupBy('status')
            ->orderBy('status', 'DESC')
            ->get()
            ->toArray();

        $medics = $medics->selectRaw('active, count(*) items')
            ->groupBy('active')
            ->orderBy('active', 'DESC')
            ->get()
            ->toArray();
        $statistics = [
            'medics' => $medics,
            'appointments' => $appointments
        ];
        //dd($statistics);


        return $statistics;
    }

    /**
     * Get all the appointments for the admin panel
     * @param $search
     * @return mixed
     */
    public function reportsStatisticsByMedic($search)
    {

        $order = 'created_at';
        $dir = 'desc';


        $medics = $this->model;

        //$appointments = \DB::table('appointments');

        if (isset($search['medic']) && $search['medic'] != "") {
            $medic = $medics->where('id', $search['medic'])->first();
            $appointments = $medic->appointments();
        }


        if (isset($search['date1']) && $search['date1'] != "") {


            $date1 = new Carbon($search['date1']);
            $date2 = (isset($search['date2']) && $search['date2'] != "") ? $search['date2'] : $search['date1'];
            $date2 = new Carbon($date2);


            $appointments = $appointments->where([
                ['appointments.date', '>=', $date1],
                ['appointments.date', '<=', $date2->endOfDay()]
            ]);
        }

        $appointments = $appointments->selectRaw('status, count(*) items')
            ->groupBy('status')
            ->orderBy('status', 'DESC')
            ->get()
            ->toArray();


        $statistics = [

            'appointments' => $appointments
        ];


        return $statistics;
    }

    /**
     * Get all the appointments for the admin panel
     * @param $search
     * @return mixed
     */
    public function reportsStatisticsReviews($search)
    {

        $order = 'created_at';
        $dir = 'desc';


        $total_rating_service_cache = 0;
        $total_rating_service_count = 0;

        if (isset($search['medic']) && $search['medic'] != "") {

            $medic = User::find($search['medic']);


            $total_rating_service_cache = $medic->rating_service_cache;

            $total_rating_service_count = $medic->rating_service_count;

            $total_rating_medic_cache = $medic->rating_medic_cache;

            $total_rating_medic_count = $medic->rating_medic_count;
        }


        $statisticsReview = [
            'rating_service_cache' => $total_rating_service_cache,
            'rating_service_count' => $total_rating_service_count,
            'rating_medic_cache' => $total_rating_medic_cache,
            'rating_medic_count' => $total_rating_medic_count,

        ];

        return $statisticsReview;
    }
}
