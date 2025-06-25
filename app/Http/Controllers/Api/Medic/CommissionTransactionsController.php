<?php

namespace App\Http\Controllers\Api\Medic;


use App\CommissionTransaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommissionTransactionResource;



class CommissionTransactionsController extends Controller
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

        $commissionTransactions = CommissionTransaction::where('user_id', auth()->user()->id)->with('resource')->pending()->latest()->paginate(10);
        $totals = CommissionTransaction::getQuery()
                            ->where('user_id', auth()->user()->id)
                            ->selectRaw("SUM(CASE WHEN paid_at IS NULL THEN Total ELSE 0 END) as TotalPending")
                            ->selectRaw("SUM(CASE WHEN paid_at IS NOT NULL THEN Total ELSE 0 END) as TotalPaid")
                            ->selectRaw('SUM(Total) as Total')
                            ->first();

        return CommissionTransactionResource::collection($commissionTransactions)->additional(['meta' => [
            'totals' => $totals,
        ]]);
    }
}
