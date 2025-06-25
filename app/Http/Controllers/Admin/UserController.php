<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CancelAccount;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Role;
use App\Repositories\UserRepository;
use App\Plan;
use App\User;
use Illuminate\Validation\Rule;
use App\Notifications\UserActive;
use App\Office;
use App\Patient;
use App\RegisterAuthorizationCode;
use App\Speciality;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class UserController extends Controller
{
    public function __construct(UserRepository $userRepo)
    {

        $this->middleware('auth');
        $this->userRepo = $userRepo;
    }
    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        $this->authorize('create', User::class);

        $search['q'] = request('q');
        $search['role'] = request('role');
        $roles = Role::all();

        $users = $this->userRepo->findAll($search);
        $plans = Plan::all();

        return view('admin.users.index', compact('users', 'search', 'roles', 'plans'));
    }

    /**
     * Mostrar vista editar paciente
     */
    public function edit(User $user)
    {
        $user->load('roles');

        $this->authorize('update', $user);

        $roles = Role::all();
        $specialities = Speciality::all();
        $plans = Plan::all();
        $subscription = $user->subscription;

        $configFactura = $user->getObligadoTributario();

        $photos = Storage::disk('s3')->allFiles('users/' . $user->id . '/titles-photos');

        $titlesPhotos = collect($photos)->map(function ($photo) {
            return [
                'url' => Storage::disk('s3')->url($photo),
                'name' => basename($photo)
            ];
        });

        return view('admin.users.edit', compact('user', 'plans', 'roles', 'subscription', 'configFactura', 'specialities', 'titlesPhotos'));
    }

    /**
     * Actualizar Paciente
     */
    public function update(User $user)
    {
        $this->validate(request(), [
            'name' => 'required|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)] //'required|email|max:255|unique:patients',
        ]);

        $user = $this->userRepo->update($user->id, request()->all());

        flash('Usuario Actualizado', 'success');

        return Redirect('/admin/users/' . $user->id);
    }

    /**
     * Active a user.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function active($id)
    {
        $user = $this->userRepo->update_active($id, 1);
        $user->cancel_at = null;
        $user->save();
        try {

            $user->notify(new UserActive($user));
        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
        {
            \Log::error($e->getMessage());
        }

        // try {
        //     \Mail::to($user)->send(new UserActive($user));
        // } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
        //     \Log::error($e->getMessage());
        // }

        return back();
    }

    /**
     * Inactive a user.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function inactive($id)
    {
        $user = $this->userRepo->update_active($id, 0);
        $user->cancel_at = now();
        $user->save();

        return back();
    }



    public function commission(User $user)
    {
        $user->commission_affiliation = !$user->commission_affiliation;
        $user->save();

        return back();
    }

    public function enableAgenda(User $user)
    {
        $user->utiliza_agenda_gps = !$user->utiliza_agenda_gps;
        $user->save();

        return back();
    }

    public function cancelAccount(User $user, CancelAccount $cancelAccount)
    {
        try {
            $cancelAccount->execute($user);
        } catch (\Exception $e) {
            flash('Ocurrió un error al cancelar la cuenta: '.$e->getMessage(), 'danger');
        }

        return back();
    }
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            $this->userRepo->delete($user->id);
            Patient::where('ide', $user->ide)->delete();
        });

        flash('Usuario Eliminado', 'success');

        return back();
    }

    public function changeAccountCentroMedico(User $user) // medicos a centro medico
    {

        $this->validate(request(), [
            'email' => ['nullable', 'email', Rule::unique('users')] //'required|email|max:255|unique:patients',
        ]);

        //validamos que en users no hay email que va a registrase como paciente
        $data = [
            'name' =>  request('name') ? request('name') : 'admin.' . Str::slug($user->name, '.'),
            'email' => request('email') ? request('email') : Str::slug($user->name, '.') . $user->id . '@cittacr.com',
            'password' => request('password') ? request('password') : $user->phone_number,
            'role' => Role::whereName('clinica')->first(),
            'phone_country_code' => request('phone_country_code') ? request('phone_country_code') : '+506',
            'phone_number' => request('phone_number') ? request('phone_number') : $user->phone_number
        ];

        $data['active'] = 1; // los medicos estan inactivos por defecto para revision
        $data['api_token'] = Str::random(50);
        $data['fe'] = 1;

        $admin = $this->userRepo->store($data);

        if ($admin) {

            $office = Office::findOrFail(request('office_id'));
            $office->type = 2; // clinica privada (centro medico)
            $office->fe = 1;
            $office->save();

            $admin->offices()->save($office, ['obligado_tributario' => 'C', 'verified' => 1]); //admin clinica
            $user->offices()->updateExistingPivot($office->id, ['obligado_tributario' => 'C', 'verified' => 1]); // medico

            $user->assignAccount($admin); // se asigna cuenta de admin de clinica a medico

            $plan = Plan::findOrFail(2); // perfil centro medico

            if (!$admin->subscription()->first()) {

                $admin->subscription()->create([
                    'plan_id' => $plan->id,
                    'cost' => $plan->cost,
                    'quantity' => $plan->quantity,
                    'ends_at' => Carbon::now()->startOfMonth()->addMonths($plan->quantity),
                    'purchase_operation_number' => '--superadmin'
                ]);
            }

            flash('Se creo la cuenta correctamente', 'success');

            return back();
        }



        flash('Ocurrio un error al crear la cuenta', 'danger');

        return back();
    }

    /**
     * Guardar paciente
     */
    public function store()
    {
        //validamos que en users no hay email que va a registrase como paciente
        $this->validate(request(), [
            'ide' => 'required|unique:users|digits_between:9,12',
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => 'required|digits_between:8,15',
        ]);

        $data = request()->all();
        $data['active'] = 1; // los medicos estan inactivos por defecto para revision
        $data['fe'] = 1;
        $data['api_token'] = Str::random(50);

        $user = \DB::transaction(function () use ($data) {
            $user = $this->userRepo->store($data);

            if ($user->hasRole('medic')) {
                $plan = Plan::where('for_medic', 1)->first();

                if (!$user->subscription()->first()) {

                    $user->subscription()->create([
                        'plan_id' => $plan->id,
                        'cost' => $plan->cost,
                        'quantity' => $plan->quantity,
                        'ends_at' => Carbon::now()->startOfMonth()->addMonths($plan->quantity),
                        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free'
                    ]);
                }
            }


            return $user;
        });

        flash('Usuario Creado', 'success');

        return Redirect('/admin/users/' . $user->id);
    }

    /**
     * Mostrar vista crear paciente
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::all();
        $specialities = Speciality::all();

        return view('admin.users.create', compact('roles', 'specialities'));
    }

    public function authorizationCodes()
    {
        $codes = RegisterAuthorizationCode::latest()->paginate();

        return $codes;
    }
    public function generateAuthorizationCode()
    {
        $code = RegisterAuthorizationCode::generate();

        return $code;
    }

    public function getTitles(User $user)
    {

        $photos = Storage::disk('s3')->allFiles('users/' . $user->id . '/titles-photos');

        $titlesPhotos = collect($photos)->map(function ($photo) {
            return [
                'url' => Storage::disk('s3')->url($photo),
                'name' => basename($photo)
            ];
        });

        return $titlesPhotos;

    }
}
