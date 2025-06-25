<?php

namespace App\Http\Controllers\Api;

use App\Accumulated;
use App\Actions\CreateAccumulatedTransaction;
use App\Setting;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\User;

class AccumulatedsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store($patientIde, CreateAccumulatedTransaction $createAccumulatedTransaction)
    {
        $data = $this->validate(request(), [
            'total' => ['required']
        ]);

        $user = User::where('ide', $patientIde)->first();

        if (!$user) {
            return response('Usuario No Existe en Doctor Blue', 422);
        }

        if (!$user->accumulated) {
            return response('Usuario No tiene Acumulado', 422);
        }

        $accumulatedAmount = floatval($data['total']) * (((Setting::getSetting('porc_accumulated')) ?? 0) / 100);

        $createAccumulatedTransaction($user->accumulated, 'API POS', $accumulatedAmount);

        return response([], 201);
    }
    public function show(Accumulated $accumulated)
    {
        return $accumulated->load(['user', 'holder', 'patients', 'transactions' => function ($query) {
            $query->where('action', 'not like', 'Rollback%')
                ->orderBy('created_at', 'desc')->limit(10);
        }, 'transactions.resource']);
    }
}
