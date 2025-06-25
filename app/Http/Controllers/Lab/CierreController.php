<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cierre;
use App\Invoice;
use App\Payment;
use App\User;
use Carbon\Carbon;
use App\Repositories\MedicRepository;

class CierreController extends Controller
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

        $office = auth()->user()->offices->first();

        $search['user'] = request('user');
        $search['office'] = request('office') ? request('office') : $office->id;
        $search['start'] = request('start') ? request('start') : Carbon::now()->startOfDay()->toDateTimeString();
        $search['end'] = request('end');
        $search['to'] = request('to') ? request('to') : Carbon::now()->toDateTimeString();
        $codigoActividad = $search['CodigoActividad'] = request('CodigoActividad');
        $search['archived'] = request('archived');

        $cierres = collect([]);
        $totales = [
            'TotalCredito' => 0,
            'TotalCreditoDolar' => 0,
            'TotalContado' => 0,
            'TotalContadoDolar' => 0,
            'TotalEfectivo' => 0,
            'TotalEfectivoDolar' => 0,
            'TotalTarjeta' => 0,
            'TotalTarjetaDolar' => 0,
            'TotalClinica' => 0,
            'TotalClinicaDolar' => 0,
            'TotalLaboratorio' => 0,
            'TotalLaboratorioDolar' => 0,
            'TotalVentas' => 0,
            'TotalVentasDolar' => 0,
            'TotalIVADevuelto' => 0,
            'TotalIVADevueltoDolar' => 0,
            'TotalGravado' => 0,
            'TotalGravadoDolar' => 0,
            'TotalExento' => 0,
            'TotalExentoDolar' => 0,
            'TotalExonerado' => 0,
            'TotalExoneradoDolar' => 0,
            'TotalDescuento' => 0,
            'TotalDescuentoDolar' => 0,
            'TotalImpuesto' => 0,
            'TotalImpuestoDolar' => 0,
            'TotalCxc' => 0,
            'TotalCxcDolar' => 0,
            'SubtotalVentas' => 0,
            'SubtotalVentasDolar' => 0,
            'TotalAbonos' => 0,
            'TotalAbonosDolar' => 0,
            'TotalVentasNeta' => 0,
            'TotalVentasNetaDolar' => 0,
            'TotalPayments' => 0,
            'TotalPaymentsDolar' => 0,
            'TotalPaymentsCurrentCxcs' => 0,
            'TotalPaymentsCurrentCxcsDolar' => 0
        ];

        if ($search['start']) {

            $cierresTotales = Cierre::search($search)->with('user', 'office')->getQuery()
                ->selectRaw('sum(TotalCredito) as TotalCredito')
                ->selectRaw("sum(TotalCreditoDolar) as TotalCreditoDolar")
                ->selectRaw("sum(TotalContado) as TotalContado")
                ->selectRaw("sum(TotalContadoDolar) as TotalContadoDolar")
                ->selectRaw("sum(TotalEfectivo) as TotalEfectivo")
                ->selectRaw("sum(TotalEfectivoDolar) as TotalEfectivoDolar")
                ->selectRaw("sum(TotalTarjeta) as TotalTarjeta")
                ->selectRaw("sum(TotalTarjetaDolar) as TotalTarjetaDolar")
                ->selectRaw("sum(TotalCheque) as TotalCheque")
                ->selectRaw("sum(TotalChequeDolar) as TotalChequeDolar")
                ->selectRaw("sum(TotalDeposito) as TotalDeposito")
                ->selectRaw("sum(TotalDepositoDolar) as TotalDepositoDolar")
                ->selectRaw("sum(TotalClinica) as TotalClinica")
                ->selectRaw("sum(TotalClinicaDolar) as TotalClinicaDolar")
                ->selectRaw("sum(TotalLaboratorio) as TotalLaboratorio")
                ->selectRaw("sum(TotalLaboratorioDolar) as TotalLaboratorioDolar")
                ->selectRaw("sum(TotalVentas) as TotalVentas")
                ->selectRaw("sum(TotalVentasDolar) as TotalVentasDolar")
                ->selectRaw("sum(TotalGravado) as TotalGravado")
                ->selectRaw("sum(TotalGravadoDolar) as TotalGravadoDolar")
                ->selectRaw("sum(TotalExento) as TotalExento")
                ->selectRaw("sum(TotalExentoDolar) as TotalExentoDolar")
                ->selectRaw("sum(TotalFacturas) as TotalFacturas")
                ->selectRaw("sum(TotalIVADevuelto) as TotalIVADevuelto")
                ->selectRaw("sum(TotalIVADevueltoDolar) as TotalIVADevueltoDolar")
                ->selectRaw("sum(TotalExonerado) as TotalExonerado")
                ->selectRaw("sum(TotalExoneradoDolar) as TotalExoneradoDolar")
                ->selectRaw("sum(TotalImpuesto) as TotalImpuesto")
                ->selectRaw("sum(TotalImpuestoDolar) as TotalImpuestoDolar")
                ->selectRaw("sum(TotalDescuento) as TotalDescuento")
                ->selectRaw("sum(TotalDescuentoDolar) as TotalDescuentoDolar")
                ->selectRaw("sum(TotalPayments) as TotalPayments")
                ->selectRaw("sum(TotalPaymentsDolar) as TotalPaymentsDolar")
                ->selectRaw("sum(TotalCxc) as TotalCxc")
                ->selectRaw("sum(TotalCxcDolar) as TotalCxcDolar")
                ->selectRaw("sum(TotalPaymentsCurrentCxcs) as TotalPaymentsCurrentCxcs")
                ->selectRaw("sum(TotalPaymentsCurrentCxcsDolar) as TotalPaymentsCurrentCxcsDolar")
                ->selectRaw("sum(TotalAbonos) as TotalAbonos")
                ->selectRaw("sum(TotalAbonosDolar) as TotalAbonosDolar")
                ->first();

            $cierres = Cierre::search($search)->with('user', 'office');

            $totales = [
                'TotalFacturas' => $cierresTotales->TotalFacturas,
                'TotalCredito' => $cierresTotales->TotalCredito,
                'TotalCreditoDolar' => $cierresTotales->TotalCreditoDolar,
                'TotalContado' => $cierresTotales->TotalContado,
                'TotalContadoDolar' => $cierresTotales->TotalContadoDolar,
                'TotalEfectivo' => $cierresTotales->TotalEfectivo,
                'TotalEfectivoDolar' => $cierresTotales->TotalEfectivoDolar,
                'TotalTarjeta' => $cierresTotales->TotalTarjeta,
                'TotalTarjetaDolar' => $cierresTotales->TotalTarjetaDolar,
                'TotalCheque' => $cierresTotales->TotalCheque,
                'TotalChequeDolar' => $cierresTotales->TotalChequeDolar,
                'TotalDeposito' => $cierresTotales->TotalDeposito,
                'TotalDepositoDolar' => $cierresTotales->TotalDepositoDolar,
                'TotalClinica' => $cierresTotales->TotalClinica,
                'TotalClinicaDolar' => $cierresTotales->TotalClinicaDolar,
                'TotalLaboratorio' => $cierresTotales->TotalLaboratorio,
                'TotalLaboratorioDolar' => $cierresTotales->TotalLaboratorioDolar,
                'TotalVentas' => $cierresTotales->TotalVentas,
                'TotalVentasDolar' => $cierresTotales->TotalVentasDolar,
                'TotalIVADevuelto' => $cierresTotales->TotalIVADevuelto,
                'TotalIVADevueltoDolar' => $cierresTotales->TotalIVADevueltoDolar,
                'TotalGravado' => $cierresTotales->TotalGravado,
                'TotalGravadoDolar' => $cierresTotales->TotalGravadoDolar,
                'TotalExento' => $cierresTotales->TotalExento,
                'TotalExentoDolar' => $cierresTotales->TotalExentoDolar,
                'TotalExonerado' => $cierresTotales->TotalExonerado,
                'TotalExoneradoDolar' => $cierresTotales->TotalExoneradoDolar,
                'TotalDescuento' => $cierresTotales->TotalDescuento,
                'TotalDescuentoDolar' => $cierresTotales->TotalDescuentoDolar,
                'TotalImpuesto' => $cierresTotales->TotalImpuesto,
                'TotalImpuestoDolar' => $cierresTotales->TotalImpuestoDolar,
                'TotalCxc' => $cierresTotales->TotalCxc,
                'TotalCxcDolar' => $cierresTotales->TotalCxcDolar,
                'SubtotalVentas' => $cierresTotales->TotalVentas - $cierresTotales->TotalCxc,
                'SubtotalVentasDolar' => $cierresTotales->TotalVentasDolar - $cierresTotales->TotalCxcDolar,
                'TotalAbonos' =>  $cierresTotales->TotalAbonos,
                'TotalAbonosDolar' => $cierresTotales->TotalAbonosDolar,
                'TotalVentasNeta' => ($cierresTotales->TotalVentas - $cierresTotales->TotalCxc) + $cierresTotales->TotalAbonos,
                'TotalVentasNetaDolar' => ($cierresTotales->TotalVentasDolar - $cierresTotales->TotalCxcDolar) + $cierresTotales->TotalAbonosDolar,
                'TotalPayments' => $cierresTotales->TotalPayments,
                'TotalPaymentsDolar' => $cierresTotales->TotalPaymentsDolar,
                'TotalPaymentsCurrentCxcs' => $cierresTotales->TotalPaymentsCurrentCxcs,
                'TotalPaymentsCurrentCxcsDolar' => $cierresTotales->TotalPaymentsCurrentCxcsDolar
            ];

            $impuestosVentasCompras = $this->getImpuestosVentasCompras($search);
        }



        if (request('print')) {
            $cierres = $cierres->get();
            return view('lab.cierres.print', compact('cierres', 'totales', 'impuestosVentasCompras', 'codigoActividad'));
        }

        $cierres = $cierres->paginate(500);

        return view('lab.cierres.index', compact('cierres', 'totales', 'search', 'impuestosVentasCompras'));
    }

    public function getImpuestosVentasCompras($search)
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
                    ->orWhere('invoices.status_fe', '<>', 'rechazado');
            })
            ->where('invoices.CodigoActividad', $codigoActividad)
            ->where('invoices.status', 1)
            ->whereBetween('invoices.created_at', [$start, $end->endOfDay()])->get();


        $idsFacturasAnuladas = $idsWithNota->pluck('IdFacturaOriginal');
        $idsNotasDeFacturasModificadas = $idsWithNota->where('AnuladaCompletamente', 0)->pluck('IdNota');

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
                    ->orWhere('status_fe', '<>', 'rechazado');
            })
            ->where('invoices.CodigoActividad', $codigoActividad)
            ->where('invoices.status', 1)
            ->whereBetween('invoices.created_at', [$start, $end->endOfDay()])
            ->groupBy('codTarifa', 'invoices.CodigoMoneda')->get();




        $data = [
            'impuestosVentas' => $impuestosVentas

        ];



        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $officeId = request('office');
        $codigoActividad = request('CodigoActividad');
        $medics = User::whereHas('createdInvoices', function ($query) use ($officeId, $codigoActividad) {

            $query->where('office_id', $officeId)
                ->where('CodigoActividad', $codigoActividad);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'medico')
                ->orWhere('name', 'clinica')
                ->orWhere('name', 'laboratorio')
                ->orWhere('name', 'asistente');
        })->where('active', 1)->get();

        // archived lasts cierrres
        Cierre::where('office_id', $officeId)
            ->where('CodigoActividad', $codigoActividad)
            ->update([
                'archived_at' => now()
            ]);

        foreach ($medics as $user) {

            $this->generateCierre($user, $officeId, $codigoActividad);
        }



        flash('Cierre Generado', 'success');

        return redirect('/lab/cierres?start=' . Carbon::parse(request('to'))->toDateString() . '&CodigoActividad=' . request('CodigoActividad'));
    }
    public function generateCierre($user, $officeId, $codigoActividad)
    {
        $lastCierre = Cierre::where('user_id', $user->id)->where('office_id', $officeId)->where('CodigoActividad', $codigoActividad)->latest()->first();
        $dateLastCierre = Carbon::now()->startOfDay();

        if ($lastCierre) {
            $dateLastCierre = $lastCierre->to;
        } else {

            $lastInvoice = Invoice::where('created_by', $user->id)->where('office_id', $officeId)->where('CodigoActividad', $codigoActividad)->orderBy('created_at', 'ASC')->first();

            if ($lastInvoice) {
                $dateLastCierre = $lastInvoice->created_at;
            } else {
                $dateLastCierre = Carbon::now()->startOfDay();
            }
        }

        $dateEndCierre = request('to') ? Carbon::parse(request('to')) : Carbon::now();

        $invoices = Invoice::where('office_id', $officeId)->with('lines.taxes', 'notascreditodebito')
            ->where(function ($query) {
                $query->where('TipoDocumento', '01')
                    ->orWhere('TipoDocumento', '04');
            })
            ->where(function ($query) {
                $query->whereNull('status_fe')
                    ->orWhere('status_fe', '<>', 'rechazado');
            })
            ->where([
                ['created_at', '>=', $dateLastCierre],
                ['created_at', '<=', $dateEndCierre]
            ])->where('created_by', $user->id)
            ->where('CodigoActividad', $codigoActividad)
            ->where('status', 1)->get();

        // dd($dateLastCierre . '---'. $dateEndCierre);

        $TotalContado = $invoices->sum(function ($invoice) {
            return ($invoice->CondicionVenta == '01' && $invoice->CodigoMoneda == 'CRC') ? $invoice->TotalWithNota : 0;
        });
        $TotalContadoDolar = $invoices->sum(function ($invoice) {
            return ($invoice->CondicionVenta == '01' && $invoice->CodigoMoneda == 'USD') ? $invoice->TotalWithNota : 0;
        });
        $TotalCredito = $invoices->sum(function ($invoice) {
            return ($invoice->CondicionVenta == '02' && $invoice->CodigoMoneda == 'CRC') ? $invoice->TotalWithNota : 0;
        });
        $TotalCreditoDolar = $invoices->sum(function ($invoice) {
            return ($invoice->CondicionVenta == '02' && $invoice->CodigoMoneda == 'USD') ? $invoice->TotalWithNota : 0;
        });
        $TotalEfectivo = $invoices->sum(function ($invoice) {
            return ($invoice->MedioPago == '01' && $invoice->CodigoMoneda == 'CRC') ? $invoice->TotalWithNota : 0;
        });
        $TotalEfectivoDolar = $invoices->sum(function ($invoice) {
            return ($invoice->MedioPago == '01' && $invoice->CodigoMoneda == 'USD') ? $invoice->TotalWithNota : 0;
        });
        $TotalTarjeta = $invoices->sum(function ($invoice) {
            return ($invoice->MedioPago == '02' && $invoice->CodigoMoneda == 'CRC') ? $invoice->TotalWithNota : 0;
        });
        $TotalTarjetaDolar = $invoices->sum(function ($invoice) {
            return ($invoice->MedioPago == '02' && $invoice->CodigoMoneda == 'USD') ? $invoice->TotalWithNota : 0;
        });
        $TotalCheque = $invoices->sum(function ($invoice) {
            return ($invoice->MedioPago == '03' && $invoice->CodigoMoneda == 'CRC') ? $invoice->TotalWithNota : 0;
        });
        $TotalChequeDolar = $invoices->sum(function ($invoice) {
            return ($invoice->MedioPago == '03' && $invoice->CodigoMoneda == 'USD') ? $invoice->TotalWithNota : 0;
        });
        $TotalDeposito = $invoices->sum(function ($invoice) {
            return ($invoice->MedioPago == '04' && $invoice->CodigoMoneda == 'CRC') ? $invoice->TotalWithNota : 0;
        });
        $TotalDepositoDolar = $invoices->sum(function ($invoice) {
            return ($invoice->MedioPago == '04' && $invoice->CodigoMoneda == 'USD') ? $invoice->TotalWithNota : 0;
        });

        // $TotalExento = $invoices->sum(function ($invoice) {
        //     return $invoice->TotalExento;
        // });
        // $TotalGravado = $invoices->sum(function ($invoice) {
        //     return $invoice->TotalGravado;
        // });

        $TotalExento = 0;
        $TotalExentoDolar = 0;
        $TotalGravado = 0;
        $TotalGravadoDolar = 0;
        $TotalExonerado = 0;
        $TotalExoneradoDolar = 0;
        $TotalIVADevuelto = 0;
        $TotalIVADevueltoDolar = 0;
        $TotalImpuesto = 0;
        $TotalImpuestoDolar = 0;
        $TotalDescuento = 0;
        $TotalDescuentoDolar = 0;
        $TotalLaboratorio = 0;
        $TotalLaboratorioDolar = 0;
        $TotalClinica = 0;
        $TotalClinicaDolar = 0;
        $TotalPaymentsCurrentCxcs = 0;
        $TotalPaymentsCurrentCxcsDolar = 0;

        foreach ($invoices as $invoice) {



            //////////////////////////////// excento  y gravado //////////////////////////////////////////
            if ($invoice->notascreditodebito->count()) { // si tiene notas de debito o credito
                foreach ($invoice->notascreditodebito as $referencia) { // obtenemos la factura original y la nota
                    if ($referencia->originalInvoice->TotalWithNota > 0) { // si la factura original tiene totalWithNota  en 0, es por que esta anulada
                        // foreach ($referencia->invoice->lines as $line) { // obtenemos las lineas de la nota de credito o debito para obtener los montos de linea
                        //     if ($line->taxes->count()) {
                        //         if($invoice->CodigoMoneda == 'USD'){
                        //             $TotalGravadasDolarConDesc += $line->MontoTotalLinea;
                        //         }else{
                        //             $TotalGravadasConDesc += $line->MontoTotalLinea;
                        //         }
                        //     } else {
                        //         if ($invoice->CodigoMoneda == 'USD') {
                        //             $TotalExentasDolarConDesc += $line->MontoTotalLinea;
                        //         }else{
                        //             $TotalExentasConDesc += $line->MontoTotalLinea;
                        //         }
                        //     }


                        // }
                        /////////////////////// clinica y laboratorio /////////////////////////////////
                        foreach ($referencia->invoice->lines as $line) {

                            if ($line->laboratory) {

                                if ($referencia->invoice->CodigoMoneda == 'USD') {
                                    $TotalLaboratorioDolar += $line->MontoTotalLinea;
                                } else {
                                    $TotalLaboratorio += $line->MontoTotalLinea;
                                }
                            } else {
                                if ($referencia->invoice->CodigoMoneda == 'USD') {
                                    $TotalClinicaDolar += $line->MontoTotalLinea;
                                } else {
                                    $TotalClinica += $line->MontoTotalLinea;
                                }
                            }
                        }
                        if ($referencia->invoice->CodigoMoneda == 'USD') {
                            $TotalExentoDolar += $referencia->invoice->TotalExento;
                            $TotalGravadoDolar += $referencia->invoice->TotalGravado;
                            $TotalExoneradoDolar += $referencia->invoice->TotalExonerado;
                            $TotalIVADevueltoDolar += $referencia->invoice->TotalIVADevuelto;
                            $TotalDescuentoDolar += $referencia->invoice->TotalDescuentos;
                            $TotalImpuestoDolar += $referencia->invoice->TotalImpuesto - $referencia->invoice->TotalIVADevuelto;
                        } else {
                            $TotalExento += $referencia->invoice->TotalExento;
                            $TotalGravado += $referencia->invoice->TotalGravado;
                            $TotalExonerado += $referencia->invoice->TotalExonerado;
                            $TotalIVADevuelto += $referencia->invoice->TotalIVADevuelto;
                            $TotalDescuento += $referencia->invoice->TotalDescuentos;
                            $TotalImpuesto += $referencia->invoice->TotalImpuesto - $referencia->invoice->TotalIVADevuelto;
                        }
                    }
                }
            } else { // si no tiene notas de debito o credito

                if ($invoice->CodigoMoneda == 'USD') {
                    $TotalExentoDolar += $invoice->TotalExento;
                    $TotalGravadoDolar += $invoice->TotalGravado;
                    $TotalExoneradoDolar += $invoice->TotalExonerado;
                    $TotalIVADevueltoDolar += $invoice->TotalIVADevuelto;
                    $TotalDescuentoDolar += $invoice->TotalDescuentos;
                    $TotalImpuestoDolar += $invoice->TotalImpuesto - $invoice->TotalIVADevuelto;
                } else {
                    $TotalExento += $invoice->TotalExento;
                    $TotalGravado += $invoice->TotalGravado;
                    $TotalExonerado += $invoice->TotalExonerado;
                    $TotalIVADevuelto += $invoice->TotalIVADevuelto;
                    $TotalDescuento += $invoice->TotalDescuentos;
                    $TotalImpuesto += $invoice->TotalImpuesto - $invoice->TotalIVADevuelto;
                }
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
            }

            $TotalPaymentsCurrentCxcs += $invoice->payments()
                ->where([
                    ['created_at', '>=', $dateLastCierre],
                    ['created_at', '<=', $dateEndCierre]
                ])
                ->where('user_id', $user->id)
                ->where('office_id', $officeId)
                ->where('CodigoMoneda', 'CRC')->sum('amount');

            $TotalPaymentsCurrentCxcsDolar += $invoice->payments()
                ->where([
                    ['created_at', '>=', $dateLastCierre],
                    ['created_at', '<=', $dateEndCierre]
                ])
                ->where('user_id', $user->id)
                ->where('office_id', $officeId)
                ->where('CodigoMoneda', 'USD')->sum('amount');
        }

        $TotalVentasDolar = $invoices->sum(function ($invoice) {
            return $invoice->CodigoMoneda == 'USD' ? $invoice->TotalWithNota : 0;
        });
        $TotalVentas = $invoices->sum(function ($invoice) {
            return $invoice->CodigoMoneda == 'CRC' ? $invoice->TotalWithNota : 0;
        });

        $TotalCxcDolar = $invoices->sum(function ($invoice) {
            return ($invoice->CondicionVenta == '02' && $invoice->CodigoMoneda == 'USD' && $invoice->TotalWithNota > 1) ? $invoice->cxc_pending_amount : 0;
        });
        $TotalCxc = $invoices->sum(function ($invoice) {
            return ($invoice->CondicionVenta == '02' && $invoice->CodigoMoneda == 'CRC' && $invoice->TotalWithNota > 1) ? $invoice->cxc_pending_amount : 0;
        });

        $payments = Payment::where([
            ['created_at', '>=', $dateLastCierre],
            ['created_at', '<=', $dateEndCierre]
        ])->where('user_id', $user->id)
            ->whereHas('invoice', function ($query) use ($codigoActividad, $officeId) {
                $query->where('CodigoActividad', $codigoActividad)
                    ->where('office_id', $officeId);
            })
            ->get();

        $TotalPayments = $payments->sum(function ($payment) {
            return $payment->CodigoMoneda == 'CRC' ? $payment->amount : 0;
        });
        $TotalPaymentsDolar = $payments->sum(function ($payment) {
            return $payment->CodigoMoneda == 'USD' ? $payment->amount : 0;
        });

        $diferenciaAbonos = $TotalPayments - $TotalPaymentsCurrentCxcs;
        $diferenciaAbonosDolar = $TotalPaymentsDolar - $TotalPaymentsCurrentCxcsDolar;
        $TotalAbonos = ($diferenciaAbonos > 0) ? $diferenciaAbonos : 0;
        $TotalAbonosDolar = ($diferenciaAbonosDolar > 0) ? $diferenciaAbonosDolar : 0;

        $observations = null;

        if ($diferenciaAbonos < 0 || $diferenciaAbonosDolar < 0) {
            $observations = "El total de abonos del dia es menor que los abonos a cxc del dia.";
        }

        $invoicesPending = Invoice::where('office_id', $officeId)->with('lines.taxes', 'notascreditodebito')
            ->where(function ($query) {
                $query->where('TipoDocumento', '01')
                    ->orWhere('TipoDocumento', '04');
            })
            ->where([
                ['created_at', '>=', $dateLastCierre],
                ['created_at', '<=', $dateEndCierre]
            ])->where('user_id', $user->id)
            ->where('CodigoActividad', $codigoActividad)
            ->where('status', 0)->get();


        $TotalVentasPendientesDolar = $invoicesPending->sum(function ($invoice) {
            return $invoice->CodigoMoneda == 'USD' ? $invoice->TotalWithNota : 0;
        });
        $TotalVentasPendientes = $invoicesPending->sum(function ($invoice) {
            return $invoice->CodigoMoneda == 'CRC' ? $invoice->TotalWithNota : 0;
        });

        $TotalFacturas = $invoices->count();


        $cierre = null;

        if ($TotalVentas > 0 || $TotalVentasDolar > 0 || $TotalAbonos || $TotalAbonosDolar) {

            $cierre = Cierre::create([
                'office_id' => $officeId,
                'user_id' => $user->id,
                'created_by' => auth()->user()->id,
                'Facturas_finalizadas' => $invoices->count(),
                'Facturas_pendientes' => $invoicesPending->count(),
                'TotalContado' => $TotalContado,
                'TotalContadoDolar' => $TotalContadoDolar,
                'TotalCredito' => $TotalCredito,
                'TotalCreditoDolar' => $TotalCreditoDolar,
                'TotalEfectivo' => $TotalEfectivo,
                'TotalEfectivoDolar' => $TotalEfectivoDolar,
                'TotalTarjeta' => $TotalTarjeta,
                'TotalTarjetaDolar' => $TotalTarjetaDolar,
                'TotalVentas' => $TotalVentas,
                'TotalVentasDolar' => $TotalVentasDolar,
                'TotalCheque' => $TotalCheque,
                'TotalChequeDolar' => $TotalChequeDolar,
                'TotalDeposito' => $TotalDeposito,
                'TotalDepositoDolar' => $TotalDepositoDolar,
                'TotalExento' => $TotalExento,
                'TotalExentoDolar' => $TotalExentoDolar,
                'TotalGravado' => $TotalGravado,
                'TotalGravadoDolar' => $TotalGravadoDolar,
                'TotalExonerado' => $TotalExonerado,
                'TotalExoneradoDolar' => $TotalExoneradoDolar,
                'TotalIVADevuelto' => $TotalIVADevuelto,
                'TotalIVADevueltoDolar' => $TotalIVADevueltoDolar,
                'TotalFacturas' => $TotalFacturas,
                'TotalImpuesto' => $TotalImpuesto,
                'TotalImpuestoDolar' => $TotalImpuestoDolar,
                'TotalDescuento' => $TotalDescuento,
                'TotalDescuentoDolar' => $TotalDescuentoDolar,
                'TotalClinica' => $TotalClinica,
                'TotalClinicaDolar' => $TotalClinicaDolar,
                'TotalLaboratorio' => $TotalLaboratorio,
                'TotalLaboratorioDolar' => $TotalLaboratorioDolar,
                'TotalVentasPendientes' => $TotalVentasPendientes,
                'TotalVentasPendientesDolar' => $TotalVentasPendientesDolar,
                'TotalCxc' => $TotalCxc,
                'TotalCxcDolar' => $TotalCxcDolar,
                'TotalPayments' => $TotalPayments,
                'TotalPaymentsDolar' => $TotalPaymentsDolar,
                'TotalPaymentsCurrentCxcs' => $TotalPaymentsCurrentCxcs,
                'TotalPaymentsCurrentCxcsDolar' => $TotalPaymentsCurrentCxcsDolar,
                'TotalAbonos' => $TotalAbonos,
                'TotalAbonosDolar' => $TotalAbonosDolar,
                'from' => $dateLastCierre,
                'to' => $dateEndCierre,
                'CodigoActividad' => $codigoActividad,
                'observations' => $observations

            ]);
        }

        return $cierre;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cierre $cierre)
    {
        $this->authorize('update', $cierre);

        $cierre->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return Redirect('/lab/cierres');
    }
}
