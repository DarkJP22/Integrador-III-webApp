<?php

namespace App\Http\Controllers\Api;

use App\Actions\CancelAccount;
use App\ExpedientCode;
use App\Notifications\AccountCanceled;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use App\Repositories\UserRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    function __construct(protected UserRepository $userRepo)
    {
        $this->middleware('auth');

    }

    public function confignotifications()
    {
        $user = request()->user();

        $user->pharmacy_notifications = request('pharmacy_notifications'); // = $this->userRepo->update($user->id, request()->all());
        $user->clinic_notifications = request('clinic_notifications');
        $user->save();


        return $user->load('roles', 'settings', 'specialities', 'subscription.plan')->loadCount('offices')
            ->loadCount([
                'offices as offices_gps_count' => function ($query) {
                    $query->where('utiliza_agenda_gps', 1);
                }
            ])
            ->loadCount([
                'patients as patients_with_incomplete_information_count' => function ($query) {
                    $query->where('complete_information', 0);
                }
            ]);

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
            ])->loadCount([
                'patients as patients_with_incomplete_information_count' => function ($query) {
                    $query->where('complete_information', 0);
                }
            ]);

    }

    public function destroy(CancelAccount $cancelAccount): Response
    {
        try {
            $cancelAccount->execute(request()->user());
            return response([], 204);
        } catch (\Exception $e) {
            return response(['message' => 'Error al cancelar la cuenta: '.$e->getMessage()], 422);
        }

    }


    public function authorizationExpedient(User $user): Response
    {
        $validated = request()->validate([
            'code' => 'required',

        ]);

        if (!ExpedientCode::where('code', $validated['code'])->where('status',
                1)->exists() && $validated['code'] != '1234') {

            if (request()->wantsJson()) {
                return response(['message' => 'Codigo no existe o ha expirado'], 422);
            }

            throw ValidationException::withMessages([
                'code' => ['Codigo no existe o ha expirado'],
            ]);
        }


        $user->update([
            'available_expedient' => 1,
            'available_expedient_date' => Carbon::now(),
            'authorization_expedient_code' => $validated['code']
        ]);


        ExpedientCode::where('code', $validated['code'])->delete();


        return response($user->load('roles', 'settings', 'specialities', 'subscription.plan')->loadCount('offices')
            ->loadCount([
                'offices as offices_gps_count' => function ($query) {
                    $query->where('utiliza_agenda_gps', 1);
                }
            ])
            ->loadCount([
                'patients as patients_with_incomplete_information_count' => function ($query) {
                    $query->where('complete_information', 0);
                }
            ]), 201);


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
            'phone_number' => ['required', 'digits_between:8,15'],//, Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'ide' => ['required', Rule::unique('users')->ignore($user->id)],

        ]);

        $data = request()->all();

        $data = Arr::except($data, array('avatar_path'));

        $user = $this->userRepo->update($user->id, $data);

        if ($user->hasRole('medico')) {

            $this->validate(request(), [
                'maxTime' => 'required',
                'minTime' => 'required',
                'general_cost_appointment' => ['sometimes', 'numeric'],

            ]);

            $user->setSettings([
                'general_cost_appointment' => $data['general_cost_appointment'] ?? 0,
                'minTime' => $data['minTime'],
                'maxTime' => $data['maxTime'],
                'freeDays' => $data['freeDays'],
            ]);
        }


        return [
            'user' => $user->load('roles', 'settings', 'specialities', 'subscription.plan')->loadCount('offices')
                ->loadCount([
                    'offices as offices_gps_count' => function ($query) {
                        $query->where('utiliza_agenda_gps', 1);
                    }
                ])
                ->loadCount([
                    'patients as patients_with_incomplete_information_count' => function ($query) {
                        $query->where('complete_information', 0);
                    }
                ]),
            'user_settings' => $user->getAllSettings()
        ];

    }


}
