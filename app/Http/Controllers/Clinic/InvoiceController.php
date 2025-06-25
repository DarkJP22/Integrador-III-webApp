<?php

namespace App\Http\Controllers\Clinic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Patient;
use App\Repositories\MedicRepository;

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
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');
        $search['CodigoActividad'] = request('CodigoActividad');
        
        $office = auth()->user()->offices->first();
        
        //$medics = $this->medicRepo->findAllByOffice($office->id);
        $medicos = $office->users()->whereHas('roles', function($q){
            $q->where('name', 'medico');
        })->get();

        $adminsClinic = $office->users()->whereHas('roles', function ($q) {
            $q->Where('name', 'clinica');
        })->get();


        $admins = $adminsClinic->map(function ($item, $key) {
            $item->name = $item->name." (Administrador)";
            return $item;
        });
      
        $medics = $medicos->merge($admins);
        
        $search['office'] = request('office') ? request('office') : $office->id;
        
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


        return view('clinic.invoices.index', compact('invoices', 'invoicesTotals', 'search', 'medics'));
    }

  
}
