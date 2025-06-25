<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Invoice;

class PatientInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function index($patientIde)
    {

        $invoices = Invoice::where('identificacion_cliente', $patientIde)
                            ->with('lines','payments', 'clinic')
                            ->where(function ($query) {
                                $query->where('TipoDocumento', '01')
                                    ->orWhere('TipoDocumento', '04');
                            })
                            ->where(function ($query) {
                                $query->whereNull('status_fe')
                                    ->orWhere('status_fe', '<>', 'rechazado');
                            })
                            ->whereDoesntHave('notascreditodebito')
                            ->latest()
                            ->paginate(10);
        

        return $invoices;
    }
    

}
