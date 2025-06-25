<?php

namespace App\Http\Controllers\Api\Medic;

use App\Commission;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommissionResource;
use App\Http\Resources\CommissionTransactionResource;

class CommissionsController extends Controller
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

        $commissions = Commission::where('user_id', auth()->user()->id)->with('transactions')->latest()->paginate(10);
        $totals = Commission::getQuery()
                            ->where('user_id', auth()->user()->id)
                            ->selectRaw("SUM(CASE WHEN paid_at IS NULL THEN Total ELSE 0 END) as TotalPending")
                            ->selectRaw("SUM(CASE WHEN paid_at IS NOT NULL THEN Total ELSE 0 END) as TotalPaid")
                            ->selectRaw('SUM(Total) as Total')
                            ->first();

        return CommissionResource::collection($commissions)->additional(['meta' => [
            'totals' => $totals,
        ]]);
    }

    public function transactions(Commission $commission)
    {
        $transactions = $commission->transactions()->with('resource')->latest()->paginate(50);
       
        return CommissionTransactionResource::collection($transactions);
    }
}
