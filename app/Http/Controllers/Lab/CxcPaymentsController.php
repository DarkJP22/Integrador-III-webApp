<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Invoice;
use App\Payment;


class CxcPaymentsController extends Controller
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

        if (!auth()->user()->hasRole('laboratorio')) {
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['CodigoActividad'] = request('CodigoActividad');

        $office = auth()->user()->offices->first();

        $search['office'] = request('office') ? request('office') : $office->id;

        $invoices = Invoice::where('CondicionVenta', '02')
            ->search($search);

        $invoiceIds = $invoices->pluck('id');

        $search['start'] = request('start');
        $search['end'] = request('end');
        $payments = Payment::search($search)->whereIn('invoice_id', $invoiceIds);

        $paymentsTotals = clone $payments;
        $paymentsTotals = $paymentsTotals->sum('amount');

        $payments = $payments->orderBy('date', 'desc')->paginate();

        return view('lab.payments.index', compact('payments', 'paymentsTotals', 'search'));
    }
}
