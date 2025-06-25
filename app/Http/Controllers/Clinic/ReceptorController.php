<?php

namespace App\Http\Controllers\Clinic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Patient;
use App\Receptor;

class ReceptorController extends Controller
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
        
        if(!auth()->user()->hasRole('clinica')){
            return redirect('/');
        }

        $search['q'] = request('q');
        $search['start'] = request('start');
        $search['end'] = request('end');

        $obligadoTributario = auth()->user()->getObligadoTributario();

        if (!$obligadoTributario) {
            return redirect('/');
        }

        $receptors = Receptor::where('obligado_tributario_id', $obligadoTributario->id)->search($search);//auth()->user()->receptors()->search($search)->latest()->paginate(10);
        $receptorsTotales = clone $receptors;

        $totales = $receptorsTotales
        ->selectRaw('SUM(IF(receptors.CodigoMoneda = "CRC", receptors.TotalFactura, 0)) as totalFacturaCRC, SUM(IF(receptors.CodigoMoneda = "USD", receptors.TotalFactura, 0)) as totalFacturaUSD, SUM(IF(receptors.CodigoMoneda = "CRC", receptors.MontoTotalImpuesto, 0)) as totalImpuestosCRC, SUM(IF(receptors.CodigoMoneda = "USD", receptors.MontoTotalImpuesto, 0)) as totalImpuestosUSD')
        ->first();

        $receptors = $receptors->latest()->paginate(10);


        return view('clinic.receptors.index', compact('receptors', 'search', 'totales'));
    }

  
}
