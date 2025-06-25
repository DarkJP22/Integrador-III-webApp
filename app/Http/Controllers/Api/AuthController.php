<?php

namespace App\Http\Controllers\Api;

use App\Accumulated;
use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Patient;
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use App\User;
use App\ResetCode;
use App\Role;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

    function __construct(protected UserRepository $userRepo, protected PatientRepository $patientRepo)
    {
    }

    public function token(Request $request): array
    {

        $emailOrIde = 'email';

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',

        ]);

        if ($validator->fails()) {
            $emailOrIde = 'ide';

            $validatorIde = Validator::make($request->all(), [
                'email' => 'required|exists:users,ide',

            ]);

            if ($validatorIde->fails()) {
                throw ValidationException::withMessages([
                    'email' => ['Usuario no existe'],
                ]);
            }
        } else {

            $validatorEmail = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',

            ]);
            if ($validatorEmail->fails()) {
                throw ValidationException::withMessages([
                    'email' => ['Usuario no existe'],
                ]);
            }
        }


        if (\Auth::attempt([
            $emailOrIde => $request->input('email'), 'password' => $request->input('password'), 'active' => 1
        ])) {
            // Authentication passed...
            $user = \Auth::user();

            if ($request->input('push_token')) {
                $user->push_token = $request->input('push_token');
                $user->save();
            }
            if (!$user->api_token) {

                $user->api_token = Str::random(50);
                $user->save();
            }

            $data = [
                'access_token' => $user->api_token,
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
                'user_settings' => $user->getAllSettings(),
            ];

            if ($user->hasRole('paciente')) {
                $data['accumulated'] = $this->getAccumulatedAccount($user);
            }

            \Auth::logout();

            return $data;
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        $data = [
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
            'user_settings' => $user->getAllSettings(),
        ];

        if ($user->hasRole('paciente')) {
            $data['accumulated'] = $this->getAccumulatedAccount($user);
        }

        return $data;
    }

    public function getAccumulatedAccount(User $user)
    {
        $holder = $user->patients->first();
        $accumulated = Accumulated::firstOrCreate(
            ['user_id' => $user->id],
            [
                'patient_id' => $holder?->id,
                'active_at' => now(),
                'acumulado' => 0
            ]
        );

        $accumulated->patients()->sync($user->patients->pluck('id'));

        return $accumulated->load('user', 'holder', 'patients');
    }

    public function register(Request $request, CreateUser $createUser, CreatePatient $createPatient)
    {

        $user = \DB::transaction(function () use ($createUser, $createPatient) {
            $data = request()->all();

            if (!($patient = Patient::where('ide', $data['ide'])->first())) {
                $patient = $createPatient([
                    ...$data,
                    'first_name' => $data['name'],
                    'complete_information' => 0
                ], [
                    'tipo_identificacion' => ['required']
                ]);

            }

            $user = $createUser([
                ...$data,
            ], Role::where('name', 'paciente')->first(),
                [
                    'password' => 'required|min:6|confirmed',
                ]
            );

           $patient->user()->whereHas('roles', function ($query) {
                $query->where('name', 'paciente');
            })->each(function ($parentUser) use ($patient) {
               $parentUser->patients()->detach($patient->id);
            });

            $user->patients()->save($patient, ['authorization' => 1]);
            $patient->fill(['created_by' => $user->id])->save();
            $user->fill(['changed_password' => now(), 'active' => 1])->save();

            return $user;
        });

        $data = [
            'access_token' => $user->api_token,
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
            'user_settings' => $user->getAllSettings(),
        ];

        if ($user->hasRole('paciente')) {
            $data['accumulated'] = $this->getAccumulatedAccount($user);
        }

        return $data;
    }

    public function updateRegister(Request $request, User $user)
    {

        $this->validate(request(), [
            'name' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'], //, Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'ide' => ['nullable', Rule::unique('users')->ignore($user->id)],
            'password' => 'required|min:6|confirmed',
        ]);

        $data = request()->all();

        $data['push_token'] = $request->input('push_token');

        $user = $this->userRepo->update($user->id, $data);


        $data = [
            'access_token' => $user->api_token,
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
            'user_settings' => $user->getAllSettings(),
        ];

        if ($user->hasRole('paciente')) {
            $data['accumulated'] = $this->getAccumulatedAccount($user);
        }

        return $data;
    }

    public function createPatientFrom($user)
    {
        $validatedData = [
            'ide' => $user->ide,
            'first_name' => $user->name,
            'phone_country_code' => $user->phone_country_code,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'created_by' => $user->id,
            'gender' => '',
            'birth_date' => '',
            'province' => '5',
            'authorization' => 1

        ];

        return $this->patientRepo->store($validatedData, $user);
    }

    public function sendResetCodePhone(Request $request)
    {
        $this->validate($request, [
            'email' => 'nullable|exists:users',
            'phone_country_code' => 'required',
            'phone_number' => 'required|exists:users'
        ]);
        $user = User::byPhone($request->phone_number, $request->phone_country_code, $request->email);
        $code = ResetCode::generateFor($user);

        $status = $code->send();
        if ($status['status'] == 0) {
            return response(['message' => 'Error al enviar codigo'], 500);
        } //$this->respondWithError($status['message']);

        return response(['message' => 'Codigo Enviado correctamente'], 200);
    }

    public function sendResetCodeEmail(Request $request)
    {
        $emailOrPhone = 'email';

        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',

        ]);

        if ($validator->fails()) {
            $emailOrPhone = 'phone_number';

            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:users,phone_number',

            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'email' => [trans('auth.failed')],
                ]);
            }
        }

        if ($emailOrPhone == 'phone_number') {
            $user = User::byPhone($request->email, $request->phone_country_code ?? '+506');
        } else {
            $user = User::byEmail($request->email);
        }


        $code = ResetCode::generateFor($user);

        $status = $code->send();
        if ($status['status'] == 0) {
            return response(['message' => 'Error al enviar codigo'], 500);
        } //$this->respondWithError($status['message']);

        return response(['message' => 'Codigo Enviado correctamente'], 200);
    }

    public function sendResetCodeIde(Request $request)
    {
        $this->validate($request, [
            'ide' => 'required|exists:users,ide',
        ]);

        $user = User::where('ide', $request->ide)->first();

        $code = ResetCode::generateFor($user);

        $status = $code->send();
        if ($status['status'] == 0) {
            return response(['message' => 'Error al enviar codigo'], 500);
        } //$this->respondWithError($status['message']);

        return response(['message' => 'Codigo Enviado correctamente'], 200);
    }

    public function newPassword(Request $request)
    {
        $this->validate($request, [
            //'email' => 'required|exists:users',
            'password' => 'required|confirmed',
            'code' => 'required|exists:reset_codes'

        ]);
        $code = ResetCode::where('code', $request->code)->where('created_at', '>', Carbon::now()->subHours(2))->first();
        if (!$code) {
            throw ValidationException::withMessages([
                'code' => ['Codigo no existe o ha expirado'],
            ]);
        }

        $user = $code->user;
        $user->password = bcrypt($request->password);
        $user->save();
        \Auth::login($user);
        \DB::table('reset_codes')->where('user_id', $user->id)->delete();
        $data = [
            'access_token' => $user->api_token,
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
            'user_settings' => $user->getAllSettings(),
        ];

        if ($user->hasRole('paciente')) {
            $data['accumulated'] = $this->getAccumulatedAccount($user);
        }

        \Auth::logout();

        return $data;
    }

    public function getUserByIde(string $ide)
    {
        if (!$ide) {
            throw ValidationException::withMessages([
                'ide' => ['Cédula requerida o no válida'],
            ]);
        }

        return User::where('ide', $ide)->whereHas('roles', function ($query) {
            $query->where('name', 'paciente');
        })->sole();
    }
}
