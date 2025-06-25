<?php

namespace App\Http\Controllers\Api\Medic;


use App\Actions\CreateUser;
use App\Notifications\NewMedic;
use App\Notifications\WelcomeUser;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plan;
use App\RegisterAuthorizationCode;
use App\Repositories\UserRepository;
use App\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public $administrators;

    function __construct(public UserRepository $userRepo)
    {
        $this->administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();
    }

    public function register(Request $request, CreateUser $createUser)
    {
        $user = \DB::transaction(function () use ($request, $createUser) {
            $data = $request->all();

            $user = $createUser([
                ...$data
            ], Role::where('name', 'medico')->first(),
                [
                    'tipo_identificacion' => ['required'],
                    'password' => 'required|min:6|confirmed',
                    'medic_code' => ['required', 'unique:users,medic_code'],
                    'authorization_code' => ['nullable'],
                    'accumulated_affiliation' => ['nullable'],
                    'speciality' => ['sometimes', 'array'],
                    'plan_id' => ['required', 'exists:plans,id'],
                    'type_of_health_professional' => ['required', 'string'],
                    'photos' => ['required', 'array'],
                    'photos.*' => ['required', 'image', 'max:10240'], // 10MB Max
                    'email' => ['required', 'email', 'unique:users,email'],
                    //'general_cost_appointment' => ['sometimes', 'numeric'],
                ]
            );

            $plan = Plan::findOrFail(request('plan_id'));

            if (!$user->subscription()->first()) {

                $user->subscription()->create([
                    'plan_id' => $plan->id,
                    'cost' => $plan->cost,
                    'quantity' => $plan->quantity,
                    'ends_at' => Carbon::now()->startOfMonth()->addMonths($plan->quantity),
                    'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free'
                ]);

            }

            $user->setSettings([
                'general_cost_appointment' => $data['general_cost_appointment'] ?? 0,
                'slotDuration' => '00:30:00',
                'minTime' => '06:00:00',
                'maxTime' => '18:00:00',
                'freeDays' => '[]',
            ]);

            if ($data['authorization_code'] ?? false) {
                RegisterAuthorizationCode::where('code', $data['authorization_code'])->delete();
            }

            foreach (($data['photos'] ?? []) as $photo) {
                $this->uploadTitles($user, $photo);
            }

            $user->update([
                'active' => 0,
            ]);

            return $user;
        });

        Notification::send($this->administrators, new NewMedic($user));
        $user->notify(new WelcomeUser($user));

        $data = [
            'access_token' => $user->api_token,
            'user' => $user->load('roles', 'settings', 'specialities', 'subscription.plan')->loadCount('offices')
                ->loadCount([
                    'offices as offices_gps_count' => function ($query) {
                        $query->where('utiliza_agenda_gps', 1);
                    }
                ]),
            'user_settings' => $user->getAllSettings(),
        ];

        return $data;
    }

    public function uploadTitles(User $user, UploadedFile $photo)
    {
        tap(null, function ($previous) use ($photo, $user) {
            $path = $photo->store('users/'.$user->id.'/titles-photos', 's3');

            if ($previous) {
                Storage::disk('s3')->delete($previous);
            }
        });
    }

    public function authorization()
    {
        $data = $this->validate(request(), [
            'code' => ['required']
        ]);

        $code = RegisterAuthorizationCode::where('code', $data['code'])->where('created_at', '>',
            Carbon::now()->subDay())->first();
        if (!$code) {
            throw ValidationException::withMessages([
                'code' => ['Codigo no existe o ha expirado'],
            ]);
        }

        return 'ok';
    }
}
