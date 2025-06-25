<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;

class NotaDebitoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        if (auth()->user()->hasRole('asistente')) {

            return view('assistant.invoices.create', [
                'invoice' => $invoice->load('lines.taxes', 'referencias', 'lines.discounts', 'affiliation'),
                'tipoDocumento' => '02',
                'creatingNota' => 1
            ]);
        }

        if (auth()->user()->hasRole('clinica')) {

            return view('clinic.invoices.create', [
                'invoice' => $invoice->load('lines.taxes', 'referencias', 'lines.discounts', 'affiliation'),
                'tipoDocumento' => '02',
                'creatingNota' => 1
            ]);
        }
        if (auth()->user()->hasRole('laboratorio')) {

            return view('lab.invoices.create', [
                'invoice' => $invoice->load('lines.taxes', 'referencias', 'lines.discounts', 'affiliation'),
                'medic' => $invoice->user,
                'tipoDocumento' => '02',
                'creatingNota' => 1,
            ]);
        }

        return view('invoices.create', [
            'invoice' => $invoice->load('lines.taxes', 'referencias', 'lines.discounts', 'affiliation'),
            'tipoDocumento' => '02',
            'creatingNota' => 1
        ]);
    }
}
