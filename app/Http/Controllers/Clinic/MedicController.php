<?php

namespace App\Http\Controllers\Clinic;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Speciality;
use App\Repositories\MedicRepository;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\User;

use App\Plan;
use Illuminate\Validation\Rule;
use App\Repositories\UserRepository;
use App\Repositories\PatientRepository;
use App\Role;
use Illuminate\Support\Str;

class MedicController extends Controller
{
    public function __construct(MedicRepository $medicRepo, UserRepository $userRepo, PatientRepository $patientRepo)
    {

        $this->medicRepo = $medicRepo;
        $this->userRepo = $userRepo;
        $this->patientRepo = $patientRepo;
        $this->middleware('auth');

        View::share('specialities', Speciality::all());
    }

    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $search['q'] = request('q');

        $office = auth()->user()->offices->first();


        $medics = $this->medicRepo->findAllByOffice($office->id, $search);


        return view('clinic.medics.index', compact('medics', 'office', 'search'));

    }

    public function store()
    {

        //validamos que en users no hay email que va a registrase como paciente
        $this->validate(request(), [
            'ide' => 'required|unique:users|digits_between:9,12',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'phone_country_code' => 'required',
            'phone_number' => 'required|digits_between:8,15',
            'medic_code' => 'required|unique:users',
            'plan_id' => 'required'
        ]);

        $data = request()->all();

        $data['api_token'] = Str::random(50);

        if (request('esteticista')) {
            $data['role'] = Role::whereName('esteticista')->first();
        }

        $user = $this->userRepo->store($data);

        $user->setSettings([
            'general_cost_appointment' => $data['general_cost_appointment'] ?? 0,
            'slotDuration' => '00:30:00',
            'minTime' => '06:00:00',
            'maxTime' => '18:00:00',
            'freeDays' => '[]',
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

        $office = auth()->user()->offices->first();

        $user->offices()->attach($office->id, ['verified' => 1, 'obligado_tributario' => 'C']);

        //Se asigna a todos los pacientes al medico recien creado

        $patients = $this->patientRepo->findAllOfClinicWithoutPaginate($office);

        foreach ($patients as $patientClinic) { //se agregan a todos los medicos de la clinica

            $user->patients()->save($patientClinic, ['authorization' => 1]);

        }




        flash('Médico Creado', 'success');

        return Redirect('/clinic/medics/');
    }

    /**
     * Mostrar vista crear paciente
     */
    public function create()
    {
        //$this->authorize('create', User::class);
        $plans = Plan::where('for_medic', 1)->get();

        return view('clinic.medics.create', compact('plans'));
    }

    public function edit(User $medic)
    {
        if (!$medic->hasRole('medico') && !$medic->hasRole('esteticista')) { // si no es medico sacarlo
            return redirect('/clinic/medics');
        }

        $office = auth()->user()->offices->first();

        $isMedicOfClinic = \DB::table('office_user')->where('office_id', $office->id)->where('user_id',
            $medic->id)->first();

        if (!$isMedicOfClinic) { // si no es medico de la clinica sacarlo
            return redirect('/clinic/medics');
        }

        return view('clinic.medics.edit', [
            'profileUser' => $medic
        ]);
    }

    /**
     * Actualizar Paciente
     */
    public function update(User $medic)
    {
        $this->validate(request(), [
            //'ide' => ['required', Rule::unique('users')->ignore($medic->id), 'digits_between:9,12'],
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($medic->id)],
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15', Rule::unique('users')->ignore($medic->id)],
            'medic_code' => ['required', Rule::unique('users')->ignore($medic->id)],


        ]);

        $data = request()->all();

        $role = !request('esteticista') ? Role::whereName('medico')->first() : Role::whereName('esteticista')->first();
        $medic->roles()->sync($role, true);


        $user = $this->userRepo->update($medic->id, $data);


        flash('Médico Actualizado', 'success');

        return back();
    }


}
