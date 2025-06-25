<?php

namespace App\Http\Controllers\Lab;

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppNotificationJob;
use App\Jobs\SendAppPhoneMessageJob;
use App\Notifications\UploadedQuote;
use App\Patient;
use App\Product;
use App\QuoteOrder;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QuoteOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['status'] = request('status');

        $quotes = QuoteOrder::with('patient')
            ->search($search)
            ->when(filled($search['status']), function ($query) use ($search) {
                $method = $search['status'] ? 'whereNotNull' : 'whereNull';
                $query->$method('uploaded_at');
            })
            ->where('office_id', auth()->user()->offices->first()?->id)
            ->paginate();

        return view('lab.quoteOrders.index', [
            'quotes' => $quotes,
            'search' => $search
        ]);
    }

    public function upload(QuoteOrder $quote)
    {
        $this->validate(request(), [
            'voucher' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png']
        ]);

        $quote = DB::transaction(function () use ($quote) {
            // save the file in storage
            $path = request()->file('voucher')->store('quotes', 's3');

            if (!$path) {
                throw new Exception("The file could not be saved.", 500);
            }

            $quote->quote_path = $path;
            $quote->uploaded_at = now();
            $quote->save();

            return $quote;
        });

        if ($quote->user->push_token) {
            $title = 'Cotización Subida';
            $message = 'Cotización de boleta creada. Ingresa a la app en la pantalla de notificaciones para ver el detalle.';
            $extraData = [
                'type' => 'quote',
                'title' => $title,
                'body' => $message,
                'url' => '/notifications',
                'resource_id' => $quote->id
            ];

            SendAppNotificationJob::dispatch($title, $message, [$quote->user->push_token], $extraData);
        }

        if($quote->phone_number){
            $message = "Cotización de boleta creada. Ingresa a la app en la pantalla de notificaciones para ver el detalle. https://mobile.cittacr.com";
            SendAppPhoneMessageJob::dispatch($message, $quote->fullPhone)->afterCommit();
        }

        $quote->user?->notify(new UploadedQuote($quote));

        flash('Comprobante Subido Correctamente', 'success');

        return back();
    }
}
