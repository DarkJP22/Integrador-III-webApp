<?php

namespace App\Repositories;

use App\Medicine;
use App\Patient;
use App\User;
use Carbon\Carbon;
use App\Role;
use App\Repositories\UserRepository;
use App\Notifications\NewPatient;
use App\Discount;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PatientRepository extends DbRepository
{
    /**
     * Construct
     * @param User $model
     */
    public function __construct(Patient $model, UserRepository $userRepo)
    {
        $this->model = $model;
        $this->limit = 10;
        $this->userRepo = $userRepo;
    }

    /**
     * save a patient
     * @param $data
     */
    public function store($data, $user = null)
    {
        $data = $this->prepareData($data);

        $patient = $this->model->create($data);

        $patient->createHistory();

        $patient = ($user) ? $user->patients()->save($patient, ['authorization' => $data['authorization']]) : auth()->user()->patients()->save($patient, ['authorization' => $data['authorization']]);

        if(isset($data['discount_id'])){
            $patient->discounts()->sync([$data['discount_id']]);
        }

        return $patient;
    }

    public function createUser($patient, $data)
    {
        $user_patient = null;
         //validamos que en users no hay email que va a registrase como paciente
        if ( (isset($data['email']) && $data['email']) || (isset($data['ide']) && $data['ide']) ) {
            request()->validate([
                'phone_number' => 'required',
                'email' => 'nullable|email|max:255|unique:users',
                'ide' => 'nullable|unique:users',
            ]);

            $data['password'] = (isset($data['password']) && $data['password']) ? $data['password'] : $data['phone_number'];

            $data['name'] = $data['first_name'];
            $data['role'] = Role::whereName('paciente')->first();
            $data['api_token'] = Str::random(50);
            

            $user = $this->userRepo->store($data);
            $user_patient = $user->patients()->save($patient, ['authorization' => 1]);

            try {

                $user->notify(new NewPatient($user));

            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            {
                \Log::error($e->getMessage());
            }    

            
        } 
        // } else {
        //     request()->validate([
        //         'phone_number' => 'required|unique:users',
        //     ]);

        //     $data['password'] = (isset($data['password']) && $data['password']) ? $data['password'] : $data['phone_number'];

        //     $data['name'] = $data['first_name'];
        //     $data['role'] = Role::whereName('paciente')->first();
        //     $data['api_token'] = Str::random(50);

        //     $user = $this->userRepo->store($data);
        //     $user_patient = $user->patients()->save($patient);
        // }

        return $user_patient;
    }

    /**
     * Update a patient
     * @param $id
     * @param $data
     * @return \Illuminate\Support\Collection|static
     */
    public function update($id, $data)
    {
        $patient = $this->model->findOrFail($id);
        $data = $this->prepareData($data);

        $patient->fill($data);
        $patient->save();

        //descuento empresarial///////////////////////

        if (auth()->user()->isAssistant()) {
            $assistantUser = \DB::table('assistants_users')->where('assistant_id', auth()->user()->id)->first();

            $user = User::find($assistantUser->user_id);

        } else {
            $user = auth()->user();
        }

        $discounts = $patient->discounts()->where('user_id', $user->id)->get();

        $patient->discounts()->detach($discounts->pluck('id')->all());
        
        if(isset($data['discount_id']))
        {

            $patient->discounts()->attach($data['discount_id']);
          
        }
        /////////////////////////////////////
        
        return $patient;
    }

    /**
     * Update a history patient
     * @param $id
     * @param $data
     * @return \Illuminate\Support\Collection|static
     */
    public function updateHistory($id, $data)
    {
        $patient = $this->model->findOrFail($id);

        $history = $patient->history;

        $history->histories = $data;

        $history->save();

        return $history;
    }

    /**
     * Add medicine to user
     * @param $id
     * @param $data
     * @return \Illuminate\Support\Collection|static
     */
    public function addMedicine($id, $data)
    {
        $patient = $this->model->findOrFail($id);

        $medicine = $patient->medicines()->create($data);

        return $medicine;
    }

    /**
     * Delete medicine to user
     * @param $id
     * @param $data
     * @return \Illuminate\Support\Collection|static
     */
    public function deleteMedicine($id)
    {
        $medicine = Medicine::findOrFail($id)->delete();

        return $medicine;
    }

    /**
     * Delete patient
     * @param $id
     * @param $data
     * @return \Illuminate\Support\Collection|static
     */
    public function delete($id)
    {
     

        $patient = $this->model->findOrFail($id);


        if (!$patient->appointments->count()) {
            if ($patient->created_by == auth()->id()) {
                return $patient = $patient->delete();
            }

            $patient = auth()->user()->patients()->detach($id);

            return true;
        }

        return $patient;
    }

    /**
    * Find all the patients for the admin panel
    * @internal param $username
    * @param null $search
    * @return mixed
    */
    public function findAllOfClinic($clinic, $search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        //$userIds = $clinic->users()->pluck('users.id');

        $patients = $this->model;

        //$patients = $patients->Search($search);

        if ((isset($search['q']) && trim($search['q'])) || (isset($search['province']) && trim($search['province']))) {
           
            $patients = $patients->Search($search);
        } else {
            $patients = $clinic->patients();
        }

        // if (isset($search['q']) && trim($search['q'])) {
        //     $patients = $patients->Search($search['q']);
        // }
        // if (isset($search['province']) && trim($search['province'])) {
        //     $patients = $patients->where('province', $search['province']);
        // }


        // $patients = $patients->whereHas('user', function ($query) use ($userIds) {
        //     $query->whereIn('users.id', $userIds);
        // });

       
        return $patients->with('appointments','offices')->orderBy('patients.' . $order, $dir)->paginate($this->limit);
    }

    /**
    * Find all the patients for the admin panel
    * @internal param $username
    * @param null $search
    * @return mixed
    */
    public function findAllOfClinicWithoutPaginate($clinic, $search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        //$userIds = $clinic->users()->pluck('users.id');

        $patients = $this->model;

        //$patients = $patients->Search($search);

        if ((isset($search['q']) && trim($search['q'])) || (isset($search['province']) && trim($search['province']))) {
           
            $patients = $patients->Search($search);
        } else {
            $patients = $clinic->patients();

            // $patients = $patients->whereHas('user', function ($query) use ($userIds) {
            //     $query->whereIn('users.id', $userIds);
            // });
        }

        // if (isset($search['q']) && trim($search['q'])) {
        //     $patients = $patients->Search($search['q']);
        // }
        // if (isset($search['province']) && trim($search['province'])) {
        //     $patients = $patients->where('province', $search['province']);
        // }


        // $patients = $patients->whereHas('user', function ($query) use ($userIds) {
        //     $query->whereIn('users.id', $userIds);
        // });

       
        return $patients->with('appointments','offices')->orderBy('patients.' . $order, $dir)->get();
    }

    /**
    * Find all the patients for the admin panel
    * @internal param $username
    * @param null $search
    * @return mixed
    */
    public function findAllOfPharmacy($pharmacy, $search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        //$userIds = $pharmacy->users()->pluck('users.id');
        $patients = $this->model;

        // $patients = $patients->Search($search);

        // $patients = $patients->whereHas('user', function ($query) use ($userIds) {
        //          $query->whereIn('users.id', $userIds);
        //     });
           
        if ((isset($search['q']) && trim($search['q'])) || (isset($search['province']) && trim($search['province'])) || (isset($search['conditions']) && trim($search['conditions']))) {
           
            $patients = $patients->Search($search);
        } else {
            $patients = $pharmacy->patients();
            // $patients = $patients->whereHas('user', function ($query) use ($userIds) {
            //     $query->whereIn('users.id', $userIds);
            // });
        }


        // if (isset($search['q']) && trim($search['q'])) {
        //     $patients = $patients->Search($search['q']);
        // }
        // if (isset($search['province']) && trim($search['province'])) {
        //     $patients = $patients->where('province', $search['province']);
        // }


        // $patients = $patients->whereHas('user', function ($query) use ($userIds) {
        //     $query->whereIn('users.id', $userIds);
        // });

       

        return $patients->with('appointments','pharmacies')->orderBy('patients.' . $order, $dir)->paginate($this->limit);
    }

    /**
     * Find all the patients for the admin panel
     * @internal param $username
     * @param null $search
     * @return mixed
     */
    public function findAll($search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        if ((isset($search['q']) && trim($search['q'])) || (isset($search['province']) && trim($search['province']))) {
            $patients = $this->model;
        } else {
            $patients = auth()->user()->patients();
        }

        if (!$search) {
            return $patients->with('appointments','user')->paginate($this->limit);
        }

        $patients = $patients->Search($search);

        

        // if (isset($search['q']) && trim($search['q'])) {
        //     $patients = $patients->Search($search['q']);
        // }

        // if (isset($search['province']) && trim($search['province'])) {
        //     $patients = $patients->where('province', $search['province']);
        // }

        if (isset($search['order']) && $search['order'] != '') {
            $order = $search['order'];
        }
        if (isset($search['dir']) && $search['dir'] != '') {
            $dir = $search['dir'];
        }

        return $patients->with('user','appointments','emergencyContacts')->orderBy('patients.' . $order, $dir)->paginate($this->limit);
    }

    public function findById($id)
    {
        return $this->model->with('vitalSigns', 'medicines', 'history.allergies.user', 'history.pathologicals.user', 'history.nopathologicals.user', 'history.heredos.user', 'history.ginecos.user')->findOrFail($id);
    }

    /**
     * List of patients for the appointments form
     */
    public function list($search = null, $user = null)
    {
        $patients = [];

        if (!$search) {
            return $patients;
        }

        $patients = ($user) ? $user->patients()->search($search)->paginate(10) : auth()->user()->patients()->search($search)->paginate(10);

        return $patients;
    }

    /**
    * List of patients for the appointments form
    */
    public function listForClinics($search = null, $limit = 10)
    {
        $patients = [];

        if (isset($search) && $search) {
            $patients = $this->model->search($search)->paginate($limit);
        }

        return $patients;
    }

    private function prepareData($data)
    {
        $data['created_by'] = isset($data['created_by']) ? $data['created_by'] : auth()->id();
        $data['authorization'] = isset($data['authorization']) ? $data['authorization'] : 0;

        return $data;
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

        $patients = $this->model;

        if (isset($search['date1']) && $search['date1'] != '') {
            $date1 = new Carbon($search['date1']);
            $date2 = (isset($search['date2']) && $search['date2'] != '') ? $search['date2'] : $search['date1'];
            $date2 = new Carbon($date2);

            $patients = $patients->where([['patients.created_at', '>=', $date1],
                    ['patients.created_at', '<=', $date2->endOfDay()]]);
        }

        $patients = $patients->selectRaw('province, count(*) items')
                         ->groupBy('province')
                         ->orderBy('province', 'DESC')
                         ->get()
                         ->toArray();
        $statistics = [
            'patients' => $patients
        ];

        return $statistics;
    }
}
