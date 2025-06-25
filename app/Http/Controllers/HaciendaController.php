<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\User;
use App\Notifications\HaciendaNotification;
use App\Receptor;
use Illuminate\Support\Facades\Storage;
use App\Services\FacturaService;
use App\Mail\SendInvoice;
use NumberFormatter;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class HaciendaController extends Controller
{
    public function __construct(FacturaService $facturaService)
    {
        //$this->middleware('auth');
        $this->facturaService = $facturaService;
    }

    // public function authToken(User $user)
    // {
    //     return json_encode($this->feRepo->get_token($user->getObligadoTributario()->atv_user, $user->getObligadoTributario()->atv_password));
    // }


    public function recepcion(Invoice $invoice)
    {

        $config = $invoice->obligadoTributario;
        $clave = $invoice->clave_fe;
        $respHacienda = $this->facturaService->recepcionDocument($clave, $config);

        $this->saveRespuestaXML($config, $clave, $respHacienda);


        if (isset($respHacienda->{'ind-estado'})) {

            if ($invoice->status_fe != 'aceptado') {
                if ($invoice->status_fe != 'rechazado' && $respHacienda->{'ind-estado'} == 'rechazado') {
                    $invoice->rollbackAccumulatedTweak();
                    $invoice->rollbackCommissionTweak();
                }
                $invoice->status_fe = $respHacienda->{'ind-estado'};
            }

            if (isset($respHacienda->{'respuesta-xml'})) {
                $invoice->resp_hacienda = json_encode($this->facturaService->decodeRespuestaXML($respHacienda->{'respuesta-xml'}));
            }

            $invoice->save();
        } else {
            return response(['errors' => $respHacienda], 400);
        }

        return $invoice;
    }

    public function xml(Invoice $invoice)
    {


        $config = $invoice->obligadoTributario;
        $clave = $invoice->clave_fe;
        $respHacienda = $this->facturaService->recepcionDocument($clave, $config);

        $this->saveRespuestaXML($config, $clave, $respHacienda);


        if (!Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $invoice->clave_fe . '.xml')) {
            flash('Archivo no encontrado', 'danger');

            return back();
        }

        return Storage::disk('local')->download('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $invoice->clave_fe . '.xml');

        // return response()->download($pathToFile);
    }

    public function recepcionMensaje(Receptor $receptor)
    {

        $config = $receptor->obligadoTributario;
        $clave = $receptor->Clave . '-' . $receptor->NumeroConsecutivoReceptor;
        $respHacienda = $this->facturaService->recepcionDocument($clave, $config); //$this->feRepo->recepcion($config, $receptor->Clave.'-'. $receptor->NumeroConsecutivoReceptor);
        //dd($respHacienda);

        $this->saveRespuestaXML($config, $clave, $respHacienda);

        if (isset($respHacienda->{'ind-estado'})) {

            if ($receptor->status_fe != 'aceptado') {
                $receptor->status_fe = $respHacienda->{'ind-estado'};
            }

            if (isset($respHacienda->{'respuesta-xml'})) {
                $receptor->resp_hacienda = json_encode($this->facturaService->decodeRespuestaXML($respHacienda->{'respuesta-xml'}));
            }

            $receptor->save();
        } else {
            return response(['errors' => $respHacienda], 400);
        }

        return $receptor;
    }
    
    public function xmlMensaje(Receptor $receptor)
    {


        $config = $receptor->obligadoTributario;

        // $respHacienda = $this->feRepo->recepcion($config, $invoice->clave_fe);

        // Storage::disk('local')->put('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $invoice->clave_fe . '.xml', base64_decode($respHacienda->{'respuesta-xml'}));


        if (!Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $receptor->Clave . '-' . $receptor->NumeroConsecutivoReceptor . '.xml')) {
            flash('Archivo no encontrado', 'danger');

            return back();
        }

        return Storage::disk('local')->download('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $receptor->Clave . '-' . $receptor->NumeroConsecutivoReceptor . '.xml');

       // return response()->download($pathToFile);
    }

    public function recepcionClave(User $user, $clave)
    {

        $config = $user->getObligadoTributario();

        $respHacienda = $this->facturaService->recepcionDocument($clave);

        return json_encode($this->facturaService->decodeRespuestaXML($respHacienda->{'respuesta-xml'}));
       
    }

    // public function comprobante(User $user, $clave)
    // {
    //     $user = ($user) ? $user : auth()->id();

    //     $result = $this->feRepo->comprobante($user->getObligadoTributario(), $clave);

    //     return $result;
    // }

    public function haciendaResponse()
    {
        $resp = request()->all();
        
        \Log::info('results of Hacienda: Clave: ' . $resp['clave'].', fecha: ' . $resp['fecha']. ', estado: ' . $resp['ind-estado']);

        $invoice = Invoice::where('clave_fe', $resp['clave'])->first();



        if (!$invoice) {
            return false;
        }

        if ($invoice->status_fe != 'rechazado' &&  $resp['ind-estado'] == 'rechazado') {
            $invoice->rollbackAccumulatedTweak();
            $invoice->rollbackCommissionTweak();
        }

        $invoice->status_fe = $resp['ind-estado'];
        $invoice->sent_to_hacienda = 1;
        $invoice->save();

        if ($resp['ind-estado'] != 'aceptado') {

            $user = User::find($invoice->created_by);
            
            if($user){
                $user->notify(new HaciendaNotification($invoice, $resp['ind-estado']));
            }
            
        }

        if($resp['ind-estado'] == 'aceptado'){

           $this->sendEmailReceptor($invoice);

        }
    }

    public function haciendaMensajeResponse()
    {
        $resp = request()->all();

        \Log::info('results of Hacienda: ' . json_encode($resp));

        $receptor = Receptor::where('Clave_Mensaje', $resp['clave'])->first();



        if (!$receptor) {
            return false;
        }

        $receptor->status_fe = $resp['ind-estado'];
        $receptor->sent_to_hacienda = 1;
        $receptor->save();

        // if ($resp['ind-estado'] != 'aceptado') {

        //     $user = User::find($receptor->created_by);

        //     if ($user) {
        //         $user->notify(new HaciendaNotification($receptor, $resp['ind-estado']));
        //     }
            

        // }
    }

    public function saveRespuestaXML($config, $clave, $respHacienda)
    {
        if (isset($respHacienda->{'respuesta-xml'})) {
            Storage::disk('local')->put('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $clave . '.xml', base64_decode($respHacienda->{'respuesta-xml'}));
        }
    }

    public function sendEmailReceptor(Invoice $invoice)
    {
        if(!$invoice->email){ return false; }

        $formatLetras = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $TotalEnLetras = $formatLetras->format($invoice->TotalComprobante);

        $data = [
            'invoice' => $invoice->load('lines.taxes', 'clinic', 'customer', 'user'),
            'TotalEnLetras' => $TotalEnLetras

        ];

        $pdf = \PDF::loadView('invoices.pdf', $data);
        //$pdf = \PDF::loadView('invoices.pdf', $invoice->toArray());

        $config = $invoice->obligadoTributario;
        $clave = $invoice->clave_fe;
        $respHacienda = $this->facturaService->recepcionDocument($clave, $config);

        $this->saveRespuestaXML($config, $clave, $respHacienda);

        if (!Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/gpsm_' . $clave . '_signed.xml')) {
            //flash('Archivo XML no encontrado', 'danger');

            if (request()->wantsJson()) {
                return response('Archivo XML no encontrado', 404);
            }
        }

        $xmlFactura = Storage::disk('local')->get('facturaelectronica/' . $config->id . '/gpsm_' . $clave . '_signed.xml');

        $xmlRespuestaHaciendaFactura = null;

        if (Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $clave . '.xml')) {

            $xmlRespuestaHaciendaFactura = Storage::disk('local')->get('facturaelectronica/' . $config->id . '/gpsm_mensaje_hacienda_' . $clave . '.xml');
        }


        try {
            \Mail::to($invoice->email)->send(new SendInvoice($pdf->output(), $xmlFactura, $xmlRespuestaHaciendaFactura, $invoice));

            if (request()->wantsJson()) {
                return response([], 200);
            }

        } catch (TransportExceptionInterface $e) {  //Swift_RfcComplianceException
            if (request()->wantsJson()) {
                return response(['error'], 500);
            }
        }
    }

    
}
