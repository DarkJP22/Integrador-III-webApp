<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Role;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Validator;
use App\PatientInvitation;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class RegisterUserInvitationController extends Controller
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
    protected $redirectTo = '/';

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
    public function showRegistrationForm($patientId, $code)
    {
        $code = PatientInvitation::where('code', $code)->where('patient_id', $patientId)->where('created_at', '>', Carbon::now()->subHours(24))->first();

        if (!$code || !$code->patient) {

            if (request()->wantsJson()) {
                return response(['message' => 'La invitacion no existe o ha expirado'], 422);
            }

            // throw ValidationException::withMessages([
            //     'username' => ['La invitacion no existe o ha expirado'],
            // ]);
            flash('La invitación no existe o ha expirado', 'danger');

            return redirect('/register');

           // return view('auth.users.register');

        }

        return view('auth.users.invitation',[
            "patientId" => $patientId,
            "patient" => $code->patient,
            "code" => $code->code
        ]);
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
            'phone_country_code' => 'required',
            'phone_number' => 'required|digits_between:8,15|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
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
       
        $code = PatientInvitation::where('code', request('code'))->where('patient_id', request('patient'))->where('created_at', '>', Carbon::now()->subHours(24))->first();

        if (!$code || !$code->patient) {

            if (request()->wantsJson()) {
                return response(['message' => 'La invitacion no existe o ha expirado'], 422);
            }

            flash('La invitación no existe o ha expirado', 'danger');

            return back();


        }

        $data['role'] = Role::whereName('paciente')->first();
        $data['api_token'] = Str::random(100);
        
        $user = $this->userRepo->store($data);

        if($code->patient){

            $user->patients()->save($code->patient, ['authorization' => 1]);
        }

        return $user;


    }
}
