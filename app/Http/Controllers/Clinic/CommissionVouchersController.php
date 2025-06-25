<?php

namespace App\Http\Controllers\Clinic;

use App\Commission;
use App\Events\CommissionPaidEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppNotificationJob;
use Exception;
use Illuminate\Support\Facades\DB;

class CommissionVouchersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Commission $commission)
    {
        $this->validate(request(), [
            'voucher' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png']
        ]);

        $commission = DB::transaction(function () use ($commission) {
            // save the file in storage
            $path = request()->file('voucher')->store('commissions', 's3');

            if (!$path) {
                throw new Exception("The file could not be saved.", 500);
            }

            $commission->comprobante = $path;
            $commission->paid_at = now();
            $commission->save();

            $commission->load('transactions');
            $commission->transactions->each->update([
                'paid_at' => $commission->paid_at
            ]);

            return $commission;
        });

        // broadcast pusher
        try {
            CommissionPaidEvent::dispatch(auth()->user(), $commission);
        } catch (\Exception $ex) {
            \Log::error('ERROR BROADCAST: '.json_encode($ex->getMessage()));
        }

        if ($commission->user->push_token) {
            $title = 'Comisión Pagada';
            $message = 'Los estudios de la fecha ' . $commission->created_at . ' han sido cancelado.';
            $extraData = [
                'type' => 'commission',
                'title' => $title,
                'body' => $message,
                'url' => '/medic/commissions',
                'resource_id' => $commission->id
            ];

            SendAppNotificationJob::dispatch($title, $message, [$commission->user->push_token], $extraData);
        }

        flash('Comprobante Subido Correctamente', 'success');

        return back();
    }
}
