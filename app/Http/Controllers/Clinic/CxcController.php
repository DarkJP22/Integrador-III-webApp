<?php

namespace App\Http\Controllers\Clinic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Patient;
use App\Repositories\MedicRepository;
use Illuminate\Support\Carbon;

class CxcController extends Controller
{
    public function __construct(MedicRepository $medicRepo)
    {
        $this->middleware('auth');
        $this->medicRepo = $medicRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');
        $search['CodigoActividad'] = request('CodigoActividad');
        $search['status'] = request('status') ?? 'pending';
        
        $office = auth()->user()->offices->first();
        
        $medics = $this->medicRepo->findAllByOffice($office->id);
        
        $search['office'] = request('office') ? request('office') : $office->id;

        $invoices = Invoice::with('user','payments', 'notascreditodebito')
        ->where('CondicionVenta', '02')
        ->where(function ($query) {
            $query->where('TipoDocumento', '01')
                ->orWhere('TipoDocumento', '04');
        })
        ->where(function ($query) {
            $query->whereNull('status_fe')
                ->orWhere('status_fe', '<>', 'rechazado');
        })
        ->search($search);

        if ($search['status'] == 'pending') {

            $invoices = $invoices->where('cxc_pending_amount', '>', 1);
        }
        if ($search['status'] == 'cancel') {

            $invoices = $invoices->where('cxc_pending_amount', '<', 1);
        }

        if($search['status'] == 'overdue'){
            $invoices = $invoices->where('PlazoCredito', '<', Carbon::now());
         }

        $cxcTotals = clone $invoices;

        $cxcTotals = $cxcTotals->where('invoices.status', 1);
                
        $cxcTotals = $cxcTotals->sum('cxc_pending_amount');

        $invoices = $invoices->latest()->paginate(10);
        
        
        return view('clinic.cxc.index', compact('invoices', 'cxcTotals', 'search', 'medics'));
     
      
       
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('lines.taxes', 'referencias');
        $totalAbonos = 0;
        $pendiente = 0;

        $totalAbonos = $invoice->payments->sum('amount');
        $pendiente = $invoice->TotalComprobante - $totalAbonos;

        return view('clinic.cxc.print', compact('invoice', 'totalAbonos', 'pendiente'));
    }
  
}
