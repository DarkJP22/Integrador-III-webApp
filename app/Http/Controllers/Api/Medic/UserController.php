<?php

namespace App\Http\Controllers\Api\Medic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    function __construct(protected UserRepository $userRepo)
    {
        $this->middleware('auth');
      
    }
    /**
     * Actualizar informacion basica del medico
     */
    public function edit()
    {
        $user = request()->user();

        return $user->load('roles', 'settings', 'specialities', 'subscription.plan')->loadCount('offices')
            ->loadCount([
                'offices as offices_gps_count' => function ($query) {
                    $query->where('utiliza_agenda_gps', 1);
                }
            ]);

    }

    /**
     * Actualizar informacion basica del medico
     */
    public function update()
    {
        $user = request()->user();

        $this->validate(request(), [
            'name' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'], //Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'ide' => ['required', Rule::unique('users')->ignore($user->id)],
        ]);

        $data = request()->all();

        $data = Arr::except($data, array('avatar_path'));

        $user = $this->userRepo->update($user->id, $data);


        return [
            'user' => $user->load('roles', 'settings', 'specialities', 'subscription.plan')->loadCount('offices')
                ->loadCount([
                    'offices as offices_gps_count' => function ($query) {
                        $query->where('utiliza_agenda_gps', 1);
                    }
                ]),
            'user_settings' => $user->getAllSettings()
        ];


    }
    /**
     * Actualizar informacion basica del medico
     */
    public function updatePushToken()
    {
        $user = request()->user();

        $user->push_token = request('push_token'); // = $this->userRepo->update($user->id, request()->all());
        $user->save();
         //$user = $this->userRepo->update($user->id, request()->all());



        return $user->load('roles', 'settings', 'specialities', 'subscription.plan')->loadCount('offices')
            ->loadCount([
                'offices as offices_gps_count' => function ($query) {
                    $query->where('utiliza_agenda_gps', 1);
                }
            ]);

    }

}
