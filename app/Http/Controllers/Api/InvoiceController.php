<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Invoice;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    
    public function show(Invoice $invoice)
    {

        return $invoice->load('lines', 'payments', 'clinic');
    }

    

}
