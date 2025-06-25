<?php

namespace App\Http\Controllers\Auth;


use App\Actions\CreateUser;
use App\Http\Controllers\Controller;

use App\Repositories\UserRepository;
use App\Role;
use App\Setting;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Pharmacy;
use App\Notifications\NewPharmacy;
use App\Plan;
use Illuminate\Support\Facades\Notification;
use App\Repositories\PharmacyRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegisterPharmacyController extends Controller
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


    protected $administrators;

    public function __construct(protected UserRepository $userRepo, protected PharmacyRepository $pharmacyRepo)
    {
        $this->middleware('guest')->except('showRegistrationPharmacyForm', 'registerPharmacy', 'showCompletePayment',
            'completePayment');

        $this->administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();
    }


    public function showRegistrationForm()
    {
        $plans = Plan::where('for_pharmacy', 1)->get();

        return view('auth.pharmacies.register', compact('plans'));
    }

    public function registerAdmin(CreateUser $createUser)
    {
        $user = DB::transaction(function () use ($createUser) {
            $data = request()->all();
            $user = $createUser($data, Role::where('name', 'farmacia')->first(), [
                'ide' => ['nullable'],
            ]);

            $plan = Plan::where('for_pharmacy', 1)->where('cost', 0)->first(); //Plan::findOrFail(request('plan_id'));


            if (!$user->subscription()->first() && $plan) {

                $user->subscription()->create([
                    'plan_id' => $plan->id,
                    'cost' => $plan->cost,
                    'quantity' => $plan->quantity,
                    'ends_at' => Carbon::now()->startOfMonth()->addMonths($plan->quantity),
                    'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free'
                ]);

            }
        });

        Auth::login($user);


        return Redirect('/pharmacy/registerform');
    }


    protected function create(array $data)
    {
        /*return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);*/
        $data['active'] = 1; // las clinicas estan inactivos por defecto para revision
        $data['role'] = Role::whereName('farmacia')->first();
        $data['api_token'] = Str::random(100);

        $user = $this->userRepo->store($data);


        if (isset($data['pharmacy'])) {
            $pharmacy = Pharmacy::findOrFail($data['pharmacy']);

            $user->pharmacies()->save($pharmacy);

            //$pharmacy->active = 1;

            //$pharmacy->save();
            try {

                Notification::send($this->administrators, new NewPharmacy($user, $pharmacy));


            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
            {
                \Log::error($e->getMessage());
            }


        }

        return $user;
    }

    public function showCompletePayment()
    {
        if (auth()->user()->subscription()->exists()) {
            return Redirect('/');
        }

        $plans = Plan::where('for_pharmacy', 1)->get();

        return view('auth.pharmacies.completePayment', compact('plans'));
    }

    public function completePayment()
    {

        $plan = Plan::findOrFail(request('plan_id'));

        if (!$plan->cost > 0) {

            if (!auth()->user()->subscription()->first()) {

                auth()->user()->subscription()->create([
                    'plan_id' => $plan->id,
                    'cost' => $plan->cost,
                    'quantity' => $plan->quantity,
                    'ends_at' => Carbon::now()->startOfMonth()->addMonths($plan->quantity),
                    'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free'
                ]);

            }

            return Redirect('/pharmacy/registerform');

        }

        return Redirect('/pharmacy/subscriptions/'.$plan->id.'/buy');


    }


    public function showRegistrationPharmacyForm()
    {

//        if (!auth()->user()->subscription) { // se verifica que tenga subscripcion
//
//            return Redirect('/pharmacy/complete-payment');
//        }

        if (auth()->user()->pharmacies->count()) { // se verifica que no tenga clincias registradas
            return Redirect('/');
        }

        return view('auth.pharmacies.registerPharmacy');
    }

    public function registerPharmacy()
    {
        $this->validate(request(), [

            'name' => 'required',
            'address' => 'required',
            'province' => 'required',
            'canton' => 'required',
            'district' => 'required',
            'phone' => 'required',
            // 'ide' => 'required',
            // 'ide_name' => 'required',
        ]);

        $data = request()->all();
        $data['active'] = 1;
        $data['verified'] = 1;
        $data['notification'] = 1;
        $data['notification_date'] = Carbon::now()->toDateTimeString();


        $pharmacy = $this->pharmacyRepo->store($data);

        $mimes = ['jpg', 'jpeg', 'bmp', 'png'];


        if (request()->file('file')) {
            $file = request()->file('file');

            $ext = $file->guessClientExtension();

            if (in_array($ext, $mimes)) {
                //$fileUploaded = $file->storeAs('offices/' . $office->id, 'photo.jpg', 'public');
                $pharmacy->update([
                    'logo_path' => request()->file('file')->store('pharmacies', 's3')
                ]);
            }
        }


        $adminPharmacy = auth()->user();

        try {

            Notification::send($this->administrators, new NewPharmacy($adminPharmacy, $pharmacy));


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
