<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\Payment;

class PaymentInvoiceController extends Controller
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
    public function index(Invoice $invoice)
    {
        $search['q'] = request('q');

        $payments = $invoice->payments()->latest()->paginate(10);

        if (request()->wantsJson()) {
            return response($payments, 200);
        }

       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Invoice $invoice)
    {
        $this->validate(request(), [
            'amount' => 'required|numeric',
    
        ]);

        $data = request()->all();
        $data['user_id'] = auth()->user()->id;
        $data["office_id"] = $invoice->office_id;
        $data["CodigoMoneda"] = $invoice->CodigoMoneda;

        $payment = $invoice->payments()->create($data);

        return $payment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //$this->authorize('update', $payment);
        $invoice = $payment->invoice;
        $payment->delete();
        $invoice->calculatePendingAmount();
        
        if (request()->wantsJson()) {
            return response([], 204);
        }

        return Redirect('/payments');
    }

}
