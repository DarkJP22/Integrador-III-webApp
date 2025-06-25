<?php

namespace App\Http\Controllers\Auth;


use App\Actions\CreateUser;
use App\Http\Controllers\Controller;
use App\Office;
use App\Repositories\UserRepository;
use App\Role;
use App\Setting;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewClinic;
use App\Plan;
use Carbon\Carbon;
use App\Repositories\OfficeRepository;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegisterClinicController extends Controller
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
    protected $redirectTo = '/clinic/agenda';

    protected $administrators;

    public function __construct(protected UserRepository $userRepo, protected OfficeRepository $officeRepo)
    {
        $this->middleware('guest')->except('showRegistrationOfficeForm', 'registerOffice','showCompletePayment', 'completePayment');

        $this->administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();
    }


    public function showRegistrationForm()
    {
        $plans = Plan::where('for_clinic', 1)->get();

        return view('auth.clinics.register', compact('plans'));
    }

    public function registerAdmin(CreateUser $createUser)
    {

        $user = DB::transaction(function () use ($createUser) {
            $data = request()->all();
            $user = $createUser($data, Role::where('name', 'clinica')->first(), [
                'ide' => ['nullable'],
                'plan_id' => ['required', 'exists:plans,id'],
            ]);

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

            return $user;

        });

        Auth::login($user);


        return Redirect('/office/register');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        /*return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);*/
        $data['active'] = 0; // las clinicas estan inactivos por defecto para revision
        $data['role'] = Role::whereName('clinica')->first();
        $data['api_token'] = Str::random(100);

        $user = $this->userRepo->store($data);

        if (isset($data['office'])) {
            $office = Office::findOrFail($data['office']);

            $user->offices()->save($office);

            //$office->active = 1;

            //$office->save();

            try {

                Notification::send($this->administrators, new NewClinic($user, $office));


            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            {
                \Log::error($e->getMessage());
            }    
           
            

        }

        return $user;
    }

    public function showCompletePayment()
    {
        if (auth()->user()->subscription()->exists()) return Redirect('/');

        $plans = Plan::where('for_clinic', 1)->get();

        return view('auth.clinics.completePayment', compact('plans'));
    }

    public function completePayment()
    {

        $plan = Plan::findOrFail(request('plan_id'));

        if(!$plan->cost > 0){

            if (!auth()->user()->subscription()->first()) {

                auth()->user()->subscription()->create([
                    'plan_id' => $plan->id,
                    'cost' => $plan->cost,
                    'quantity' => $plan->quantity,
                    'ends_at' => Carbon::now()->startOfMonth()->addMonths($plan->quantity),
                    'purchase_operation_number' => 'free'
                ]);

            }

            return Redirect('/office/register');

        }

        return Redirect('/clinic/subscriptions/'. $plan->id .'/buy');


    }


    public function showRegistrationOfficeForm()
    {

//        if(!auth()->user()->subscription){ // se verifica que tenga subscripcion
//
//            return Redirect('/clinic/complete-payment');
//        }

        if (auth()->user()->offices->count()) { // se verifica que no tenga clincias registradas
            return Redirect('/');
        }

        return view('auth.clinics.registerOffice');
    }

    public function registerOffice()
    {
        $this->validate(request(), [
            'name' => 'required',
            'address' => 'required',
            'province' => 'required',
            'canton' => 'required',
            'district' => 'required',
            'phone' => 'required',
            'utiliza_agenda_gps' => 'sometimes',
            // 'ide' => 'required',
            // 'ide_name' => 'required',
        ]);

        $data = request()->all();
        $data['type'] = 2;
        $data['utiliza_agenda_gps'] = 1;
        $data['active'] = 0;
        $data['verified'] = 1;
        $data['obligado_tributario'] = 'C';
        $data['notification'] = 1;
        $data['notification_date'] = Carbon::now()->toDateTimeString();
        $data['original_type'] = 2;
        $data['created_by'] = auth()->id();
        $data['fe'] = 1;

        //if($data['type'] == 'Consultorio Independiente') $data['active'] = 1;

        $office = $this->officeRepo->store($data);

        $office->rooms()->create([
            'name' => 'Consultorio Médico'
        ]);

        $mimes = ['jpg', 'jpeg', 'bmp', 'png'];


        if (request()->file('file')) {
            $file = request()->file('file');

            $ext = $file->guessClientExtension();

            if (in_array($ext, $mimes)) {
                //$fileUploaded = $file->storeAs('offices/' . $office->id, 'photo.jpg', 'public');
                $office->update([
                    'logo_path' => request()->file('file')->store('offices', 's3')
                ]);
            }
        }

        auth()->user()->update([

            "fe" => $data['fe']

            ]);

        $adminClinic = auth()->user();

        try {

            Notification::send($this->administrators, new NewClinic($adminClinic, $office));


        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
        {
            \Log::error($e->getMessage());
        }




        return Redirect('/');
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
            'password' => 'required|min:6|confirmed'
        ]);
    }
}
