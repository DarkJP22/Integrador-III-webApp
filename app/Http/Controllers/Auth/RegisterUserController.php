<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Role;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
use Validator;

class RegisterUserController extends Controller
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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/patients/create';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->middleware('guest');
        $this->userRepo = $userRepo;
    }

     /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {

        return view('auth.users.register');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        $v = Validator::make($data, [
            'tipo_identificacion' => 'required',
            'ide' => 'required|unique:users',
            'name' => 'required|max:255',
            'phone_country_code' => 'required',
            'phone_number' => 'required|digits_between:8,15',//|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|min:6|confirmed',

        ]);

        $v->sometimes('tipo_identificacion', 'required', function ($input) {
            return $input->ide != '';
        });

        $v->sometimes('ide', 'digits:9', function ($input) {
            return $input->tipo_identificacion == '01';
        });

        $v->sometimes('ide', 'digits:10', function ($input) {
            return $input->tipo_identificacion == '02' || $input->tipo_identificacion == '04';
        });

        $v->sometimes('ide', 'digits_between:11,12', function ($input) {
            return $input->tipo_identificacion == '03';
        });

        //$v->validate();

        return $v;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        // $this->validate(request(), [ //verificar que no este en paciente el mismo telefono y email si es q lo envia
        //         'phone_number' => 'required|digits_between:8,15|unique:patients',
        //         'email' => 'nullable|email|unique:patients'
        //     ]);

        $data['role'] = Role::whereName('paciente')->first();
        $data['api_token'] = Str::random(100);

        return $this->userRepo->store($data);


    }
}
