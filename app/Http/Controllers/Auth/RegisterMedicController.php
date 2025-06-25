<?php

namespace App\Http\Controllers\Auth;

use App\Actions\CreateUser;
use App\Enums\TypeOfHealthProfessional;
use App\Notifications\WelcomeUser;
use App\Role;
use App\Setting;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Repositories\UserRepository;
use App\Speciality;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewMedic;
use App\Plan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegisterMedicController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/medic/offices';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->middleware('guest');
        $this->userRepo = $userRepo;
        $this->administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $specialities = Speciality::all();
        $plans = Plan::where('for_medic', 1)->get();
        $typesOfHealthProfessional = TypeOfHealthProfessional::options();


        return view('auth.medics.register', compact('specialities', 'plans', 'typesOfHealthProfessional'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'medic_code' => 'required|unique:users',
            'ide' => 'required|unique:users|digits_between:9,12'

        ]);
    }


    protected function registerMedic(CreateUser $createUser)
    {

        $user = DB::transaction(function () use ($createUser) {
            $data = request()->all();
            $user = $createUser($data, Role::where('name', 'medico')->first(), [
                'medic_code' => 'required|unique:users',
                'speciality' => ['sometimes', 'array'],
                'plan_id' => ['required', 'exists:plans,id'],
                'type_of_health_professional' => ['required', 'string'],
                //'general_cost_appointment' => ['required', 'numeric']
            ]);

            $user->fill(['active' => 0])->save();

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

            return $user;
        });

        Auth::login($user);

        try {

            Notification::send($this->administrators, new NewMedic($user));
            $user->notify(new WelcomeUser($user));

        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
        {
            \Log::error($e->getMessage());
        }


        return Redirect('/');


    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        //$data['active'] = 0; // los medicos estan inactivos por defecto para revision
        $data['api_token'] = Str::random(100);


        $user = $this->userRepo->store($data);


        Auth::login($user);


        return $user;


    }
}
