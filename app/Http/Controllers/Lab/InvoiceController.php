<?php

namespace App\Http\Controllers\Lab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Patient;
use App\Repositories\MedicRepository;
use App\User;

class InvoiceController extends Controller
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
        if (!auth()->user()->hasRole('laboratorio')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');
        $search['CodigoActividad'] = request('CodigoActividad');
        
        $office = auth()->user()->offices->first();
        
        
        $search['office'] = request('office') ? request('office') : $office->id;
        $selectedMedic = request('medic') ? User::find(request('medic')) : null;
        
        $invoices = Invoice::with('notascreditodebito', 'user')->search($search);

        $invoicesTotals = clone $invoices;
        $invoicesTotals = $invoicesTotals->where(function ($query) {
                            $query->where('TipoDocumento', '01')
                                ->orWhere('TipoDocumento', '04');
                        })
                        ->where(function ($query) {
                            $query->whereNull('invoices.status_fe')
                                ->orWhere('invoices.status_fe', '<>', 'rechazado');
                        })
                        ->where('invoices.status', 1);

        $invoicesTotals = $invoicesTotals->selectRaw('SUM(CASE WHEN CodigoMoneda = "CRC" THEN TotalWithNota END) as total')->first();
        $invoices = $invoices->latest()->paginate(10);


        return view('lab.invoices.index', compact('invoices', 'invoicesTotals', 'search', 'selectedMedic'));
    }

  
}
