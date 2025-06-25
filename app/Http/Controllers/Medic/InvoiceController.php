<?php

namespace App\Http\Controllers\Medic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Patient;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Invoice::class);

        if(!auth()->user()->isCurrentRole('medico')){
            return redirect('/');
        }
        
        if( !auth()->user()->belongsToCentroMedico() && !auth()->user()->subscriptionPlanHasFe() ){ /*|| (!auth()->user()->permissionCentroMedico())*/ 
            return redirect('/medic/changeaccounttype');
        }
        
        // if(auth()->user()->belongsToCentroMedico() && auth()->user()->centroMedicoPendingCharge()){
        //     return redirect('/medic/disabledInvoices');
        // }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['office'] = request('office');



        $invoices = auth()->user()->invoices()->with('notascreditodebito', 'user', 'clinic')->search($search);
        
        if(!request('office')){

            $clinicsIds = auth()->user()->clinicsWithPermissionFe()->pluck('id');
        
            $invoices = $invoices->whereIn('office_id', $clinicsIds);
        }
        
        $invoices = $invoices->latest()->paginate(10);


        return view('invoices.index', compact('invoices', 'search'));
    }

  
}
