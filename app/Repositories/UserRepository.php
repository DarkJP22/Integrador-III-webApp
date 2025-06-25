<?php namespace App\Repositories;

use App\Role;
use App\Setting;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class UserRepository extends DbRepository{


    /**
     * Construct
     * @param User $model
     */
    function __construct(User $model)
    {
        $this->model = $model;
        $this->limit = 10;
    }

    /**
     * save a user
     * @param $data
     */
    public function store($data): User
    {
        
        $data = $this->prepareData($data);
      
        $user = $this->model->create($data);
        
        $role = (isset($data['role'])) ? $data['role'] : Role::whereName('medico')->first();
        
        $user->assignRole($role);
        
        $user->update([
            'current_role_id' => $role instanceof Role ? $role->id : $role
        ]);
        
 
        
        if(isset($data['speciality']))
            $user->assignSpeciality($data['speciality']);

        //$user->createSettings();
        

        return $user;
    }

    private function prepareData($data)
    {
        if(! isset($data['freeDays']) )
        {
            $data['freeDays'] = [];
        }
        if(! isset($data['speciality']) )
        {
            $data['speciality'] = [];
        }

        $data['accumulated_affiliation'] = $data['accumulated_affiliation'] ?? 0;

        if(empty($data['password']))
           return $data = Arr::except($data, array('password'));

        $data['password'] = bcrypt($data['password']);
        $data['changed_password'] = $data['changed_password'] ?? now();


        return $data;
    }

    public function update($id, $data): User
    {
        $user = $this->model->findOrFail($id);
        $data = $this->prepareData($data);

        $user->fill($data);



        if ($user->hasRole('medico')) {

            if (isset($data['speciality'])){
                $user->assignSpeciality($data['speciality']);
            }




            // if(isset($data['minTime'])){
            //     $data['minTime'] = $data['minTime']; //. ':00';
            // }
            // if(isset($data['maxTime'])){
            //     $data['maxTime'] = $data['maxTime']; //. ':00';
            // }

//            $settings = Setting::where('user_id', $user->id)->first();
//
//            if ($settings) {
//                    $settings->fill($data);
//                    $settings->save();
//                }

        }

        $user->save();


        return $user;
    }

    public function update_active($id, $state) : User
    {

        $user = $this->model->findOrFail($id);


        if($user->hasRole('clinica'))
        {


            foreach ($user->offices as $office) {

                if($office->administrators()->count() < 2){
                     $office->active = $state;
                     $office->save();
                 }
            }
        }

        $user->active = $state;
        $user->save();

        return $user;
    }


    //List of users for the modal view of user.

    /**
     * Find all the users for the admin panel
     * @internal param $username
     * @param null $search
     * @return mixed
     */
    public function findAll($search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        if (!$search) return $this->model->with('offices','settings','roles','specialities', 'subscription.plan')->paginate($this->limit);

        if (isset($search['q']) && trim($search['q']))
        {
            $users = $this->model->Search($search['q']);
        } else
        {
            $users = $this->model;
        }

        if (isset($search['active']) && $search['active'] != "")
        {

            $users = $users->where('active', '=', $search['active']);
        }
        if (isset($search['role']) && $search['role'] != "")
        {
            $users = $users->whereHas('roles', function ($query) use ($search) {
                        $query->where('name',  $search['role']);
                    });

        }

        if (isset($search['order']) && $search['order'] != "")
        {
            $order = $search['order'];
        }
        if (isset($search['dir']) && $search['dir'] != "")
        {
            $dir = $search['dir'];
        }


        return $users->with('offices','settings','roles','specialities', 'subscription.plan')->orderBy('users.'.$order , $dir)->paginate($this->limit);

    }

    public function list_users($value = null, $search = null)
    {

        if ($search && $value != "")
            $users = ($value) ? $this->model->where('id', '<>', $value)->search($search)->paginate(8) : $this->model->paginate(8);
        else if ($value != "")
            $users = ($value) ? $this->model->where('id', '<>', $value)->paginate(8) : $this->model->paginate(8);
        else
            $users = $this->model->search($search)->paginate(8);

        return $users;
    }

     /**
     * Delete user
     * @param $id
     * @param $data
     * @return \Illuminate\Support\Collection|static
     */
    public function delete($id)
    {

        $user = $this->model->findOrFail($id);


        if($user->hasRole('clinica'))
        {


            foreach ($user->offices as $office) {

                if($office->administrators()->count() < 2){
                     $office->active = 0;
                     $office->save();
                 }
            }
        }


        \DB::table('office_user')->where('user_id', $user->id)->delete();
        \DB::table('role_user')->where('user_id', $user->id)->delete();


        $user = $user->delete();

        return $user;
    }


}