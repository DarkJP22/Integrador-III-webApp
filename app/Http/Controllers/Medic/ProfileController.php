<?php

namespace App\Http\Controllers\Medic;

use App\SubscriptionInvoice;
use App\User;
use Illuminate\Validation\Rule;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use App\Speciality;


class ProfileController extends Controller
{
    public function __construct(public UserRepository $userRepo)
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = auth()->user();

        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }

        $this->authorize('update', $user);

        $tab = request('tab');

        $assistants = auth()->user()->assistants()->with('clinicsAssistants')->get();

        $specialities = Speciality::all();

        $commissionByAppointments = auth()->user()->specialities->count() ? auth()->user()->subscription->plan->specialist_cost_commission_by_appointment : auth()->user()->subscription->plan->general_cost_commission_by_appointment;

        $configFactura = $user->getObligadoTributario();
        $userOffices = auth()->user()->offices->count();

        return view('medic.profile', [
            'profileUser' => $user,
            'specialities' => $specialities,
            'assistants' => $assistants,
            'tab' => $tab,
            'configFactura' => $configFactura ? $configFactura->load('activities') : null,
            'userOffices' => $userOffices,
            'commissionByAppointments' => $commissionByAppointments,
        ]);
    }

    /**
     * Actualizar informacion basica del medico
     */
    public function update(User $user)
    {
        $this->authorize('update', $user);

        $this->validate(request(), [
            'name' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'],//, Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'medic_code' => 'required',
            'general_cost_appointment' => ['sometimes', 'numeric'],
        ]);

        $user = $this->userRepo->update($user->id, request()->all());
        $user->setSettings(request()->only(['freeDays', 'general_cost_appointment', 'minTime', 'maxTime']));

        flash('Cuenta Actualizada', 'success');

        return Redirect('medic/profiles');

    }


}
