<?php

namespace App\Http\Controllers\Clinic;

use App\Commission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Office;
use App\User;
use Carbon\Carbon;
use App\Repositories\MedicRepository;
use App\Invoice;
use Carbon\CarbonPeriod;

class ReportsController extends Controller
{
    function __construct(MedicRepository $medicRepo)
    {
        $this->middleware('auth');
        $this->medicRepo = $medicRepo;

    }

    public function balance()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $search['date'] = request('date') ? request('date') : Carbon::now()->toDateString();
        $search['CodigoActividad'] = request('CodigoActividad');
        
        $start = new Carbon($search['date']);
        $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['date'];
        $end = new Carbon($end);

        $office = auth()->user()->offices->first();
    
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

        
        return view('clinic.reports.balance', compact('statisticsInvoices','search'));

    }

    public function commissionAppointments()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');

        $office = auth()->user()->offices->first();
        
        //$this->medicRepo->findAllByOffice($office->id);
        $officeMedics = $office->users()->whereHas('roles', function($q){ 
            $q->where('name', 'medico');
        })->get();

        $attended = [
            'medics' => [],
            'totalAttended' => 0,
            'totalPending' => 0,
        ];

        if(request('start')){
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);


            $medics = $office->medicsWithIncomes($start, $end);

            if (isset($search['medic']) && $search['medic'] != '') { //si es por clinica y por medico individual
                $medics = $medics->where('id', $search['medic']);
            }


        
            $expedient = [
                'medics' => $medics->count(),
                'monthly_payment' => getAmountPerExpedientUse(),
                'total' => $medics->count() * getAmountPerExpedientUse(),
            ];

            $medicsArray = [];
            $totalAttended = 0;
            $totalPending = 0;
            $totalBilled = 0;
            $totalBilledCommission = 0;

            foreach ($medics as $medic) {

                // $incomesAttented = $medic->incomes()->where('office_id', $office->id)->where([
                //     ['incomes.date', '>=', $start],
                //     ['incomes.date', '<=', $end->endOfDay()]
                // ])->where('type', 'I'); //cobro por cita generada de paciente

               
                
                // $incomesPending = $medic->incomes()->where('office_id', $office->id)->where([
                //     ['incomes.date', '>=', $start],
                //     ['incomes.date', '<=', $end->endOfDay()]
                // ])->where('type', 'P'); //generada por otro usuario (secretaria, clinica, medico)
               
                $totaIncomes = $medic->incomes;

                $incomesAttented = $totaIncomes->filter(function ($item, $key) {
                    return $item->type == 'I';
                });
                
                $incomesPending = $totaIncomes->filter(function ($item, $key) {
                    return $item->type == 'P';
                });
                

                $totalMedicAttended = $incomesAttented->sum('amount');
                $totalMedicPending = $incomesPending->sum('amount');
                

                $medicData = [
                    'id' => $medic->id,
                    'name' => $medic->name,
                    'attented' => $incomesAttented->count(),
                    'attented_amount' => $totalMedicAttended,
                    'pending' => $incomesPending->count(),
                    'pending_amount' => $totalMedicPending,
                    
                ];

                $medicsArray[] = $medicData;

                $totalAttended += $totalMedicAttended;
                $totalPending += $totalMedicPending;
            
            }

            $attended = [
                'medics' => $medicsArray,
                'totalAttended' => $totalAttended,
                'totalPending' => $totalPending,
            ];

        }

    

        return view('clinic.reports.commissionAppointments', compact('attended','search', 'officeMedics'));



    }

    public function commissionBilled()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }
        
        $search['start'] = request('start');
        $search['end'] = request('end');
        $search['medic'] = request('medic');
        $search['CodigoActividad'] = request('CodigoActividad');

        $office = auth()->user()->offices->first();
        //$officeMedics = $this->medicRepo->findAllByOffice($office->id);
        $officeMedics = $office->users()->whereHas('roles', function($q){ 
            $q->where('name', 'medico');
        })->get();

        $billed = [
            'medics' => [],
            'totalBilled' => 0,
            'totalBilledDolar' => 0,
            'totalBilledCommission' => 0,
            'totalBilledCommissionDolar' => 0,
        ];

        if (request('start')) {
            
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);


            $medics = $office->medicsWithIncomes($start, $end);

            if (isset($search['medic']) && $search['medic'] != '') { //si es por clinica y por medico individual
                $medics = $medics->where('id', $search['medic']);
            }


            $expedient = [
                'medics' => $medics->count(),
                'monthly_payment' => getAmountPerExpedientUse(),
                'total' => $medics->count() * getAmountPerExpedientUse(),
            ];
            
            $medicsArray = [];
            $totalBilled = 0;
            $totalBilledDolar = 0;
            $totalBilledCommission = 0;
            $totalBilledCommissionDolar = 0;
           
            foreach ($medics as $medic) {

                // $invoicesBilled = $medic->invoices()->where('office_id', $office->id)->where([
                //     ['invoices.created_at', '>=', $start],
                //     ['invoices.created_at', '<=', $end->endOfDay()]
                // ])->where('status', 1)->get();//->where('appointment_id','<>', 0);

                $invoicesBilled = Invoice::where('user_id', $medic->id)
                ->where(function ($query) {
                    $query->where('TipoDocumento', '01')
                        ->orWhere('TipoDocumento', '04');
                })
                ->where(function ($query) {
                    $query->whereNull('status_fe')
                        ->orWhere('status_fe','<>', 'rechazado');
                })
                ->where('office_id', $office->id)->where([
                        ['invoices.created_at', '>=', $start],
                        ['invoices.created_at', '<=', $end->endOfDay()]
                    ])->where('status', 1)
                    ->where('CodigoActividad', $search['CodigoActividad'])
                    ->get();
                    
                $totalMedicBilled = $invoicesBilled->sum(function ($invoice) {
                    return $invoice->CodigoMoneda == 'CRC' ? $invoice->TotalWithNota : 0;
                });
                $totalMedicBilledDolar = $invoicesBilled->sum(function ($invoice) {
                    return $invoice->CodigoMoneda == 'USD' ? $invoice->TotalWithNota : 0;
                });

                $totalMedicCommissionBilled = $totalMedicBilled * ($medic->commission / 100);
                $totalMedicCommissionBilledDolar = $totalMedicBilledDolar * ($medic->commission / 100);

                $medicData = [
                    'id' => $medic->id,
                    'name' => $medic->name,
                    'commission' => $medic->commission,
                    'billed' => $invoicesBilled->count(),
                    'billed_amount' => $totalMedicBilled,
                    'billed_amount_dolar' => $totalMedicBilledDolar,
                    'billed_commission_amount' => $totalMedicCommissionBilled,
                    'billed_commission_amount_dolar' => $totalMedicCommissionBilledDolar

                ];

                $medicsArray[] = $medicData;

                $totalBilled += $totalMedicBilled;
                $totalBilledDolar += $totalMedicBilledDolar;
                $totalBilledCommission += $totalMedicCommissionBilled;
                $totalBilledCommissionDolar += $totalMedicCommissionBilledDolar;

            }

            $billed = [
                'medics' => $medicsArray,
                'totalBilled' => $totalBilled,
                'totalBilledDolar' => $totalBilledDolar,
                'totalBilledCommission' => $totalBilledCommission,
                'totalBilledCommissionDolar' => $totalBilledCommissionDolar,

            ];
            
        }

       

        return view('clinic.reports.commissionBilled', compact('billed', 'search', 'officeMedics'));



    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sales()
    {
        if (!auth()->user()->hasRole('clinica')) {
            return redirect('/');
        }

        $office = auth()->user()->offices->first();

        $search['user'] = request('user');
        $search['office'] = request('office') ? request('office') : $office->id;
        $search['start'] = request('start') ? request('start') : Carbon::now()->startOfDay()->toDateTimeString();
        $search['end'] = request('end');
        $search['CodigoActividad'] = request('CodigoActividad');
        

        $sales = collect([]);
        $totales = [
            'TotalExonerado' => 0,
            'TotalExoneradoDolar' => 0,
            'TotalExento' => 0,
            'TotalExentoDolar' => 0,
            'TotalGravado' => 0,
            'TotalGravadoDolar' => 0,
            'TotalClinica' => 0,
            'TotalClinicaDolar' => 0,
            'TotalLaboratorio' => 0,
            'TotalLaboratorioDolar' => 0,
            'TotalIVADevuelto' => 0,
            'TotalIVADevueltoDolar' => 0,
            'TotalVentas' => 0,
            'TotalVentasDolar' => 0,
        ];
      
       // dd($search['start']);
        if ($search['start']) {

            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $period = CarbonPeriod::create($start, $end);
         
        
            $invoices = Invoice::where('office_id', $search['office'])->with('lines.taxes', 'notascreditodebito')
                ->where(function ($query) {
                    $query->where('TipoDocumento', '01')
                        ->orWhere('TipoDocumento', '04');
                })
                ->where(function ($query) {
                    $query->whereNull('status_fe')
                        ->orWhere('status_fe','<>', 'rechazado');
                })
                ->where([
                    ['created_at', '>=', $start],
                    ['created_at', '<=', $end->endOfDay()]
                ])->where('status', 1)
                ->where('CodigoActividad', $search['CodigoActividad'])
                ->get();

                
            foreach ($period as $index => $date) {
                //echo $date->format('Y-m-d');

                $TotalExento = 0;
                $TotalExentoDolar = 0;
                $TotalGravado = 0;
                $TotalGravadoDolar = 0;
                $TotalExonerado = 0;
                $TotalExoneradoDolar = 0;
                $TotalIVADevuelto = 0;
                $TotalIVADevueltoDolar = 0;
                $TotalLaboratorio = 0;
                $TotalLaboratorioDolar = 0;
                $TotalClinica = 0;
                $TotalClinicaDolar = 0;

                $invoicesDay = $invoices->filter(function ($item, $key) use($date) {
                    return $item->created_at->toDateString() == $date->format('Y-m-d');
                });
                //dd($invoicesDay);
                foreach ($invoicesDay as $invoice) {
            
            /////////////////////// clinica y laboratorio /////////////////////////////////
                    foreach ($invoice->lines as $line) {

                        if ($line->laboratory) {

                            if ($invoice->CodigoMoneda == 'USD') {
                                $TotalLaboratorioDolar += $line->MontoTotalLinea;
                            } else {
                                $TotalLaboratorio += $line->MontoTotalLinea;
                            }
                        } else {
                            if ($invoice->CodigoMoneda == 'USD') {
                                $TotalClinicaDolar += $line->MontoTotalLinea;
                            } else {
                                $TotalClinica += $line->MontoTotalLinea;
                            }
                        }
                    }            

            //////////////////////////////// excento  y gravado //////////////////////////////////////////
                    if ($invoice->notascreditodebito->count()) { // si tiene notas de debito o credito
                        foreach ($invoice->notascreditodebito as $referencia) { // obtenemos la factura original y la nota
                            if ($referencia->originalInvoice->TotalWithNota > 0) { // si la factura original tiene totalWithNota  en 0, es por que esta anulada
                             
                                if($referencia->invoice->CodigoMoneda == 'USD'){
                                    $TotalExentoDolar += $referencia->invoice->TotalExento;
                                    $TotalGravadoDolar += $referencia->invoice->TotalGravado;
                                    $TotalExoneradoDolar += $referencia->invoice->TotalExonerado;
                                    $TotalIVADevueltoDolar += $referencia->invoice->TotalIVADevuelto;
                                   
                                }else{
                                    $TotalExento += $referencia->invoice->TotalExento;
                                    $TotalGravado += $referencia->invoice->TotalGravado;
                                    $TotalExonerado += $referencia->invoice->TotalExonerado;
                                    $TotalIVADevuelto += $referencia->invoice->TotalIVADevuelto;
                                    
                                }
                            }
                        }
                    } else { // si no tiene notas de debito o credito

                        if($invoice->CodigoMoneda == 'USD'){
                            $TotalExentoDolar += $invoice->TotalExento;
                            $TotalGravadoDolar += $invoice->TotalGravado;
                            $TotalExoneradoDolar += $invoice->TotalExonerado;
                            $TotalIVADevueltoDolar += $invoice->TotalIVADevuelto;
                           
                        }else{
                            $TotalExento += $invoice->TotalExento;
                            $TotalGravado += $invoice->TotalGravado;
                            $TotalExonerado += $invoice->TotalExonerado;
                            $TotalIVADevuelto += $invoice->TotalIVADevuelto;
                           
                        }
                        

                    }
                }

                $TotalVentasDolar = $invoicesDay->sum(function ($invoice) use($date) {
                    return $invoice->CodigoMoneda == 'USD' ? $invoice->TotalWithNota : 0;
                });
                $TotalVentas = $invoicesDay->sum(function ($invoice) use ($date) {
                    return $invoice->CodigoMoneda == 'CRC' ? $invoice->TotalWithNota : 0;
                });

                $totalesDay = [
                    'date' => $date->format('Y-m-d'),
                    'TotalExonerado' => $TotalExonerado,
                    'TotalExoneradoDolar' => $TotalExoneradoDolar,
                    'TotalExento' => $TotalExento,
                    'TotalExentoDolar' => $TotalExentoDolar,
                    'TotalGravado' => $TotalGravado,
                    'TotalGravadoDolar' => $TotalGravadoDolar,
                    'TotalClinica' => $TotalClinica,
                    'TotalClinicaDolar' => $TotalClinicaDolar,
                    'TotalLaboratorio' => $TotalLaboratorio,
                    'TotalLaboratorioDolar' => $TotalLaboratorioDolar,
                    'TotalIVADevuelto' => $TotalIVADevuelto,
                    'TotalIVADevueltoDolar' => $TotalIVADevueltoDolar,
                    'TotalVentas' => $TotalVentas,
                    'TotalVentasDolar' => $TotalVentasDolar
                ];

               
                $sales[] = (object)$totalesDay;

                
            }


             $totales = (object)[
                    'TotalExonerado' => $sales->sum('TotalExonerado'),
                    'TotalExoneradoDolar' => $sales->sum('TotalExoneradoDolar'),
                    'TotalExento' => $sales->sum('TotalExento'),
                    'TotalExentoDolar' => $sales->sum('TotalExentoDolar'),
                    'TotalGravado' => $sales->sum('TotalGravado'),
                    'TotalGravadoDolar' => $sales->sum('TotalGravadoDolar'),
                    'TotalClinica' => $sales->sum('TotalClinica'),
                    'TotalClinicaDolar' => $sales->sum('TotalClinicaDolar'),
                    'TotalLaboratorio' => $sales->sum('TotalLaboratorio'),
                    'TotalLaboratorioDolar' => $sales->sum('TotalLaboratorioDolar'),
                    'TotalIVADevuelto' => $sales->sum('TotalIVADevuelto'),
                    'TotalIVADevueltoDolar' => $sales->sum('TotalIVADevueltoDolar'),
                    'TotalVentas' => $sales->sum('TotalVentas'),
                    'TotalVentasDolar' => $sales->sum('TotalVentasDolar'),
                ];

                $impuestosVentas = $this->getImpuestosVentas($search);
        }


        if (request('print')) {
            $sales = $sales;
            return view('clinic.reports.salesPrint', compact('sales', 'totales', 'impuestosVentas'));
        }

        $sales = $sales;

        return view('clinic.reports.sales', compact('sales', 'totales', 'search', 'impuestosVentas'));
    }

    public function getImpuestosVentas($search)
    {


        
        $start = new Carbon($search['start']);
        $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
        $end = new Carbon($end);
        $codigoActividad = $search['CodigoActividad'];

        $idsWithNota = \DB::table('invoices')
            ->join('referencias', 'invoices.id', '=', 'referencias.referencia_id')
            ->join('invoices as notas', 'referencias.invoice_id', '=', 'notas.id')
            ->select(\DB::raw('invoices.id as IdFacturaOriginal, notas.id as IdNota, IF(invoices.totalWithNota <= 0 and (invoices.totalWithNota <> invoices.TotalComprobante), 1, 0) as AnuladaCompletamente'))
            ->where(function ($query) {
                $query->whereNull('invoices.status_fe')
                    ->orWhere('invoices.status_fe','<>', 'rechazado');
            })
            ->where('invoices.CodigoActividad', $codigoActividad)
            ->where('invoices.status', 1)
            ->whereBetween('invoices.created_at', [$start, $end->endOfDay()])->get();

      
        $idsFacturasAnuladas = $idsWithNota->pluck('IdFacturaOriginal');
        $idsNotasDeFacturasModificadas = $idsWithNota->where('AnuladaCompletamente', 0)->pluck('IdNota');
      
        //dd($idsNotasDeFacturasModificadas);


        $impuestosVentas = \DB::table('invoices')
        ->join('invoice_lines', 'invoices.id', '=', 'invoice_lines.invoice_id')
        ->leftJoin('invoice_line_taxes', 'invoice_lines.id', '=', 'invoice_line_taxes.invoice_line_id')
        ->select(\DB::raw('IFNUll(invoice_line_taxes.CodigoTarifa, "00") as codTarifa, SUM(invoice_lines.SubTotal) as TotalGravadoDesc, SUM(invoice_lines.MontoTotalLinea) as TotalVentas, SUM(invoice_line_taxes.ImpuestoNeto) as TotalImpuesto, SUM(IF(invoices.MedioPago = "02" AND invoice_lines.is_servicio_medico = 1 AND invoice_lines.type = "S", invoice_line_taxes.ImpuestoNeto, 0)) as TotalIVADevuelto, invoices.CodigoMoneda'))
        ->whereNotIn('invoices.id', $idsFacturasAnuladas)
        ->where(function ($query) use ($idsNotasDeFacturasModificadas) {
            $query->where('invoices.TipoDocumento',  '01')
                  ->orWhere('invoices.TipoDocumento', '04')
                  ->orWhereIn('invoices.id', $idsNotasDeFacturasModificadas);
        })
        ->where(function ($query) {
            $query->whereNull('status_fe')
                ->orWhere('status_fe','<>', 'rechazado');
        })
        ->where('invoices.CodigoActividad', $codigoActividad)
        ->where('invoices.status', 1)
        ->whereBetween('invoices.created_at', [$start, $end->endOfDay()])
        ->groupBy('codTarifa','invoices.CodigoMoneda')->get();

        //dd($impuestosVentas);

     

        $data = [
            'impuestosVentas' => $impuestosVentas
            
        ];
      
       

        return $data;
        
    }

    
}
