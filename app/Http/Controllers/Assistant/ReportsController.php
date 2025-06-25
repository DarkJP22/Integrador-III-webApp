<?php

namespace App\Http\Controllers\Assistant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Office;
use App\User;
use Carbon\Carbon;

class ReportsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');

    }

    public function balance()
    {
        if (!auth()->user()->hasRole('asistente')) {
            return redirect('/');
        }

        $search['date'] = request('date') ? request('date') : Carbon::now()->toDateString();
        $search['CodigoActividad'] = request('CodigoActividad');
        
        $start = new Carbon($search['date']);
        $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['date'];
        $end = new Carbon($end);

        $office = auth()->user()->clinicsAssistants->first();
    
        //$medics = Office::find($office->id)->medicsWithInvoices(Carbon::now(), Carbon::now());

        $medics = User::whereHas('invoices', function ($query) use ($office, $start, $end, $search) {

            $query->where('office_id', $office->id)
                ->where(function ($query) {
                    $query->where('TipoDocumento', '01')
                        ->orWhere('TipoDocumento', '04');
                })
                ->where(function ($query) {
                    $query->whereNull('status_fe')
                        ->orWhere('status_fe','<>', 'rechazado');
                })
                ->where('CodigoActividad', $search['CodigoActividad'])
                ->where([
                    ['invoices.created_at', '>=', $start->startOfDay()],
                    ['invoices.created_at', '<=', $end->endOfDay()]
                ]);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'medico')
                  ->orWhere('name', 'esteticista');

        })->where('active', 1)->with(['invoices' => function ($query) use ($office, $start, $end, $search)
        {
            $query->where('office_id', $office->id)
                ->where(function ($query) {
                    $query->where('TipoDocumento', '01')
                        ->orWhere('TipoDocumento', '04');
                })
                ->where(function ($query) {
                    $query->whereNull('status_fe')
                        ->orWhere('status_fe','<>', 'rechazado');
                })
                ->where('CodigoActividad', $search['CodigoActividad'])
                ->where([
                    ['invoices.created_at', '>=', $start->startOfDay()],
                    ['invoices.created_at', '<=', $end->endOfDay()]
                ]);
        }])->get();

       
        $totalAppointments = 0;
        $totalPending = 0;
        $totalInvoicesCRC = 0;
        $totalInvoicesUSD = 0;
        $totalInvoicesPendingCRC = 0;
        $totalInvoicesPendingUSD = 0;
        $totalCommission = 0;
        
        foreach ($medics as $medic) {
            $invoicesTotalMedicUSD = $medic->invoices->sum(function ($invoice) {
                return $invoice->CodigoMoneda == 'USD' ? $invoice->TotalWithNota : 0;
            });
            $invoicesTotalMedicCRC = $medic->invoices->sum(function ($invoice) {
                return $invoice->CodigoMoneda == 'CRC' ? $invoice->TotalWithNota : 0;
            });
           
            $medic->finishedInvoices = $medic->invoices->sum(function ($invoice) {
                return $invoice->status == 1 ? 1 : 0;
            });

            $totalAppointments += $medic->finishedInvoices;

            $invoicesTotalPendingMedicUSD = $medic->invoices->sum(function ($invoice) {
                return $invoice->status == 0 && $invoice->CodigoMoneda == 'USD' ? $invoice->TotalWithNota : 0;
            });
            $invoicesTotalPendingMedicCRC = $medic->invoices->sum(function ($invoice) {
                return $invoice->status == 0 && $invoice->CodigoMoneda == 'CRC' ? $invoice->TotalWithNota : 0;
            });

            $medic->pendingInvoices = $medic->invoices->sum(function ($invoice) {
                return $invoice->status == 0 ? 1 : 0;
            });

            $totalPending += $medic->pendingInvoices;

            $medic->invoicesTotalMedicCRC = $invoicesTotalMedicCRC;
            $medic->invoicesTotalMedicUSD = $invoicesTotalMedicUSD;
            $medic->invoicesTotalPendingMedicCRC = $invoicesTotalPendingMedicCRC;
            $medic->invoicesTotalPendingMedicUSD = $invoicesTotalPendingMedicUSD;
            $totalInvoicesCRC += $invoicesTotalMedicCRC;
            $totalInvoicesUSD += $invoicesTotalMedicUSD;
            $totalInvoicesPendingCRC += $invoicesTotalPendingMedicCRC;
            $totalInvoicesPendingUSD += $invoicesTotalPendingMedicUSD;
        }
      
        $statisticsInvoices = [
            'medics' => $medics,
            'totalAppointments' => $totalAppointments,
            'totalInvoicesCRC' => $totalInvoicesCRC,
            'totalInvoicesUSD' => $totalInvoicesUSD,
            'totalPending' => $totalPending,
            'totalInvoicesPendingCRC' => $totalInvoicesPendingCRC,
            'totalInvoicesPendingUSD' => $totalInvoicesPendingUSD,
           
        ];
        
        // $medics = User::whereHas('invoices' , function ($query) use ($office, $start, $end) {
           
        //     $query->where('office_id', $office->id)
        //     ->where([['invoices.created_at', '>=', $start->startOfDay()],
        //         ['invoices.created_at', '<=',$end->endOfDay()]]);
        // })->whereHas('roles', function ($query){
        //                         $query->where('name',  'medico')
        //                         ->orWhere('name',  'clinica')
        //                        ->orWhere('name',  'asistente');
                                     
        //                     })->where('active', 1)->get();
   
        // $totalAppointments = 0;
        // $totalPending = 0;
        // $totalInvoicesCRC = 0;
        // $totalInvoicesUSD = 0;
        // $totalInvoicesPendingCRC = 0;
        // $totalInvoicesPendingUSD = 0;
        // $totalCommission = 0;
        
        // foreach ($medics as $medic) {
        //     $invoicesTotalMedicCRC = $medic->invoices()->where(function ($query){
        //         $query->where('TipoDocumento', '01')
        //         ->orWhere('TipoDocumento', '04');
        //     })
        //     ->where([['invoices.created_at', '>=', $start->startOfDay()],
        //         ['invoices.created_at', '<=', $end->endOfDay()]])
        //     ->where('status', 1)->where('CodigoMoneda', 'CRC')->sum('TotalWithNota');

        //     $invoicesTotalMedicUSD = $medic->invoices()
        //     ->where(function ($query){
        //         $query->where('TipoDocumento', '01')
        //         ->orWhere('TipoDocumento', '04');
        //     })
        //     ->where([['invoices.created_at', '>=', $start->startOfDay()],
        //         ['invoices.created_at', '<=', $end->endOfDay()]])
        //     ->where('status', 1)->where('CodigoMoneda', 'USD')->sum('TotalWithNota');
            
            
        //     $medic->finishedInvoices = $medic->invoices()
        //     ->where(function ($query){
        //         $query->where('TipoDocumento', '01')
        //         ->orWhere('TipoDocumento', '04');
        //     })
        //     ->where([['invoices.created_at', '>=', $start->startOfDay()],
        //         ['invoices.created_at', '<=', $end->endOfDay()]])
        //     ->where('status', 1)->count();

        //     $totalAppointments += $medic->finishedInvoices;

        //     $invoicesTotalPendingMedicCRC = $medic->invoices()
        //     ->where(function ($query){
        //         $query->where('TipoDocumento', '01')
        //         ->orWhere('TipoDocumento', '04');
        //     })
        //     ->where([['invoices.created_at', '>=', $start->startOfDay()],
        //         ['invoices.created_at', '<=', $end->endOfDay()]])
        //     ->where('status', 0)->where('CodigoMoneda', 'CRC')->sum('TotalWithNota');

        //     $invoicesTotalPendingMedicUSD = $medic->invoices()
        //     ->where(function ($query){
        //         $query->where('TipoDocumento', '01')
        //         ->orWhere('TipoDocumento', '04');
        //     })
        //     ->where([['invoices.created_at', '>=', $start->startOfDay()],
        //         ['invoices.created_at', '<=', $end->endOfDay()]])
        //     ->where('status', 0)->where('CodigoMoneda', 'USD')->sum('TotalWithNota');

        //     $medic->pendingInvoices = $medic->invoices()
        //     ->where(function ($query){
        //         $query->where('TipoDocumento', '01')
        //         ->orWhere('TipoDocumento', '04');
        //     })
        //     ->where([['invoices.created_at', '>=', $start->startOfDay()],
        //         ['invoices.created_at', '<=', $end->endOfDay()]])
        //     ->where('status', 0)->count();

        //     $totalPending += $medic->pendingInvoices;

        //     $medic->invoicesTotalMedicCRC = $invoicesTotalMedicCRC;
        //     $medic->invoicesTotalMedicUSD = $invoicesTotalMedicUSD;
        //     $medic->invoicesTotalPendingMedicCRC = $invoicesTotalPendingMedicCRC;
        //     $medic->invoicesTotalPendingMedicUSD = $invoicesTotalPendingMedicUSD;
        //     $totalInvoicesCRC += $invoicesTotalMedicCRC;
        //     $totalInvoicesUSD += $invoicesTotalMedicUSD;
        //     $totalInvoicesPendingCRC += $invoicesTotalPendingMedicCRC;
        //     $totalInvoicesPendingUSD += $invoicesTotalPendingMedicUSD;
        // }
      
        // $statisticsInvoices = [
        //     'medics' => $medics,
        //     'totalAppointments' => $totalAppointments,
        //     'totalInvoicesCRC' => $totalInvoicesCRC,
        //     'totalInvoicesUSD' => $totalInvoicesUSD,
        //     'totalPending' => $totalPending,
        //     'totalInvoicesPendingCRC' => $totalInvoicesPendingCRC,
        //     'totalInvoicesPendingUSD' => $totalInvoicesPendingUSD,
           
        // ];
        
        return view('assistant.reports.balance', compact('statisticsInvoices','search'));

    }

    
}
