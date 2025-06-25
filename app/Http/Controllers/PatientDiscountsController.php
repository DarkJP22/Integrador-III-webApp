<?php

namespace App\Http\Controllers;

use App\Actions\CreateAccumulatedTransaction;
use App\Actions\CreateUserDiscount;
use App\Discount;
use App\User;

class PatientDiscountsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $patientAccounts = User::search(request('q'))
            ->with('accumulated')
            ->whereHas('roles', function ($query) {
                $query->where('name',  'paciente');
            })
            ->paginate(10);

        return response($patientAccounts, 200);
    }

    public function discounts()
    {
        $to = auth()->user()->isPharmacy() ? 'farmacia' : 'clinica';

        $discounts = Discount::where('for_gps_users', 1)
            ->where('to', $to)->get();

        return response($discounts, 200);
    }

    public function store(User $user, CreateAccumulatedTransaction $createAccumulatedTransaction, CreateUserDiscount $createUserDiscount)
    {
        $data = $this->validate(request(), [
            'amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_discount' => 'required|numeric',
            'total' => 'required|numeric',
            'CodigoMoneda' => 'required'
        ]);

        \DB::transaction(function () use ($user, $data, $createUserDiscount, $createAccumulatedTransaction) {
            $item = $createUserDiscount->create($user, $data);

            if ($user->accumulated) {
                $createAccumulatedTransaction($user->accumulated, 'Modulo', -$item->total_discount, $item);
            }
        });


        return response($user->load('accumulated'), 201);
    }
}
