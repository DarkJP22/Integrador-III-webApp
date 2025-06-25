<?php

namespace App\Http\Controllers\Lab;

use App\Commission;
use App\CommissionTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsCommissionLabsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');


    }

    public function commissionLabs()
    {
        if (!auth()->user()->hasRole('laboratorio')) {
            return redirect('/');
        }

        $this->validate(request(), [
            'medic' => ['nullable','exists:users,id'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

            
        $selectedMedic = User::find(request('medic'));

       

        $commissions = Commission::query()
                                   ->when(request('medic'), function ($query)
                                   {
                                        $query->where('user_id', request('medic'));
                                   })
                                   ->when(request('start'), function ($query, $start)
                                   {
                                        $query->whereBetween('created_at', [$start, Carbon::parse(request('end') ?? $start)->endOfDay()]);
                                   })
                                   ->with('user')
                                   ->withCount('transactions')
                                   ->latest()
                                   ->paginate();

    

        return view('lab.reports.commissionLabs', [
            'commissions' => $commissions,
            'selectedMedic' => $selectedMedic
            
        ]);



    }

    public function commissionLabsPending()
    {
        if (!auth()->user()->hasRole('laboratorio')) {
            return redirect('/');
        }

        $this->validate(request(), [
            'medic' => ['nullable','exists:users,id'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

            
        $selectedMedic = User::find(request('medic'));

       
        $commissions = DB::table('commission_transactions')
                        ->join('users', 'users.id', '=', 'commission_transactions.user_id')
                        ->selectRaw('users.name, count(commission_transactions.id) as transactions_count, sum(commission_transactions.Total) as Total')
                        ->whereNull('commission_id')
                        ->when(request('medic'), function ($query)
                        {
                            $query->where('commission_transactions.user_id', request('medic'));
                        })
                        ->when(request('start'), function ($query, $start)
                        {
                            $query->whereBetween('commission_transactions.created_at', [$start, Carbon::parse(request('end') ?? $start)->endOfDay()]);
                        })
                        ->groupBy('user_id')
                        ->paginate();
       

        return view('lab.reports.commissionLabsPending', [
            'commissions' => $commissions,
            'selectedMedic' => $selectedMedic
            
        ]);



    }

    
}
