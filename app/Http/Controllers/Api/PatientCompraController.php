<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Patient;
use App\Http\Controllers\Controller;
use App\Invoice;
use Illuminate\Support\Carbon;
class PatientCompraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
      

    }
    
    public function index(Patient $patient)
    {

        $start =  request('start') ? request('start') : Carbon::now()->subMonths(3)->format('Y-m-d');
        $end =  request('end') ? request('end') : Carbon::now()->format('Y-m-d');

      
        //$historialCompras = $this->farmaciaService->getHistorialCompras($patient, null, $start, $end);
        $historialCompras = Invoice::query()
                                    ->with('clinic')
                                    ->where('identificacion_cliente', $patient->ide)
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
                                    ->paginate();

        return $historialCompras;
    }

   
    

}
