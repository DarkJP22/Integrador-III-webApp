<?php

namespace App\Http\Controllers\Clinic;

use App\Commission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class ReportsCommissionLabsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');


    }

    public function commissionLabs()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $this->validate(request(), [
            'medic' => ['exists:users,id'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

        $office = auth()->user()->offices->first();
            
        $officeMedics = $office->users()->whereHas('roles', function($q){ 
            $q->where('name', 'medico');
        })->get();

       

        $commissions = Commission::where('user_id', request('medic'))
                                   ->when(request('start'), function ($query, $start)
                                   {
                                        $query->whereBetween('created_at', [$start, Carbon::parse(request('end') ?? $start)->endOfDay()]);
                                   })
                                   ->with('user')
                                   ->withCount('transactions')
                                   ->latest()
                                   ->paginate();

    

        return view('clinic.reports.commissionLabs', [
            'commissions' => $commissions,
            'officeMedics' => $officeMedics
            
        ]);



    }

    
}
