<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\UserBillingJob;
use App\Setting;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use App\Subscription;
use App\Plan;

class SubscriptionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * Guardar paciente
     */
    public function store(User $user)
    {
        //validamos que en users no hay email que va a registrase como paciente
        $this->validate(request(), [
            'plan_id' => 'required',
        ]);

        $newPlan = Plan::find(request('plan_id'));

        $subscriptionMonthsFree = Setting::getSetting('subscription_months_free') ?? 0;

        $user->subscription()->create([
            'plan_id' => $newPlan->id,
            'cost' => $newPlan->cost,
            'quantity' => $newPlan->quantity,
            'ends_at' => Carbon::now()->startOfMonth()->addMonths($newPlan->quantity + (int) $subscriptionMonthsFree),
        ]);

        flash('Subscripción Creada', 'success');

        return Redirect('/admin/users/'.$user->id);
    }

    /**
     * Actualizar Paciente
     */
    public function update(Subscription $subscription)
    {
        $data = $this->validate(request(), [
            'plan_id' => 'required',
            'cost' => ['sometimes', 'required', 'numeric'],
            'ends_at' => ['sometimes', 'required', 'date'],
        ]);

        $newPlan = Plan::find($data['plan_id']);


        $subscription->plan_id = $newPlan->id;
        $subscription->cost = $data['cost'] ?? $newPlan->cost;
        $subscription->ends_at = $data['ends_at'] ?? $subscription->ends_at;
        $subscription->save();

        if (request()->wantsJson()) {
            return response()->json($subscription->load('plan'), 200);
        }

        flash('Subscripción Actualizada', 'success');

        return Redirect('/admin/users/'.$subscription->user_id);
    }

    /**
     * Eliminar consulta(cita)
     */
    public function destroy(Subscription $subscription)
    {

        $subscription->delete();

        flash('Subscripción Eliminado', 'success');

        return back();
    }

    public function billing(Subscription $subscription)
    {
        $user = $subscription->user;

        if($subscription->cost > 0){
            $this->dispatch(new UserBillingJob($user))->onConnection('sync');
        }

        return $subscription->fresh()->load('plan');
    }


}
